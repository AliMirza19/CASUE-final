<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string|min:6|max:12',
            'password' => 'required|string|min:6|max:30',
        ]);

        // Attempt to find user by registration ID
        $user = \App\Models\User::where('reg_id', $request->reg_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'reg_id' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Log the activity
        ActivityLog::logActivity($user, 'User logged in');

        // Regenerate session for security
        $request->session()->regenerate();

        // Check if user needs to change password
        if ($user->needsPasswordChange()) {
            return redirect()->route('password.change')
                ->with('info', 'Welcome! You must change your password before continuing.');
        }

        // Redirect to appropriate dashboard
        return $this->redirectToDashboard($user);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            ActivityLog::logActivity($user, 'User logged out');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to appropriate dashboard based on role.
     */
    private function redirectToDashboard($user)
    {
        // Special handling for faculty users who are appointed as HOD or Patron
        if ($user->role === 'faculty') {
            if ($user->isAppointedHod()) {
                return redirect()->route('hod.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($user->isAppointedPatron()) {
                return redirect()->route('patron.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
            }
        }
        
        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'hod' => 'hod.dashboard',
            'patron' => 'patron.dashboard',
            'president' => 'president.dashboard',
            'student' => 'student.dashboard',
            'sa' => 'sa.dashboard',
            'vc' => 'vc.dashboard',
            'gd' => 'gd.dashboard',
            'faculty' => 'faculty.dashboard',
        ];

        $route = $dashboardRoutes[$user->role] ?? 'student.dashboard';
        
        return redirect()->route($route)->with('success', 'Welcome back, ' . $user->name . '!');
    }
}