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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $allowedRoles = $roles; // $roles is already an array thanks to ...$roles
        
        \Log::info("MULTI-ROLE CHECK: User {$user->email} (Role: {$user->role}) against: " . implode(', ', $allowedRoles));
        
        // Basic role check
        $hasAccess = in_array($user->role, $allowedRoles);
        
        // Role Inheritance / Hierarchical Access
        $studentRoles = ['student', 'gd', 'photo', 'video', 'smt', 'doc', 'deco', 'vc', 'sa'];

        // 1. President can access anything a Student or Team Lead can
        if (!$hasAccess && $user->role === 'president') {
            foreach ($studentRoles as $sr) {
                if (in_array($sr, $allowedRoles)) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        // 2. Technical Team Roles (GD, Photo, SMT, etc.)
        // Students who are members of these teams should also have access
        if (!$hasAccess && in_array($user->role, $studentRoles)) {
            $roleToTeamType = [
                'gd' => 'graphics',
                'photo' => 'photo',
                'video' => 'video',
                'smt' => 'smt',
                'doc' => 'doc',
                'deco' => 'decoration',
            ];

            foreach ($allowedRoles as $role) {
                if (isset($roleToTeamType[$role])) {
                    $teamType = $roleToTeamType[$role];
                    // Check if user is in a team of this type
                    if ($user->teams()->where('type', $teamType)->exists()) {
                        $hasAccess = true;
                        break;
                    }
                }
                
                // Also allow team leads to access student-level pages
                if ($role === 'student') {
                    $hasAccess = true;
                    break;
                }
            }
        }

        // 3. Admin can access almost everything
        if ($user->role === 'admin') {
            $hasAccess = true; 
        }

        // Special handling for HOD and Patron roles (appointed status check)
        if (in_array('hod', $allowedRoles) && ($user->role === 'hod' || $user->isAppointedHod())) {
            $hasAccess = true;
        }
        if (in_array('patron', $allowedRoles) && ($user->role === 'patron' || $user->isAppointedPatron())) {
            $hasAccess = true;
        }
        
        // Re-check for specific appointed access if user is faculty
        if (!$hasAccess && $user->role === 'faculty') {
            if (in_array('hod', $allowedRoles) && $user->isAppointedHod()) {
                $hasAccess = true;
            } elseif (in_array('patron', $allowedRoles) && $user->isAppointedPatron()) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            \Log::info("Access for {$user->email} to " . $request->path() . " is DENIED. Allowed: " . implode(',', $allowedRoles));
            $dashboardRoutes = [
                'admin' => 'admin.dashboard',
                'hod' => 'hod.dashboard',
                'patron' => 'patron.dashboard',
                'president' => 'president.dashboard',
                'student' => 'student.dashboard',
                'sa' => 'sa.dashboard',
                'vc' => 'vc.dashboard',
                'gd' => 'gd.dashboard',
                'photo' => 'photo.dashboard',
                'video' => 'video.dashboard',
                'smt' => 'smt.dashboard',
                'doc' => 'doc.dashboard',
                'deco' => 'deco.dashboard',
                'faculty' => 'faculty.dashboard',
            ];
            $route = $dashboardRoutes[$user->role] ?? 'student.dashboard';
            return redirect()->route($route)->with('error', 'You do not have permission to access that page.');
        }

        // Check if user needs to change password (except for password change routes)
        if ($user->needsPasswordChange() && !$request->routeIs('password.change*')) {
            return redirect()->route('password.change')->with('info', 'You must change your password before continuing.');
        }

        \Log::info("Access for {$user->email} to " . $request->path() . " is ALLOWED.");
        return $next($request);
    }
}