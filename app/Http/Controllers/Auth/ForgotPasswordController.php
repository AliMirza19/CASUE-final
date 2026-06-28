<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ForgotPasswordController extends Controller
{
    /**
     * Show the identity verification form.
     * Step 1: User must provide Reg ID + CNIC to prove identity.
     */
    public function showVerifyForm()
    {
        return view('auth.verify-identity');
    }

    /**
     * Handle identity verification and send password reset link.
     * Step 2: Match reg_id + cnic against the users table, then
     * programmatically trigger Laravel's PasswordBroker.
     */
    public function verifyIdentity(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string|min:4|max:20',
            'cnic'   => 'required|string|regex:/^\d{5}-\d{7}-\d{1}$/',
        ], [
            'cnic.regex' => 'CNIC must be in the format: 35201-1234567-1',
        ]);

        // Attempt to find the user by both Reg ID and CNIC
        $user = User::where('reg_id', $request->reg_id)
                     ->where('cnic', $request->cnic)
                     ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('error', 'Verification failed. Please check your Reg ID and CNIC.');
        }

        // Check if the user has a registered email
        if (empty($user->email)) {
            return back()
                ->with('error', 'No email address is associated with this account. Please contact the administrator.');
        }

        // Programmatically send the password reset link via PasswordBroker
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()
                ->with('success', 'Verification successful! A password reset link has been sent to your registered university email.');
        }

        // Handle throttle or other errors
        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->with('error', 'Please wait before requesting another reset link. Try again in a few minutes.');
        }

        return back()
            ->with('error', 'Unable to send reset link. Please try again later or contact the administrator.');
    }

    /**
     * Show the password reset form (arrived from email link).
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle the actual password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|max:30|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'         => Hash::make($password),
                    'password_changed' => true,
                ]);

                $user->save();

                event(new PasswordReset($user));

                // Log the activity
                ActivityLog::logActivity($user, 'Password reset via secure identity verification');
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Password reset successfully! You can now login with your new password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', __($status));
    }
}
