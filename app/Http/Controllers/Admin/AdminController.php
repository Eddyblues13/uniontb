<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\User;
use App\Models\Trade;
use App\Models\Profit;
use App\Models\Deposit;
use App\Mail\DebitEmail;
use App\Models\Document;
use App\Models\Earnings;
use App\Models\Referral;
use App\Mail\CreditEmail;
use App\Models\Withdrawal;
use App\Mail\sendUserEmail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ChequeDeposit;
use App\Models\AccountBalance;
use App\Models\InvestmentPlan;
use App\Models\SavingsBalance;
use Illuminate\Support\Carbon;
use App\Models\CheckingBalance;
use App\Models\TransferHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    /**
     * Display the admin dashboard with a list of all users.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $data['users'] = User::get();
        return view('admin.home', $data);
    }

    public function manageUsersPage(Request $request)
    {

        $data['users'] = User::query()
            ->when($request->search, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', $request->sort ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();
        return view('admin.manage_users', $data);
    }



    public function manageCheck()
    {
        $data['users'] = User::get();
        return view('admin.manage_check', $data);
    }

    public function manageTransactionsPage()
    {

        $data['transactions'] = User::join('transactions', 'users.id', '=', 'transactions.user_id')
            ->get(['users.email', 'users.first_name', 'users.last_name', 'transactions.*']);

        return view('admin.manage_transactions', $data);
    }

    // Method to delete a transaction
    public function deleteTransaction($id)
    {
        // Fetch the transaction by ID
        $transaction = Transaction::findOrFail($id);

        // Delete the transaction
        $transaction->delete();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }

    // Method to approve a transaction
    public function approveTransaction($id)
    {
        // Fetch the transaction by ID
        $transaction = Transaction::findOrFail($id);

        // Check if the transaction is already approved
        if ($transaction->transaction_status == 1) {
            return redirect()->back()->with('info', 'Transaction is already approved.');
        }

        // Approve the transaction by setting the status to 1 (Processed)
        $transaction->transaction_status = 1;
        $transaction->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Transaction approved successfully.');
    }




    public function manageWithdrawalsPage()
    {

        $data['withdrawals'] = User::join('withdrawals', 'users.id', '=', 'withdrawals.user_id')
            ->get(['users.email', 'users.first_name', 'users.last_name', 'withdrawals.*']);

        return view('admin.manage_withdrawal', $data);
    }


    public function viewDeposit($id)
    {

        $data['proof']  = Deposit::findOrFail($id);

        return view('admin.proof', $data);
    }




    public function manageKycPage()
    {
        // Retrieve only users with KYC details (id_card_path and passport_photo_path are not null)
        $data['kyc'] = User::whereNotNull('card')
            ->whereNotNull('pass')
            ->get();

        return view('admin.kyc', $data);
    }



    public function acceptKyc($id)
    {

        $user  = User::where('id', $id)->first();
        $user->kyc_status = 1;
        $user->save();
        return back()->with('message', 'Kyc Approved Successfully');
    }


    public function rejectKyc($id)
    {

        $user  = User::where('id', $id)->first();
        $user->kyc_status = 0;
        $user->save();
        return back()->with('message', 'Kyc Rejected Successfully');;
    }


    public function resetUserPassword($user_id)
    {

        $user = User::findOrFail($user_id);


        $user->update([
            'password' => Hash::make('user01236'),
        ]);

        return back()->with('message', 'Password has been reset successfully.');
    }


    public function clearAccount($id)
    {
        $user = User::find($id);
        if ($user) {

            // Delete related records (posts, comments, likes) associated with the user
            $user->profits()->delete();
            $user->deposits()->delete();
            $user->transactions()->delete();
            $user->earnings()->delete();
            $user->withdrawals()->delete();

            return back()->with('message', 'Records deleted successfully');
        } else {
            return back()->with('message', 'User Not Found');
        }
    }



    public function editUser(Request $request, User $user)
    {

        //$user = User::findOrFail($user_id);


        $request->validate([
            'username' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'country' => 'required',


        ]);

        $user->update([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
        ]);

        return back()->with('message', 'user updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->delete();
            return redirect()->route('manage.users.page')->with('message', 'User deleted successfully');
        }

        return redirect()->route('manage.users.page')->with('error', 'User not found');
    }


    public function newUser(Request $request)
    {

        $user = new User;
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->email = $request['email'];
        $user->account_type = "Joint Account";
        $user->password = Hash::make($request['password']);
        $user->save();

        return back()->with('message', 'New User Created  Successfully');
    }







    /**
     * Display the user profile.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function viewUser($id)
    {
        $data['user'] = User::where('id', $id)
            ->first();

        if (!$data['user']) {
            abort(404, 'User not found');
        }



        $data['savings_balance'] = SavingsBalance::where('user_id', $id)->sum('amount');
        $data['checking_balance'] = CheckingBalance::where('user_id', $id)->sum('amount');

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

        return view('admin.user_data', $data);
    }





    public function creditUserPage($id)
    {
        $user = User::find($id);

        $data['user'] = $user;

        // Sum of successful account balance
        $data['balance_sum'] = AccountBalance::where('user_id',  $user->id)
            ->sum('amount');

        // Sum of successful account balance
        $data['profit_sum'] = Profit::where('user_id', $user)
            ->sum('amount');

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('admin.credit_user', $data);
    }

    /**
     * Open a new account.
     *
     * @return \Illuminate\View\View
     */
    public function openAccount()
    {
        // Display form for opening a new account
        return view('admin.open_account');
    }


    /**
     * Open a new account.
     *
     * @return \Illuminate\View\View
     */
    public function sendEmailPage()
    {
        // Display form for opening a new account
        return view('admin.send_mail_form');
    }

    public function sendEmail(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $email = $request->input('email');
        $subject = $request->input('subject');
        $messageBody = $request->input('message');

        try {
            Mail::send([], [], function ($message) use ($email, $subject, $messageBody) {
                $message->to($email)
                    ->subject($subject)
                    ->setBody($messageBody, 'text/html');
            });

            return response()->json(['success' => 'Email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email. Please try again.']);
        }
    }




    public function suspendAccount(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            // Logic to suspend the user account
            $user->account_suspended = 1;
            $user->save();

            return response()->json(['message' => 'Account suspended successfully.']);
        }

        return response()->json(['message' => 'User not found.'], 404);
    }

    public function unblockAccount(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            // Logic to unblock the user account
            $user->account_suspended = 0;
            $user->save();

            return response()->json(['message' => 'Account unblocked successfully.']);
        }

        return response()->json(['message' => 'User not found.'], 404);
    }
    /**
     * Update user details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserDetail(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->first_name = $request->input('firstname');
            $user->last_name = $request->input('lastname');
            $user->phone = $request->input('phone');
            $user->email = $request->input('email');
            $user->dob = $request->input('dob');
            $user->address = $request->input('addressB');
            $user->save();

            return response()->json(['success' => 'User details updated successfully.']);
        }

        return response()->json(['error' => 'User not found.'], 404);
    }

    /**
     * Update bank details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBankDetail(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->account_type = $request->input('accounttype');
            $user->account_number = $request->input('accountnumber');
            $user->currency = $request->input('usercurrency');
            $user->imf_code = $request->input('imf');
            $user->cot_code = $request->input('cot');
            $user->daily_limit = $request->input('daily_limit');
            $user->secret_code = $request->input('secretCode');
            $user->save();

            return response()->json(['success' => 'Bank details updated successfully.']);
        }

        return response()->json(['error' => 'User not found.'], 404);
    }

    /**
     * Fund a user account.
     *
     * @param string $accountnumber
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function fundUser($accountnumber, $id)
    {
        // Implement logic to fund user account
        return response()->view('admin.fund_user', compact('accountnumber', 'id'));
    }

    /**
     * View user transaction history.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function userTransaction($id)
    {
        // Implement logic to view user transactions
        return response()->view('admin.user_transaction', compact('id'));
    }

    /**
     * Track user transfers.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function userTransferTracking($id)
    {
        // Implement logic to track user transfers
        return response()->view('admin.user_transfer_tracking', compact('id'));
    }

    /**
     * Debit a user account.
     *
     * @param string $accountnumber
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function debitUser($accountnumber, $id)
    {
        // Implement logic to debit user account
        return response()->view('admin.debit_user', compact('accountnumber', 'id'));
    }

    /**
     * Update user profile photo.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto($id)
    {
        // Implement logic to update user profile photo
        return response()->view('admin.update_photo', compact('id'));
    }

    /**
     * View user activity.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function userActivity($id)
    {
        // Implement logic to view user activity
        return response()->view('admin.user_activity', compact('id'));
    }

    /**
     * Reset user password.
     *
     * @param int $userid
     * @return \Illuminate\Http\Response
     */
    public function userPasswordReset($userid)
    {
        // Implement logic to reset user password
        return response()->view('admin.user_password_reset', compact('userid'));
    }












    // Method to show the profile update form
    public function editProfile()
    {
        // Retrieve the authenticated admin using the 'admin' guard
        $admin = Auth::guard('admin')->user();
        return view('admin.admin_profile', compact('admin')); // Profile Blade file
    }

    // Method to handle the profile update
    public function updateProfile(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Update the profile of the authenticated admin
        $admin = Auth::guard('admin')->user();
        $admin->name = $request->firstname;
        // $admin->middlename = $request->middlename;
        // $admin->lastname = $request->lastname;
        // $admin->phone = $request->phone;
        $admin->email = $request->email;
        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully!'
        ]);
    }

    // Method to handle password update
    public function updatePassword(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Retrieve the authenticated admin
        $admin = Auth::guard('admin')->user();

        // Check if the old password matches
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Old password is incorrect.'
            ], 422);
        }

        // Update the new password
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully!'
        ]);
    }



    public function showResetPasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.admin_change_user_password', compact('user'));
    }


    public function resetPassword(Request $request)
    {
        // Validate input
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'id' => 'required|exists:users,id',
        ]);

        // Find user by ID
        $user = User::findOrFail($request->id);

        // Update user password
        $user->password = Hash::make($request->password);
        $user->save();

        // Return success message
        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }

    public function impersonate(User $user)
    {
        // Store the original user's ID in the session (if not already stored)
        if (!session()->has('impersonate')) {
            session()->put('impersonate', Auth::id());
        }

        // Impersonate the specified user
        Auth::loginUsingId($user->id);

        $data['credit_withdrawal'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Withdrawal')->where('transaction', 'credit')->sum('credit');
        $data['debit_withdrawal'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Withdrawal')->where('transaction', 'debit')->sum('debit');
        $data['withdrawal_balance'] = $data['debit_withdrawal'];

        $data['credit_deposit'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Deposit')->where('transaction', 'credit')->sum('credit');
        $data['debit_deposit'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Deposit')->where('transaction', 'debit')->sum('debit');
        $data['deposit_balance'] = $data['credit_deposit'] - $data['debit_deposit'];

        $data['credit_profit'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Profit')->where('transaction', 'credit')->sum('credit');
        $data['debit_profit'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Profit')->where('transaction', 'debit')->sum('debit');
        $data['profit_balance'] = $data['credit_profit'] - $data['debit_profit'];

        $data['credit_earning'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Earning')->where('transaction', 'credit')->sum('credit');
        $data['debit_earning'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Earning')->where('transaction', 'debit')->sum('debit');
        $data['earning_balance'] = $data['credit_earning'] - $data['debit_earning'];





        $data['credit_Investment'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Investment')->where('transaction', 'credit')->sum('credit');
        $data['debit_Investment'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Investment')->where('transaction', 'debit')->sum('debit');
        $data['Investment_balance'] = $data['credit_Investment'] - $data['debit_Investment'];

        $data['credit_referral'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Referral')->where('transaction', 'credit')->sum('credit');
        $data['debit_referral'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction_type', 'Referral')->where('transaction', 'debit')->sum('debit');
        $data['referral_balance'] = $data['credit_referral'] - $data['debit_referral'];


        $data['credit_balance'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction', 'credit')->sum('credit');
        $data['debit_balance'] = Transaction::where('user_id', $user->id)->where('status', '1')->where('transaction', 'debit')->sum('debit');
        $data['total_balance'] = $data['credit_balance'] - $data['debit_balance'];


        // Redirect to the user's home page with the relevant data
        return view('dashboard.home', $data)->with('success', 'You are logged in as ' . $user->name);
    }


    public function leaveImpersonate()
    {
        // Check if the session has an 'impersonate' value
        if (session()->has('impersonate')) {
            // Retrieve the original user's ID from the session
            $originalUserId = session()->get('impersonate');

            // Log in as the original user
            Auth::loginUsingId($originalUserId);

            // Forget the impersonation session data
            session()->forget('impersonate');

            $data['users'] = User::get();


            // Sum of pending deposits
            $data['pending_deposits_sum'] = Deposit::where('status', '0')->sum('amount');

            // Sum of successful deposits
            $data['total_deposits'] = Deposit::sum('amount');

            // Sum of pending withdrawals
            $data['pending_withdrawals_sum'] = Withdrawal::where('status', '0')->sum('amount');

            // Sum of successful withdrawals
            $data['total_withdrawals'] = Withdrawal::sum('amount');

            // sum total users
            $data['total_users'] = User::count();

            // sum total users
            // $data['suspended_users'] = User::where('account_suspended', '1')->count();

            $data['suspended_users'] = User::count();
            // Redirect to the original user's dashboard or home page
            return redirect()->route('admin.home', $data)->with('message', 'You have returned to your original account.');
        }

        // If no impersonation is happening, redirect to home
        return redirect()->route('admin.home')->with('message', 'No impersonation found.');
    }



    public function userVerification($id)
    {
        $user_data = DB::table('users')->where('id', $id)->first();
        // $full_name = $user_data->first_name;
        // $email =   $user_data->email;
        // $user = [

        //     'full_name' => $full_name,
        // ];

        // // Mail::to($email)->send(new activateAccountEmail($user));
        $status = array();
        $status['user_status'] = 1;
        $update = DB::table('users')->where('id', $id)->update($status);
        return redirect()->back()->with('message', 'Successful!! User Has Been Verified, they can now login thier account');
    }

    public function userSuspension($id)
    {

        $status = array();
        $status['user_status'] = 0;
        $update = DB::table('users')->where('id', $id)->update($status);
        return redirect()->back()->with('message', 'User Has Been Suspended Successfully');
    }


    public function credit(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'nullable|string',
            't_type' => 'required|string'
        ]);

        // Generate a unique transaction ID and reference
        $transactionId = strtoupper(uniqid('TXN_'));
        $transactionRef = strtoupper(uniqid('REF_'));

        // Create the transaction record
        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'transaction_id' => $transactionId,
            'transaction_ref' => $transactionRef,
            'transaction_type' => 'Credit', // From "Transfer Scope" dropdown
            'transaction' => 'credit', // Since this is a credit transaction
            'transaction_amount' => $request->merge([
                'amount' => str_replace(',', '', $request->input('amount'))
            ]), // Amount to be credited
            'transaction_description' => $request->description, // Optional description
            'transaction_status' => '1', // Default status can be 'pending', adjust as needed
            'wallet_address' => null, // If wallet transfers are applicable, you can fill this
            'wallet_type' => null, // Can be filled if relevant to your setup
            'account_name' => null, // If related to bank transfers
            'account_number' => null, // If related to bank transfers
            'account_type' => null, // If related to bank transfers
            'bank_name' => null, // If related to bank transfers
            'routing_number' => null, // If related to bank transfers
        ]);



        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $balance =  $request['balance'] + $request['amount'];
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];

        $data = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'balance' => $balance,
            'currency' => $currency,
            'ref' => $transactionRef,
        ];



        // Optional: Send email notification if requested
        if ($request->t_type == 'yes') {
            $user = User::findOrFail($request->user_id);
            // Send email notification (assuming a mailable is set up)
            Mail::to($email)->send(new CreditEmail($data));
        }

        // Redirect or return response after successful credit
        return redirect()->back()->with('message', 'Transaction created successfully and credit applied.');
    }

    public function debit(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|integer',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'nullable|string',
            't_type' => 'required|string'
        ]);

        // Generate a unique transaction ID and reference
        $transactionId = strtoupper(uniqid('TXN_'));
        $transactionRef = strtoupper(uniqid('REF_'));

        // Create the transaction record
        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'transaction_id' => $transactionId,
            'transaction_ref' => $transactionRef,
            'transaction_type' => 'Debit', // From "Transfer Scope" dropdown
            'transaction' => 'debit', // Since this is a credit transaction
            'transaction_amount' => $request->merge([
                'amount' => str_replace(',', '', $request->input('amount'))
            ]), // Amount to be credited
            'transaction_description' => $request->description, // Optional description
            'transaction_status' => '1', // Default status can be 'pending', adjust as needed
            'wallet_address' => null, // If wallet transfers are applicable, you can fill this
            'wallet_type' => null, // Can be filled if relevant to your setup
            'account_name' => null, // If related to bank transfers
            'account_number' => null, // If related to bank transfers
            'account_type' => null, // If related to bank transfers
            'bank_name' => null, // If related to bank transfers
            'routing_number' => null, // If related to bank transfers
        ]);



        $full_name = $request['name'];
        $email =  $request['email'];
        $amount = $request->input('amount');
        $date = Carbon::now();
        $balance =  $request['balance'] - $request['amount'];
        $description =  $request['description'];
        $a_number =  $request['a_number'];
        $currency =  $request['currency'];

        $data = [

            'account_number' => $a_number,
            'account_name' => $full_name,
            'full_name' => $full_name,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'balance' => $balance,
            'currency' => $currency,
            'ref' => $transactionRef,
        ];



        // Optional: Send email notification if requested
        if ($request->t_type == 'yes') {
            $user = User::findOrFail($request->user_id);
            // Send email notification (assuming a mailable is set up)
            Mail::to($email)->send(new DebitEmail($data));
        }

        // Redirect or return response after successful credit
        return redirect()->back()->with('message', 'Transaction created successfully and debit applied.');
    }

    public function vatCode(Request $request)
    {
        $users = array();
        $users['first_code'] = $request->input('vat_code');;
        $update = DB::table('users')->update($users);
        return back()->with('message', 'VAT Code updated, successfully');
    }


    public function adminUpdateUser(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->{$request->field} = $request->value;
        $user->save();

        return response()->json(['message' => true, 'message' => 'User updated successfully']);
    }

    public function toggleAccountStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->user_status = $user->user_status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->user_status
        ]);
    }

    public function toggleEmailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->email_status = $user->email_status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->email_status
        ]);
    }

    public function toggleUserStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->user_status = $user->user_status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->email_status
        ]);
    }

    public function getUsers(Request $request)
    {
        $validated = $request->validate([
            'num' => 'sometimes|integer|min:1|max:100',
            'search' => 'nullable|string|min:3|max:50',
            'order' => 'sometimes|in:name,email,created_at'
        ]);

        $users = User::query()
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })
            ->orderBy($validated['order'] ?? 'created_at')
            ->take($validated['num'] ?? 10)
            ->get();

        $status = 200;
        if ($request->has('search') && empty($validated['search']) && !empty($request->search)) {
            $status = 201; // Invalid search length
        }

        return response()->json([
            'status' => $status,
            'data' => view('admin.users.partials.table_rows', compact('users'))->render()
        ]);
    }

    // In your controller
    public function loanHistory(Request $request)
    {
        $loans = Loan::with('user')
            ->when($request->search, function ($query) use ($request) {
                return $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', $request->sort ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return view('admin.loan', compact('loans'));
    }

    public function transferHistory(Request $request)
    {
        $transfers = TransferHistory::with(['user'])
            ->when($request->search, function ($query) use ($request) {
                return $query->where('reference', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            })
            ->orderBy('completed_at', $request->sort ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return view('admin.transfer_history', compact('transfers'));
    }

    public function chequeHistory(Request $request)
    {
        $chequeDeposits = ChequeDeposit::with(['user'])
            ->when($request->search, function ($query) use ($request) {
                return $query->where('remarks', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            })
            ->orderBy('created_at', $request->sort ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return view('admin.cheque_history', compact('chequeDeposits'));
    }

    public function showChequeHistory(ChequeDeposit $chequeDeposit)
    {
        return view('admin.cheque_history_show', compact('chequeDeposit'));
    }

    public function sendUserEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $email = $request->email;
        $subject = $request->subject;
        $messageBody = $request->message;

        Mail::to($email)->send(new sendUserEmail($subject, $messageBody));

        return back()->with('message', 'Email sent successfully!');
    }
}
