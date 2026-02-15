<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\UnauthorizedController;
use Illuminate\Support\Facades\Route;

// Redirect root to appropriate destination
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Special handling for faculty users who are appointed as HOD or Patron
        if ($user->role === 'faculty') {
            if ($user->isAppointedHod()) {
                return redirect()->route('hod.dashboard');
            } elseif ($user->isAppointedPatron()) {
                return redirect()->route('patron.dashboard');
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
        return redirect()->route($dashboardRoutes[$user->role] ?? 'student.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/unauthorized', [UnauthorizedController::class, 'index'])->name('unauthorized');
Route::get('/csrf-token', fn() => response()->json(['csrf_token' => csrf_token()]))->middleware('web');

// Protected Routes
Route::middleware(['auth', 'check.password.changed'])->group(function () {
    
    // ADMIN ROUTES
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('analytics');
        Route::get('/terms', [App\Http\Controllers\Admin\TermController::class, 'index'])->name('terms.index');
        Route::get('/terms/create', [App\Http\Controllers\Admin\TermController::class, 'create'])->name('terms.create');
        Route::post('/terms', [App\Http\Controllers\Admin\TermController::class, 'store'])->name('terms.store');
        Route::post('/terms/{id}/activate', [App\Http\Controllers\Admin\TermController::class, 'activate'])->name('terms.activate');
        Route::post('/terms/{id}/deactivate', [App\Http\Controllers\Admin\TermController::class, 'deactivate'])->name('terms.deactivate');
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/bulk-upload', [App\Http\Controllers\Admin\BulkUploadController::class, 'index'])->name('bulk-upload');
        Route::post('/bulk-upload', [App\Http\Controllers\Admin\BulkUploadController::class, 'upload'])->name('bulk-upload.upload');
        Route::get('/bulk-upload/sample', [App\Http\Controllers\Admin\BulkUploadController::class, 'downloadSample'])->name('bulk-upload.sample');
        // HOD Management Routes
        Route::get('/manage-hod', [App\Http\Controllers\Admin\DashboardController::class, 'manageHod'])->name('manage-hod');
        Route::get('/search-user-hod', [App\Http\Controllers\Admin\DashboardController::class, 'searchUserForHod'])->name('search-user-hod');
        Route::post('/continue-hod', [App\Http\Controllers\Admin\DashboardController::class, 'continueHod'])->name('continue-hod');
        Route::post('/appoint-hod', [App\Http\Controllers\Admin\DashboardController::class, 'appointHod'])->name('appoint-hod');
    });
    
    // STUDENT ROUTES
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\Student\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Student\DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/events', [App\Http\Controllers\Student\EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [App\Http\Controllers\Student\EventController::class, 'create'])->name('events.create');
        Route::post('/events', [App\Http\Controllers\Student\EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}', [App\Http\Controllers\Student\EventController::class, 'show'])->name('events.show');
        Route::get('/events/{id}/edit', [App\Http\Controllers\Student\EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{id}', [App\Http\Controllers\Student\EventController::class, 'update'])->name('events.update');
        Route::post('/events/{id}/forward', [App\Http\Controllers\Student\EventController::class, 'forwardToPatron'])->name('events.forward');
        Route::get('/election', [App\Http\Controllers\Student\ElectionController::class, 'index'])->name('election');
        Route::get('/election/register', [App\Http\Controllers\Student\ElectionController::class, 'register'])->name('election.register');
        Route::post('/election/register', [App\Http\Controllers\Student\ElectionController::class, 'submitRegistration'])->name('election.submit');
        Route::get('/election/vote', [App\Http\Controllers\Student\ElectionController::class, 'vote'])->name('election.vote');
        Route::post('/election/vote', [App\Http\Controllers\Student\ElectionController::class, 'castVote'])->name('election.cast');

        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/read', [App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    });
    
    // PRESIDENT ROUTES
    Route::middleware('role:president')->prefix('president')->name('president.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\President\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/events', [App\Http\Controllers\President\DashboardController::class, 'events'])->name('events');
        Route::get('/review/{id}', [App\Http\Controllers\President\DashboardController::class, 'review'])->name('review');
        Route::post('/review/{id}', [App\Http\Controllers\President\DashboardController::class, 'approve'])->name('approve');
        Route::get('/profile', [App\Http\Controllers\President\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\President\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // PATRON ROUTES
    Route::middleware('role:patron')->prefix('patron')->name('patron.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Patron\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/graphics', [App\Http\Controllers\Patron\DashboardController::class, 'graphics'])->name('graphics');
        Route::get('/candidates', [App\Http\Controllers\Patron\DashboardController::class, 'candidates'])->name('candidates');
        Route::get('/review-event/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewEvent'])->name('review-event');
        Route::post('/review-event/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveEvent'])->name('approve-event');
        Route::get('/review-candidate/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewCandidate'])->name('review-candidate');
        Route::post('/review-candidate/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveCandidate'])->name('approve-candidate');
        Route::get('/review-graphics/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewGraphics'])->name('review-graphics');
        Route::post('/review-graphics/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveGraphics'])->name('approve-graphics');
        Route::get('/chat', [App\Http\Controllers\Patron\ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [App\Http\Controllers\Patron\ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Patron\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/profile', [App\Http\Controllers\Patron\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Patron\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // HOD ROUTES
    Route::middleware('role:hod')->prefix('hod')->name('hod.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Hod\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/budget', [App\Http\Controllers\Hod\DashboardController::class, 'manageBudget'])->name('budget');
        Route::post('/budget', [App\Http\Controllers\Hod\DashboardController::class, 'saveBudget'])->name('budget.save');
        Route::post('/budget/lock', [App\Http\Controllers\Hod\DashboardController::class, 'lockBudget'])->name('budget.lock');
        Route::get('/review/{id}', [App\Http\Controllers\Hod\DashboardController::class, 'reviewEvent'])->name('review');
        Route::post('/review/{id}', [App\Http\Controllers\Hod\DashboardController::class, 'approveEvent'])->name('approve');
        Route::get('/analytics', [App\Http\Controllers\Hod\DashboardController::class, 'analytics'])->name('analytics');
        
        // Financial Reports Routes
        Route::get('/financial-reports', [App\Http\Controllers\Hod\DashboardController::class, 'financialReports'])->name('financial-reports');
        Route::get('/financial-reports/download', [App\Http\Controllers\Hod\DashboardController::class, 'exportFinancialSummary'])->name('financial-reports.download');
        Route::get('/api/financial-data', [App\Http\Controllers\Hod\DashboardController::class, 'getFinancialChartData'])->name('api.financial-data');
        Route::get('/api/spending-analytics', [App\Http\Controllers\Hod\DashboardController::class, 'getSpendingAnalytics'])->name('api.spending-analytics');
        
        Route::get('/chat', [App\Http\Controllers\Hod\ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [App\Http\Controllers\Hod\ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Hod\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/profile', [App\Http\Controllers\Hod\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Hod\DashboardController::class, 'updateProfile'])->name('profile.update');


        // Patron Management Routes
        Route::get('/manage-patron', [App\Http\Controllers\Hod\DashboardController::class, 'managePatron'])->name('manage-patron');
        Route::get('/search-user-patron', [App\Http\Controllers\Hod\DashboardController::class, 'searchUserForPatron'])->name('search-user-patron');
        Route::post('/continue-patron', [App\Http\Controllers\Hod\DashboardController::class, 'continuePatron'])->name('continue-patron');
        Route::post('/appoint-patron', [App\Http\Controllers\Hod\DashboardController::class, 'appointPatron'])->name('appoint-patron');
    });
    
    // SA ROUTES
    Route::middleware('role:sa')->prefix('sa')->name('sa.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Sa\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/events', [App\Http\Controllers\Sa\DashboardController::class, 'events'])->name('events');
        Route::get('/approved', [App\Http\Controllers\Sa\DashboardController::class, 'approved'])->name('approved');
        Route::get('/review/{id}', [App\Http\Controllers\Sa\DashboardController::class, 'reviewEvent'])->name('review');
        Route::post('/review/{id}', [App\Http\Controllers\Sa\DashboardController::class, 'approveEvent'])->name('approve');
        Route::get('/profile', [App\Http\Controllers\Sa\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Sa\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // VC ROUTES
    Route::middleware('role:vc')->prefix('vc')->name('vc.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Vc\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/volunteers', [App\Http\Controllers\Vc\DashboardController::class, 'volunteers'])->name('volunteers');
        Route::get('/assign/{eventId}', [App\Http\Controllers\Vc\DashboardController::class, 'assignVolunteers'])->name('assign');
        Route::post('/assign/{eventId}', [App\Http\Controllers\Vc\DashboardController::class, 'saveVolunteers'])->name('assign.save');
        Route::get('/profile', [App\Http\Controllers\Vc\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Vc\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // GD ROUTES
    Route::middleware('role:gd')->prefix('gd')->name('gd.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Gd\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/designs', [App\Http\Controllers\Gd\DashboardController::class, 'designs'])->name('designs');
        Route::get('/upload/{eventId}', [App\Http\Controllers\Gd\DashboardController::class, 'uploadDesign'])->name('upload');
        Route::post('/upload/{eventId}', [App\Http\Controllers\Gd\DashboardController::class, 'saveDesign'])->name('upload.save');
        Route::get('/view-feedback/{id}', [App\Http\Controllers\Gd\DashboardController::class, 'viewFeedback'])->name('view-feedback');
        Route::get('/profile', [App\Http\Controllers\Gd\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Gd\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // FACULTY ROUTES
    Route::middleware(['faculty.redirect', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Faculty\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/events', [App\Http\Controllers\Faculty\DashboardController::class, 'events'])->name('events');
        Route::get('/events/{id}', [App\Http\Controllers\Faculty\DashboardController::class, 'showEvent'])->name('events.show');
        Route::get('/societies', [App\Http\Controllers\Faculty\DashboardController::class, 'societies'])->name('societies');
        Route::get('/profile', [App\Http\Controllers\Faculty\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Faculty\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
});
