<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SavingsBalance;
use App\Models\CheckingBalance;
use App\Models\TransferHistory;
use Illuminate\Support\Facades\DB;
use App\Models\WireTransferHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{
    public function showForm($type)
    {


        $validTypes = ['wire', 'local', 'internal', 'paypal', 'crypto', 'skrill'];

        if (!in_array($type, $validTypes)) {
            abort(404);
        }



        $user = Auth::user();
        $data['user'] = Auth::user();
        $data['savings_balance'] = SavingsBalance::where('user_id', $user->id)->sum('amount');
        $data['checking_balance'] = CheckingBalance::where('user_id', $user->id)->sum('amount');

        $data['currentMonth'] = Carbon::now()->format('M Y'); // Example: "Feb 2025"

        $data['totalSavingsCredit'] = SavingsBalance::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'credit')
            ->sum('amount');

        $data['totalSavingsDebit'] = SavingsBalance::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'debit')
            ->sum('amount');

        $data['totalCheckingCredit'] = CheckingBalance::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'credit')
            ->sum('amount');



        $data['totalCheckingDebit'] = CheckingBalance::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'debit')
            ->sum('amount');



        return view("user.transfer.{$type}",  $data);
    }

    public function processTransfer(Request $request)
    {
        $transferType = $request->type;
        $validationRules = $this->getValidationRules($transferType);

        $validated = $request->validate($validationRules);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $account = $validated['account'];
            $amount = $validated['amount'];

            // Fetch current balance
            $balance = ($account === 'savings')
                ? SavingsBalance::where('user_id', $user->id)->sum('amount')
                : CheckingBalance::where('user_id', $user->id)->sum('amount');

            if ($amount > $balance) {
                throw ValidationException::withMessages(['amount' => 'Insufficient funds in the selected account.']);
            }

            // Store data in session for tax confirmation
            Session::put('transfer_data', [
                'type' => $transferType,
                'validated' => $validated,
                'details' => $this->getTransferDetails($transferType, $validated)
            ]);

            DB::commit();

            $user = Auth::user();
            $data['savings_balance'] = SavingsBalance::where('user_id', $user->id)->sum('amount');
            $data['checking_balance'] = CheckingBalance::where('user_id', $user->id)->sum('amount');

            $data['currentMonth'] = Carbon::now()->format('M Y'); // Example: "Feb 2025"

            $data['totalSavingsCredit'] = SavingsBalance::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'credit')
                ->sum('amount');

            $data['totalSavingsDebit'] = SavingsBalance::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'debit')
                ->sum('amount');



            $data['totalCheckingCredit'] = CheckingBalance::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'credit')
                ->sum('amount');


            $data['totalCheckingDebit'] = CheckingBalance::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('type', 'debit')
                ->sum('amount');

            // Redirect to tax confirmation form
            //return view('user.transfer.tax-form', compact('transferType', 'amount'), $data);
            // Redirect to tax confirmation form
            return redirect()->route('transfer.confirmTax', [
                'transferType' => $transferType,
                'amount' => $amount
            ])->with($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }




    public function confirmTax(Request $request)
    {
        $transferData = session('transfer_data');

        if (!$transferData) {
            return redirect()->route('home')->with('error', 'Session expired, please start over.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'tax_code' => 'required|string|max:20',
            ]);

            $user = Auth::user();
            // Verify tax code (replace with your validation logic)
            if ($request->tax_code !==  $user->code_one) {
                return back()->with('error', 'Invalid Tax Code. Please try again.');
            }

            // Merge tax code with transfer data
            $transferData['tax_code'] = $request->tax_code;
            // Extract user and account details
            $user = Auth::user();
            $account = $transferData['validated']['account'];
            $amount = $transferData['validated']['amount'];

            // Deduct amount from selected account
            if ($account === 'savings') {
                SavingsBalance::where('user_id', $user->id)->decrement('amount', $amount);
            } else {
                CheckingBalance::where('user_id', $user->id)->decrement('amount', $amount);
            }

            $reference = $this->generateReference();
            // Store transaction in wire transfer history
            TransferHistory::create([
                'reference' => $reference,
                'user_id' => $user->id,
                'type' => $transferData['type'],
                'amount' => $amount,
                'currency' => $user->currency,
                'from_account' => $account,
                'details' => json_encode(array_merge($transferData['details'], ['tax_code' => $request->tax_code])),
                'status' => 'completed'
            ]);

            session()->forget('transfer_data');
            // Redirect to receipt route
            // return redirect()->route('transfer.receipt')->with('transferData', $transferData);


            // return redirect()->route('home')->with('success', 'Transfer completed successfully.');

            // Store receipt data in session temporarily
            $receiptData = [
                'reference' => $reference,
                'date' => now()->format('Y-m-d H:i:s'),
                'amount' => $amount,
                'currency' => $user->currency,
                'account_type' => $account,
                'recipient' => $transferData['details']['recipient_name'] ?? '',
                'recipient_account' => $transferData['details']['recipient_account'] ?? '',
                'bank_name' => $transferData['details']['bank_name'] ?? '',
                'tax_code' => $request->tax_code,
                'user' => $user
            ];

            session()->flash('receipt_data', $receiptData);

            return redirect()->route('transfer.receipt');
        }

        $user = Auth::user();
        $data['savings_balance'] = SavingsBalance::where('user_id', $user->id)->sum('amount');
        $data['checking_balance'] = CheckingBalance::where('user_id', $user->id)->sum('amount');

        $data['currentMonth'] = Carbon::now()->format('M Y'); // Example: "Feb 2025"

        $data['totalSavingsCredit'] = SavingsBalance::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'credit')
            ->sum('amount');

        $data['totalSavingsDebit'] = SavingsBalance::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'debit')
            ->sum('amount');



        $data['totalCheckingCredit'] = CheckingBalance::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'credit')
            ->sum('amount');


        $data['totalCheckingDebit'] = CheckingBalance::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', 'debit')
            ->sum('amount');

        // Show the tax code form with retained data
        return view('user.transfer.tax-form', compact('transferData'), $data);
    }

    private function generateReference()
    {
        return 'TX-' . time() . '-' . Str::upper(Str::random(6));
    }


    public function showReceipt()
    {
        if (!session()->has('receipt_data')) {
            return redirect()->route('home')->with('error', 'No receipt data found.');
        }

        $receiptData = session('receipt_data');
        return view('user.transfer.receipt', compact('receiptData'));
    }

    private function getValidationRules($type)
    {
        $baseRules = [
            'type' => 'required|in:wire,local,interbank,paypal,crypto,skrill',
            'account' => 'required|in:savings,checking',
            'amount' => 'required|numeric|min:0.01',
            //'pin' => 'required|digits:4'
        ];

        $typeRules = [
            'wire' => [
                'name' => 'required|string|max:255',
                'acct' => 'required|regex:/^[A-Za-z0-9]+$/',
                'bank' => 'required|string|max:255',
                'swift' => 'nullable|string',
                'routing' => 'required|numeric',
                'address' => 'nullable|string|max:500',
                'remarks' => 'nullable|string|max:255'
            ],
            'local' => [
                'name' => 'required|string|max:255',
                'acct' => 'required|regex:/^[A-Za-z0-9]+$/',
                'bank' => 'required|string|max:255',
                'remarks' => 'nullable|string|max:255'
            ],
            'internal' => [
                'name' => 'required|string|max:255',
                'acct' => 'required|regex:/^[A-Za-z0-9]+$/',
                'bank' => 'required|string|max:255',
                'routing' => 'required|numeric',
                'remarks' => 'nullable|string|max:255'
            ],
            'paypal' => [
                'email' => 'required|email',
                'remarks' => 'nullable|string|max:255'
            ],
            'crypto' => [
                'wallet_address' => 'required|string|max:255',
                'crypto_type' => 'required|string|in:bitcoin,ethereum,usdt',
                'remarks' => 'nullable|string|max:255'
            ],
            'skrill' => [
                'email' => 'required|email',
                'remarks' => 'nullable|string|max:255'
            ]
        ];

        return array_merge($baseRules, $typeRules[$type] ?? []);
    }

    private function getTransferDetails($type, $validated)
    {
        $typeDetails = [
            'wire' => [
                'name' => $validated['name'] ?? null,
                'acct' => $validated['acct'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'swift' => $validated['swift'] ?? null,
                'routing' => $validated['routing'] ?? null,
                'address' => $validated['address'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ],
            'local' => [
                'name' => $validated['name'] ?? null,
                'acct' => $validated['acct'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ],
            'interbank' => [
                'name' => $validated['name'] ?? null,
                'acct' => $validated['acct'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'routing' => $validated['routing'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ],
            'paypal' => [
                'email' => $validated['email'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ],
            'crypto' => [
                'wallet_address' => $validated['wallet_address'] ?? null,
                'crypto_type' => $validated['crypto_type'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ],
            'skrill' => [
                'email' => $validated['email'] ?? null,
                'remarks' => $validated['remarks'] ?? null
            ]
        ];

        return $typeDetails[$type] ?? [];
    }


    public function success()
    {
        return redirect()->route('home')->with('success', 'Transfer completed successfully.');
    }
}
