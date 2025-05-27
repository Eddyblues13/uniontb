<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SavingsBalance;
use App\Models\CheckingBalance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionNotificationMail;

class TransactionController extends Controller
{
    public function creditSavings(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $description = $request->description;

        SavingsBalance::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'status' => 'active',
            'description' => $description,
        ]);

        Mail::to($user->email)->send(new TransactionNotificationMail($user, $amount, 'Savings Balance', 'Credit', $description));

        return view('admin.receipt', [
            'user' => $user,
            'amount' => $amount,
            'type' => 'Credit',
            'balanceType' => 'Savings Balance',
            'description' => $description,
        ]);
    }

    public function debitSavings(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $description = $request->description;

        SavingsBalance::create([
            'user_id' => $user->id,
            'amount' => -$amount,
            'type' => 'debit',
            'status' => 'active',
            'description' => $description,
        ]);

        Mail::to($user->email)->send(new TransactionNotificationMail($user, $amount, 'Savings Balance', 'Debit', $description));

        return view('admin.receipt', [
            'user' => $user,
            'amount' => $amount,
            'type' => 'Debit',
            'balanceType' => 'Savings Balance',
            'description' => $description,
        ]);
    }

    public function creditChecking(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $description = $request->description;

        CheckingBalance::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'status' => 'active',
            'description' => $description,
        ]);

        Mail::to($user->email)->send(new TransactionNotificationMail($user, $amount, 'Checking Balance', 'Credit', $description));

        return view('admin.receipt', [
            'user' => $user,
            'amount' => $amount,
            'type' => 'Credit',
            'balanceType' => 'Checking Balance',
            'description' => $description,
        ]);
    }

    public function debitChecking(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $description = $request->description;

        CheckingBalance::create([
            'user_id' => $user->id,
            'amount' => -$amount,
            'type' => 'debit',
            'status' => 'active',
            'description' => $description,
        ]);

        Mail::to($user->email)->send(new TransactionNotificationMail($user, $amount, 'Checking Balance', 'Debit', $description));

        return view('admin.receipt', [
            'user' => $user,
            'amount' => $amount,
            'type' => 'Debit',
            'balanceType' => 'Checking Balance',
            'description' => $description,
        ]);
    }
}
