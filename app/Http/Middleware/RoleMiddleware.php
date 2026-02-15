<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check if user has the required role
        $hasAccess = $user->role === $role;
        
        // Special handling for HOD and Patron roles
        // ONLY appointed faculty users should have access to HOD/Patron routes
        if ($role === 'hod') {
            // For HOD routes, ONLY appointed HOD can access
            $hasAccess = $user->isAppointedHod();
        } elseif ($role === 'patron') {
            // For Patron routes, ONLY appointed Patron can access
            $hasAccess = $user->isAppointedPatron();
        } elseif (!$hasAccess && $user->role === 'faculty') {
            // For other roles, faculty users who are appointed can access
            if ($role === 'hod' && $user->isAppointedHod()) {
                $hasAccess = true;
            } elseif ($role === 'patron' && $user->isAppointedPatron()) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->route('unauthorized')->with('error', 'You do not have permission to access this page.');
        }

        // Check if user needs to change password (except for password change routes)
        if ($user->needsPasswordChange() && !$request->routeIs('password.change*')) {
            return redirect()->route('password.change')->with('info', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}