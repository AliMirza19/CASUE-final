<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckPasswordChanged;
use App\Http\Middleware\FacultyRedirectMiddleware;
use App\Http\Middleware\HODMiddleware;
use App\Http\Middleware\PatronMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configure guest middleware to redirect authenticated users to their dashboard
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function ($request) {
            $user = $request->user();
            if (!$user) {
                return '/login';
            }
            
            $dashboardRoutes = [
                'admin' => '/admin/dashboard',
                'hod' => '/hod/dashboard',
                'patron' => '/patron/dashboard',
                'president' => '/president/dashboard',
                'student' => '/student/dashboard',
                'sa' => '/sa/dashboard',
                'vc' => '/vc/dashboard',
                'gd' => '/gd/dashboard',
                'faculty' => '/faculty/dashboard',
            ];
            
            return $dashboardRoutes[$user->role] ?? '/student/dashboard';
        });
        
        // Register custom middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'admin' => AdminMiddleware::class,
            'student' => StudentMiddleware::class,
            'hod' => HODMiddleware::class,
            'patron' => PatronMiddleware::class,
            'check.password.changed' => CheckPasswordChanged::class,
            'faculty.redirect' => FacultyRedirectMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 419 CSRF Token Mismatch - redirect back with error message
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please refresh and try again.',
                    'csrf_token' => csrf_token()
                ], 419);
            }
            
            return redirect()
                ->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Your session has expired. Please try again.');
        });
    })->create();
