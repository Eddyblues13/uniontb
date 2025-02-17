<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function step1Submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Store the validated data in the session
        $request->session()->put('registration_data', $validated);

        return redirect()->route('register');
    }

    /**
     * Show the registration form (Step 2).
     */
    public function showRegistrationForm()
    {
        $registrationData = session('registration_data');

        if (!$registrationData) {
            return redirect()->route('registration.step1')->with('error', 'Please complete Step 1 first.');
        }

        return view('auth.register', compact('registrationData'));
    }


    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'ssn' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nok_name' => 'nullable|string|max:255',
            'nok_email' => 'nullable|email',
            'nok_phone' => 'nullable|string|max:20',
            'nok_relationship' => 'nullable|string|max:100',
            'nok_address' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
            'password' => 'required|string|min:4|confirmed',
            'pin' => 'required|numeric|digits_between:4,4',
            'passport' => 'nullable|file|mimes:png,jpg,gif|max:5120',
            'kyc' => 'nullable|file|mimes:pdf,png,jpg,gif|max:5120',
        ]);

        // Handle file uploads with unique naming
        $passportPath = $request->hasFile('passport')
            ? $request->file('passport')->storeAs('passports', time() . '_' . $request->file('passport')->getClientOriginalName(), 'public')
            : null;

        $kycPath = $request->hasFile('kyc')
            ? $request->file('kyc')->storeAs('kycs', time() . '_' . $request->file('kyc')->getClientOriginalName(), 'public')
            : null;

        // Generate a unique login ID & account number
        $loginId = rand(1000000000, 9999999999);

        $accountNumber = rand(1000000000, 9999999999);


        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'dob' => $validated['dob'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'ssn' => $validated['ssn'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'country' => $validated['country'] ?? null,
            'city' => $validated['city'] ?? null,
            'zip' => $validated['zip'] ?? null,
            'address' => $validated['address'] ?? null,
            'nok_name' => $validated['nok_name'] ?? null,
            'nok_email' => $validated['nok_email'] ?? null,
            'nok_phone' => $validated['nok_phone'] ?? null,
            'nok_relationship' => $validated['nok_relationship'] ?? null,
            'nok_address' => $validated['nok_address'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'password' => Hash::make($validated['password']),
            'pin' => $validated['pin'], // Securely hash the PIN
            'passport_path' => $passportPath,
            'kyc_path' => $kycPath,
            'login_id' => $loginId,
            'account_number' => $accountNumber,
            'plain' => $validated['password'],
            'verification_code' => rand(1000, 9999),
            'email_status' => 1,
            'user_status' => 1,
            'verification_expiry' => now()->addMinutes(10),
        ]);

        // Send verification email if needed
        //$this->sendVerificationEmail($user);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account created successfully!');
    }




    protected function sendVerificationEmail(User $user)
    {


        $vmessage = "
        <p>Hello {$user->name},</p>
        <p>We are so happy to have you on board. We need to verify your email address.</p>
        <p>Use this code to verify your email: <strong>{$user->verification_code}</strong></p>
        <p>Please note that this code will expire in 10 minutes.</p>
    ";

        Mail::to($user->email)->send(new VerificationEmail($vmessage));
    }


    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('home.login');
    }

    /**
     * Handle user login.
     */


    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if login input is an email or phone number
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login_id';

        // Attempt login with either email or phone
        if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password])) {
            $user = Auth::user();

            // Redirect based on user type
            if ($user->user_type === 'admin') {
                return redirect()->route('admin.home')->with('success', 'Welcome, Admin!');
            }

            return redirect()->route('home')->with('success', 'Logged in successfully!');
        }

        // If login fails, redirect back with an error
        return redirect()->back()->withErrors(['login' => 'Invalid login credentials'])->withInput();
    }



    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('success', 'Password reset link sent to your email.');
    }




    /**
     * Handle user logout.
     */
}
