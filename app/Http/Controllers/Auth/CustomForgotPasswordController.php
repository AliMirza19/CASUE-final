<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomForgotPasswordController extends Controller
{
    // Step 1: Show Identity Verification Form
    public function showVerifyIdentityForm(Request $request)
    {
        $reg_id = $request->query('reg_id', old('reg_id'));
        return view('auth.passwords.verify-identity', compact('reg_id'));
    }

    // Step 1: Process Identity Verification
    public function processVerifyIdentity(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string',
            'cnic' => 'required|string',
        ]);

        $user = User::where('reg_id', $request->reg_id)
                    ->where('cnic', $request->cnic)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Identity verification failed. Records do not match.');
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $email = $user->email;

        // Save/Update OTP
        DB::table('password_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => (string)$otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Send Email
        Mail::to($email)->send(new PasswordResetOtpMail($otp));

        // Store email in session
        $request->session()->put('reset_email', $email);

        // Mask email for message (e.g., a***@example.com)
        $parts = explode("@", $email);
        $name = implode("@", array_slice($parts, 0, count($parts)-1));
        $maskedName = substr($name, 0, 1) . str_repeat('*', max(1, strlen($name) - 1));
        $maskedEmail = $maskedName . "@" . end($parts);

        return redirect()->route('password.otp.form')->with('success', "OTP has been sent to your registered university email ($maskedEmail).");
    }

    // Step 2: Show OTP Form
    public function showOtpForm(Request $request)
    {
        if (!$request->session()->has('reset_email')) {
            return redirect()->route('password.verify.identity')->with('error', 'Session expired. Please verify your identity again.');
        }

        return view('auth.passwords.verify-otp');
    }

    // Step 2: Process OTP
    public function processOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = $request->session()->get('reset_email');
        if (!$email) {
            return redirect()->route('password.verify.identity')->with('error', 'Session expired. Please verify your identity again.');
        }

        $record = DB::table('password_otps')
                    ->where('email', $email)
                    ->first();

        if (!$record || $record->otp !== $request->otp) {
            return back()->with('error', 'Invalid OTP.');
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        // Mark OTP as verified in session
        $request->session()->put('otp_verified', true);

        return redirect()->route('password.reset.form')->with('success', 'OTP verified successfully. You may now choose a new password.');
    }

    // Step 3: Show Reset Password Form
    public function showResetPasswordForm(Request $request)
    {
        if (!$request->session()->has('reset_email') || !$request->session()->get('otp_verified')) {
            return redirect()->route('password.verify.identity')->with('error', 'Unauthorized access. Please verify your identity first.');
        }

        return view('auth.passwords.custom-reset');
    }

    // Step 3: Process Password Reset
    public function processResetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->session()->get('reset_email');
        if (!$email || !$request->session()->get('otp_verified')) {
            return redirect()->route('password.verify.identity')->with('error', 'Unauthorized access. Please verify your identity first.');
        }

        // Update User Password
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->password_changed = true;
            $user->save();
        }

        // Delete OTP record
        DB::table('password_otps')->where('email', $email)->delete();

        // Clear Session
        $request->session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Your password has been successfully reset. You may now login with your new password.');
    }
}
