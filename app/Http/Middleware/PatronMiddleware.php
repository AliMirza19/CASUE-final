<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PatronMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        if (!$user->hasRole('patron')) {
            return redirect()->route('unauthorized')->with('error', 'Patron access required.');
        }

        if ($user->needsPasswordChange() && !$request->routeIs('password.change*')) {
            return redirect()->route('password.change')->with('info', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}