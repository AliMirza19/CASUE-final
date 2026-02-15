<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the password change form.
     */
    public function showChangeForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.change-password');
    }

    /**
     * Handle password change request.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', Password::min(6)->max(30)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Check if new password is different from current
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from your current password.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'password_changed' => true,
        ]);

        // Log the activity
        ActivityLog::logActivity($user, 'Password changed successfully');

        // Redirect to appropriate dashboard
        return $this->redirectToDashboard($user)
            ->with('success', 'Password changed successfully! Welcome to the system.');
    }

    /**
     * Redirect user to appropriate dashboard based on role.
     */
    private function redirectToDashboard($user)
    {
        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'hod' => 'hod.dashboard',
            'patron' => 'patron.dashboard',
            'president' => 'president.dashboard',
            'student' => 'student.dashboard',
            'sa' => 'sa.dashboard',
            'vc' => 'vc.dashboard',
            'gd' => 'gd.dashboard',
        ];

        $route = $dashboardRoutes[$user->role] ?? 'student.dashboard';
        
        return redirect()->route($route);
    }
}