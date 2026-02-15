<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     * 
     * This middleware ensures users who haven't changed their default password
     * are redirected to the password change page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Skip password change check for password change routes and logout
        if ($request->routeIs('password.change*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // If user needs to change password, redirect to password change page
        if ($user->needsPasswordChange()) {
            return redirect()->route('password.change')
                ->with('info', 'You must change your password before accessing the system.');
        }

        return $next($request);
    }
}