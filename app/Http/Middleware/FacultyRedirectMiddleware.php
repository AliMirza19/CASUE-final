<?php

namespace App\Http\Middleware;

use App\Models\AcademicTerm;
use App\Models\RoleAssignment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FacultyRedirectMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Check if faculty user is appointed as HOD or Patron for the active term.
     * If so, redirect them to the appropriate dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only redirect if hitting the root or faculty routes specifically
        if (!$request->is('/') && !$request->is('faculty*')) {
            return $next($request);
        }
        
        // Only apply to faculty users
        if (!$user || $user->role !== 'faculty') {
            return $next($request);
        }
        
        $activeTerm = AcademicTerm::getActive();
        
        // Skip redirection if the user explicitly switched their active role to 'faculty'
        if ($user->getActiveRole() === 'faculty') {
            return $next($request);
        }
        
        if ($activeTerm) {
            // Check if appointed as HOD for active term
            $hodAssignment = RoleAssignment::getCurrentHod($activeTerm->id);
            if ($hodAssignment && $hodAssignment->user_id === $user->id) {
                return redirect()->route('hod.dashboard')
                    ->with('info', 'You are currently appointed as HOD.');
            }
            
            // Check if appointed as Patron for active term
            $patronAssignment = RoleAssignment::getCurrentPatron($activeTerm->id);
            if ($patronAssignment && $patronAssignment->user_id === $user->id) {
                return redirect()->route('patron.dashboard')
                    ->with('info', 'You are currently appointed as Patron.');
            }
        }
        
        return $next($request);
    }
}
