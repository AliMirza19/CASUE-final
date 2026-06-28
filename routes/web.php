<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UnauthorizedController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\EventChatController;
use Illuminate\Support\Facades\Route;

// Redirect root to appropriate destination
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $activeRole = $user->getActiveRole();
        
        if ($activeRole === 'student') {
            $teamType = $user->currentTeamType();
            $teamRoutes = [
                'graphics' => 'gd.dashboard',
                'photo' => 'photo.dashboard',
                'video' => 'video.dashboard',
                'smt' => 'smt.dashboard',
                'doc' => 'doc.dashboard',
                'decoration' => 'deco.dashboard',
                'volunteer' => 'student.dashboard', // Volunteer uses student dashboard or specialized one?
            ];
            if ($teamType && isset($teamRoutes[$teamType]) && $user->role === 'student') {
                return redirect()->route($teamRoutes[$teamType]);
            }
        }

        $dashboardRoutes = [
            'admin'    => 'admin.dashboard',
            'hod'      => 'hod.dashboard',
            'patron'   => 'patron.dashboard',
            'president'=> 'president.dashboard',
            'student'  => 'student.dashboard',
            'gd'       => 'gd.dashboard',
            'faculty'  => 'faculty.dashboard',
            'vc'       => 'vc.dashboard',
            'sa'       => 'sa.dashboard',
            'photo'    => 'photo.dashboard',
            'video'    => 'video.dashboard',
            'smt'      => 'smt.dashboard',
            'doc'      => 'doc.dashboard',
            'deco'     => 'deco.dashboard',
        ];
        return redirect()->route($dashboardRoutes[$activeRole] ?? 'student.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // High-Security 3-Step Password Reset via OTP
    Route::get('/verify-identity', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'showVerifyIdentityForm'])->name('password.verify.identity');
    Route::post('/verify-identity', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'processVerifyIdentity'])->name('password.verify.identity.submit');
    
    Route::get('/verify-otp', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verify-otp', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'processOtp'])->name('password.otp.submit');
    
    Route::get('/reset-password', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\CustomForgotPasswordController::class, 'processResetPassword'])->name('password.reset.submit');
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/unauthorized', [UnauthorizedController::class, 'index'])->name('unauthorized');
Route::get('/csrf-token', fn() => response()->json(['csrf_token' => csrf_token()]))->middleware('web');

// Public Certificate Verification
Route::get('/verify-certificate/{uuid}', [\App\Http\Controllers\CertificateVerificationController::class, 'verify'])->name('verify.certificate');

Route::middleware(['auth'])->group(function () {
    Route::middleware('role:admin,hod,patron')->prefix('archive')->name('archive.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('index');
        Route::get('/term/{term}', [\App\Http\Controllers\ArchiveController::class, 'showTerm'])->name('term');
        Route::get('/event/{event}', [\App\Http\Controllers\ArchiveController::class, 'showEventArchive'])->name('event');
    });

    Route::middleware(['check.password.changed'])->group(function () {
        // Announcement Management
        Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);

    // President Selection Workflow
    Route::prefix('selection')->name('selection.')->group(function() {
        // Step 1: Patron
        Route::get('/patron', [SelectionController::class, 'patronView'])->name('patron');
        Route::post('/shortlist/{candidate}', [SelectionController::class, 'shortlist'])->name('shortlist');
        
        // Step 2: HOD
        Route::get('/hod', [SelectionController::class, 'hodView'])->name('hod');
        Route::post('/form-committee', [SelectionController::class, 'formCommittee'])->name('form-committee');
        
        // Step 3 & 4: Discussion & Finalization
        Route::get('/discussion', [SelectionController::class, 'discussionRoom'])->name('discussion');
        Route::post('/message/{committee}', [SelectionController::class, 'sendMessage'])->name('send-message');
        Route::match(['get', 'post'], '/finalize/{committee}/{candidate}', [SelectionController::class, 'finalizePresident'])->name('finalize');
    });

    // Task Management
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::match(['get', 'post'], '/tasks/bulk', [\App\Http\Controllers\TaskController::class, 'bulkStore'])->name('tasks.bulk');
    Route::patch('/tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    
    // CAUSE-AI Chatbot Routes
    Route::post('/api/ai-chat', [\App\Http\Controllers\ChatController::class, 'chat'])->name('ai.chat');
    Route::get('/api/ai-chat/history', [\App\Http\Controllers\ChatController::class, 'history'])->name('ai.history');

    // Chat System
    Route::get('/chat', [EventChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [EventChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{id}/send', [EventChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/groups/{id}/messages', [EventChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/messages/{id}/annotate', [EventChatController::class, 'annotate'])->name('chat.annotate');
    
    // Direct Chat Routes
    Route::get('/direct-chat', [\App\Http\Controllers\DirectChatController::class, 'index'])->name('direct-chat.index');
    Route::get('/direct-chat/messages/{userId}', [\App\Http\Controllers\DirectChatController::class, 'getMessages'])->name('direct-chat.messages');
    Route::post('/direct-chat/send/{userId}', [\App\Http\Controllers\DirectChatController::class, 'sendMessage'])->name('direct-chat.send');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Universal Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/switch-role', [\App\Http\Controllers\ProfileController::class, 'switchRole'])->name('profile.switch-role');
    
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
        Route::match(['get', 'post'], '/continue-hod', [App\Http\Controllers\Admin\DashboardController::class, 'continueHod'])->name('continue-hod');
        Route::match(['get', 'post'], '/appoint-hod', [App\Http\Controllers\Admin\DashboardController::class, 'appointHod'])->name('appoint-hod');

        // CSV Import Routes
        Route::get('/import/students', [App\Http\Controllers\ImportController::class, 'showStudentImport'])->name('import.students');
        Route::post('/import/students', [App\Http\Controllers\ImportController::class, 'importStudents']);
        Route::get('/import/faculty', [App\Http\Controllers\ImportController::class, 'showFacultyImport'])->name('import.faculty');
        Route::post('/import/faculty', [App\Http\Controllers\ImportController::class, 'importFaculty']);

        // Detailed Student Data Collection (F25 AI-SE Template)
        Route::get('/students/create', [App\Http\Controllers\Admin\StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [App\Http\Controllers\Admin\StudentController::class, 'store'])->name('students.store');
        Route::post('/students/bulk', [App\Http\Controllers\Admin\StudentController::class, 'bulkUpload'])->name('students.bulk');
        Route::get('/students/sample', [App\Http\Controllers\Admin\StudentController::class, 'downloadSample'])->name('students.sample');

        // Detailed Faculty Data Collection (Admin Template)
        Route::get('/faculty/create', [App\Http\Controllers\Admin\FacultyController::class, 'create'])->name('faculty.create');
        Route::post('/faculty', [App\Http\Controllers\Admin\FacultyController::class, 'store'])->name('faculty.store');
        Route::post('/faculty/bulk', [App\Http\Controllers\Admin\FacultyController::class, 'bulkUpload'])->name('faculty.bulk');
        Route::get('/faculty/sample', [App\Http\Controllers\Admin\FacultyController::class, 'downloadSample'])->name('faculty.sample');

        // Budget Management (Moved from HOD)
        Route::get('/budget', [App\Http\Controllers\Admin\DashboardController::class, 'manageBudget'])->name('budget');
        Route::post('/budget', [App\Http\Controllers\Admin\DashboardController::class, 'saveBudget'])->name('budget.save');
    });
    
    // STUDENT ROUTES
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Student\DashboardController::class, 'overview'])->name('overview');
        Route::get('/profile', [App\Http\Controllers\Student\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Student\DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/events', [App\Http\Controllers\Student\EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [App\Http\Controllers\Student\EventController::class, 'create'])->name('events.create');
        Route::post('/events', [App\Http\Controllers\Student\EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}', [App\Http\Controllers\Student\EventController::class, 'show'])->name('events.show');
        Route::get('/events/{id}/edit', [App\Http\Controllers\Student\EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{id}', [App\Http\Controllers\Student\EventController::class, 'update'])->name('events.update');
        Route::post('/events/{id}/forward', [App\Http\Controllers\Student\EventController::class, 'forwardToPatron'])->name('events.forward');
        Route::post('/join-volunteer-pool', [App\Http\Controllers\Student\DashboardController::class, 'joinVolunteerPool'])->name('join-volunteer-pool');
        Route::get('/election', [App\Http\Controllers\Student\ElectionController::class, 'index'])->name('election');
        Route::get('/election/register', [App\Http\Controllers\Student\ElectionController::class, 'register'])->name('election.register');
        Route::post('/election/register', [App\Http\Controllers\Student\ElectionController::class, 'submitRegistration'])->name('election.submit');
        Route::post('/election/optimize-manifesto', [App\Http\Controllers\Student\ElectionController::class, 'optimizeManifesto'])->name('election.optimize-manifesto');
        Route::get('/election/vote', [App\Http\Controllers\Student\ElectionController::class, 'vote'])->name('election.vote');
        Route::post('/election/vote', [App\Http\Controllers\Student\ElectionController::class, 'castVote'])->name('election.cast');
        Route::get('/events/{id}/download-approval', [App\Http\Controllers\Student\EventController::class, 'downloadApproval'])->name('events.download-approval');
        Route::get('/faq', [App\Http\Controllers\Student\DashboardController::class, 'faq'])->name('faq');
        
        // Student-President Messaging
        Route::get('/messages', [App\Http\Controllers\Student\MessageController::class, 'index'])->name('messages');
        Route::post('/messages/send', [App\Http\Controllers\Student\MessageController::class, 'sendMessage'])->name('messages.send');
        Route::get('/messages/fetch', [App\Http\Controllers\Student\MessageController::class, 'fetchMessages'])->name('messages.fetch');
    });
    
    // PRESIDENT ROUTES
    Route::middleware('role:president')->prefix('president')->name('president.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\President\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\President\DashboardController::class, 'overview'])->name('overview');
        Route::get('/events', [App\Http\Controllers\President\DashboardController::class, 'events'])->name('events');
        Route::get('/review/{id}', [App\Http\Controllers\President\DashboardController::class, 'review'])->name('review');
        Route::post('/review/{id}', [App\Http\Controllers\President\DashboardController::class, 'approve'])->name('approve');
        Route::get('/profile', [App\Http\Controllers\President\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\President\DashboardController::class, 'updateProfile'])->name('profile.update');
        
        // President's Own Events
        Route::get('/my-events', [App\Http\Controllers\President\EventController::class, 'index'])->name('my-events.index');
        Route::get('/my-events/create', [App\Http\Controllers\President\EventController::class, 'create'])->name('my-events.create');
        Route::post('/my-events', [App\Http\Controllers\President\EventController::class, 'store'])->name('my-events.store');
        Route::get('/my-events/{id}', [App\Http\Controllers\President\EventController::class, 'show'])->name('my-events.show');
        
        // Track All Events
        Route::get('/track-events', [App\Http\Controllers\President\EventController::class, 'trackEvents'])->name('track-events');
        
        // Appoint Team Leads
        Route::get('/manage-teams', [App\Http\Controllers\President\DashboardController::class, 'manageTeams'])->name('manage-teams');
        Route::get('/search-student', [App\Http\Controllers\President\DashboardController::class, 'searchStudent'])->name('search-student');
        Route::match(['get', 'post'], '/continue-team-lead', [App\Http\Controllers\President\DashboardController::class, 'continueTeamLead'])->name('continue-team-lead');
        Route::match(['get', 'post'], '/appoint-team-lead', [App\Http\Controllers\President\DashboardController::class, 'appointTeamLead'])->name('appoint-team-lead');

        // Tasks Management
        Route::get('/tasks', [App\Http\Controllers\President\DashboardController::class, 'viewTasks'])->name('tasks.index');
        Route::get('/tasks/assign', [App\Http\Controllers\President\DashboardController::class, 'assignTasks'])->name('tasks.assign');
        
        // Review Events List
        Route::get('/review-list', [App\Http\Controllers\President\DashboardController::class, 'reviewEvents'])->name('review-list');
        
        // President-Student Messaging
        Route::get('/student-messages', [App\Http\Controllers\President\MessageController::class, 'index'])->name('student-messages');
        Route::get('/student-messages/{studentId}', [App\Http\Controllers\President\MessageController::class, 'conversation'])->name('student-messages.conversation');
        Route::post('/student-messages/{studentId}/send', [App\Http\Controllers\President\MessageController::class, 'sendMessage'])->name('student-messages.send');
        Route::get('/student-messages/{studentId}/fetch', [App\Http\Controllers\President\MessageController::class, 'fetchMessages'])->name('student-messages.fetch');
    });


    
    // PATRON ROUTES
    Route::middleware('role:patron')->prefix('patron')->name('patron.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Patron\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Patron\DashboardController::class, 'overview'])->name('overview');
        Route::get('/graphics', [App\Http\Controllers\Patron\DashboardController::class, 'graphics'])->name('graphics');
        Route::get('/candidates', [App\Http\Controllers\Patron\DashboardController::class, 'candidates'])->name('candidates');
        Route::get('/review-event/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewEvent'])->name('review-event');
        Route::post('/review-event/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveEvent'])->name('approve-event');
        Route::get('/review-candidate/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewCandidate'])->name('review-candidate');
        Route::post('/review-candidate/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveCandidate'])->name('approve-candidate');
        Route::get('/review-graphics/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'reviewGraphics'])->name('review-graphics');
        Route::post('/review-graphics/{id}', [App\Http\Controllers\Patron\DashboardController::class, 'approveGraphics'])->name('approve-graphics');
        
        // Patron's Own Events
        Route::get('/my-events', [App\Http\Controllers\Patron\EventController::class, 'index'])->name('my-events.index');
        Route::get('/my-events/create', [App\Http\Controllers\Patron\EventController::class, 'create'])->name('my-events.create');
        Route::post('/my-events', [App\Http\Controllers\Patron\EventController::class, 'store'])->name('my-events.store');
        Route::get('/my-events/{id}', [App\Http\Controllers\Patron\EventController::class, 'show'])->name('my-events.show');
        
        // Track All Events
        Route::get('/track-events', [App\Http\Controllers\Patron\EventController::class, 'trackEvents'])->name('track-events');
        
        Route::get('/chat', [App\Http\Controllers\Patron\ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [App\Http\Controllers\Patron\ChatController::class, 'send'])->name('chat.send');
        Route::post('/chat/summarize', [App\Http\Controllers\Patron\ChatController::class, 'summarize'])->name('chat.summarize');
        Route::get('/chat/messages', [App\Http\Controllers\Patron\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/election-settings', [App\Http\Controllers\Patron\ElectionSettingController::class, 'index'])->name('election.settings');
        Route::post('/election-settings', [App\Http\Controllers\Patron\ElectionSettingController::class, 'update'])->name('election.settings.update');
        Route::get('/profile', [App\Http\Controllers\Patron\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Patron\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // HOD ROUTES
    Route::middleware('role:hod')->prefix('hod')->name('hod.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Hod\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Hod\DashboardController::class, 'overview'])->name('overview');
        Route::get('/budget', [App\Http\Controllers\Hod\DashboardController::class, 'manageBudget'])->name('budget');
        Route::get('/review/{id}', [App\Http\Controllers\Hod\DashboardController::class, 'reviewEvent'])->name('review');
        Route::match(['get', 'post'], '/review/{id}/final', [App\Http\Controllers\Hod\DashboardController::class, 'finalApprovalForm'])->name('review.final');
        Route::post('/review/{id}', [App\Http\Controllers\Hod\DashboardController::class, 'approveEvent'])->name('approve');
        Route::get('/analytics', [App\Http\Controllers\Hod\DashboardController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [App\Http\Controllers\Hod\DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\Hod\DashboardController::class, 'updateSettings'])->name('settings.update');
        
        // HOD's Own Events
        Route::get('/my-events', [App\Http\Controllers\Hod\EventController::class, 'index'])->name('my-events.index');
        Route::get('/my-events/create', [App\Http\Controllers\Hod\EventController::class, 'create'])->name('my-events.create');
        Route::post('/my-events', [App\Http\Controllers\Hod\EventController::class, 'store'])->name('my-events.store');
        Route::get('/my-events/{id}', [App\Http\Controllers\Hod\EventController::class, 'show'])->name('my-events.show');
        
        // Financial Reports Routes
        Route::get('/financial-reports', [App\Http\Controllers\Hod\DashboardController::class, 'financialReports'])->name('financial-reports');
        Route::get('/financial-reports/download', [App\Http\Controllers\Hod\DashboardController::class, 'exportFinancialSummary'])->name('financial-reports.download');
        Route::get('/api/financial-data', [App\Http\Controllers\Hod\DashboardController::class, 'getFinancialChartData'])->name('api.financial-data');
        Route::get('/api/spending-analytics', [App\Http\Controllers\Hod\DashboardController::class, 'getSpendingAnalytics'])->name('api.spending-analytics');
        
        Route::get('/chat', [App\Http\Controllers\Hod\ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [App\Http\Controllers\Hod\ChatController::class, 'send'])->name('chat.send');
        Route::post('/chat/summarize', [App\Http\Controllers\Hod\ChatController::class, 'summarize'])->name('chat.summarize');
        Route::get('/chat/messages', [App\Http\Controllers\Hod\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/profile', [App\Http\Controllers\Hod\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Hod\DashboardController::class, 'updateProfile'])->name('profile.update');


        // Patron Management Routes
        Route::get('/manage-patron', [App\Http\Controllers\Hod\DashboardController::class, 'managePatron'])->name('manage-patron');
        Route::get('/search-user-patron', [App\Http\Controllers\Hod\DashboardController::class, 'searchUserForPatron'])->name('search-user-patron');
        Route::post('/continue-patron', [App\Http\Controllers\Hod\DashboardController::class, 'continuePatron'])->name('continue-patron');
        Route::post('/appoint-patron', [App\Http\Controllers\Hod\DashboardController::class, 'appointPatron'])->name('appoint-patron');
    });
    

    // VC ROUTES
    Route::middleware('role:vc')->prefix('vc')->name('vc.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Vc\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Vc\DashboardController::class, 'overview'])->name('overview');
        Route::get('/search-students', [App\Http\Controllers\Vc\DashboardController::class, 'searchStudents'])->name('search-students');
        Route::post('/events/{eventId}/assign-volunteer', [App\Http\Controllers\Vc\DashboardController::class, 'assignVolunteer'])->name('assign-volunteer');
        Route::delete('/events/{eventId}/remove-volunteer/{volunteerId}', [App\Http\Controllers\Vc\DashboardController::class, 'removeVolunteer'])->name('remove-volunteer');
        Route::post('/api/suggest-volunteers/{eventId}', [App\Http\Controllers\Vc\DashboardController::class, 'suggestVolunteers'])->name('api.suggest');
    });
    
    
    // GD ROUTES
    Route::middleware('role:gd')->prefix('gd')->name('gd.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Gd\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Gd\DashboardController::class, 'overview'])->name('overview');
        Route::get('/designs', [App\Http\Controllers\Gd\DashboardController::class, 'designs'])->name('designs');
        Route::get('/upload/{eventId}', [App\Http\Controllers\Gd\DashboardController::class, 'uploadDesign'])->name('upload');
        Route::post('/upload/{eventId}', [App\Http\Controllers\Gd\DashboardController::class, 'saveDesign'])->name('upload.save');
        Route::post('/api/ai-persona', [App\Http\Controllers\Gd\DashboardController::class, 'getAiPersona'])->name('api.ai-persona');
        Route::post('/api/ai-copy', [App\Http\Controllers\Gd\DashboardController::class, 'getAiCopy'])->name('api.ai-copy');
        Route::get('/view-feedback/{id}', [App\Http\Controllers\Gd\DashboardController::class, 'viewFeedback'])->name('view-feedback');
        Route::get('/profile', [App\Http\Controllers\Gd\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Gd\DashboardController::class, 'updateProfile'])->name('profile.update');

        // Certificate Generator Routes
        Route::get('/certificate-generator', [App\Http\Controllers\Gd\CertificateGeneratorController::class, 'index'])->name('certificate.generator');
        Route::post('/certificate-generator/process', [App\Http\Controllers\Gd\CertificateGeneratorController::class, 'process'])->name('certificate.process');
    });
    
    // FACULTY ROUTES
    Route::middleware(['faculty.redirect', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Faculty\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Faculty\DashboardController::class, 'overview'])->name('overview');
        Route::get('/events', [App\Http\Controllers\Faculty\DashboardController::class, 'events'])->name('events');
        Route::get('/events/{id}', [App\Http\Controllers\Faculty\DashboardController::class, 'showEvent'])->name('events.show');
        Route::get('/societies', [App\Http\Controllers\Faculty\DashboardController::class, 'societies'])->name('societies');
        Route::get('/profile', [App\Http\Controllers\Faculty\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Faculty\DashboardController::class, 'updateProfile'])->name('profile.update');

        // Faculty's Own Events
        Route::get('/my-events', [App\Http\Controllers\Faculty\MyEventController::class, 'index'])->name('my-events.index');
        Route::get('/my-events/create', [App\Http\Controllers\Faculty\MyEventController::class, 'create'])->name('my-events.create');
        Route::post('/my-events', [App\Http\Controllers\Faculty\MyEventController::class, 'store'])->name('my-events.store');
        Route::get('/my-events/{id}', [App\Http\Controllers\Faculty\MyEventController::class, 'show'])->name('my-events.show');
        Route::get('/my-events/{id}/edit', [App\Http\Controllers\Faculty\MyEventController::class, 'edit'])->name('my-events.edit');
        Route::put('/my-events/{id}', [App\Http\Controllers\Faculty\MyEventController::class, 'update'])->name('my-events.update');
        Route::post('/my-events/{id}/forward', [App\Http\Controllers\Faculty\MyEventController::class, 'forwardToPatron'])->name('my-events.forward');
        Route::get('/my-events/{id}/download-approval', [App\Http\Controllers\Faculty\MyEventController::class, 'downloadApproval'])->name('my-events.download-approval');
    });

    // PHOTOGRAPHY TEAM ROUTES
    Route::middleware('role:photo')->prefix('photo')->name('photo.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Photo\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Photo\DashboardController::class, 'overview'])->name('overview');
        Route::match(['get', 'post'], '/events/{eventId}/upload', [App\Http\Controllers\Photo\DashboardController::class, 'upload'])->name('upload');
        Route::delete('/media/{id}', [App\Http\Controllers\Photo\DashboardController::class, 'destroy'])->name('destroy');
        Route::get('/profile', [App\Http\Controllers\Photo\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Photo\DashboardController::class, 'updateProfile'])->name('profile.update');
    });

    // VIDEOGRAPHY TEAM ROUTES
    Route::middleware('role:video')->prefix('video')->name('video.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Video\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Video\DashboardController::class, 'overview'])->name('overview');
        Route::match(['get', 'post'], '/events/{eventId}/upload', [App\Http\Controllers\Video\DashboardController::class, 'upload'])->name('upload');
        Route::delete('/media/{id}', [App\Http\Controllers\Video\DashboardController::class, 'destroy'])->name('destroy');
        Route::get('/profile', [App\Http\Controllers\Video\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Video\DashboardController::class, 'updateProfile'])->name('profile.update');
    });

    // SOCIAL MEDIA TEAM ROUTES
    Route::middleware('role:smt')->prefix('smt')->name('smt.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Smt\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Smt\DashboardController::class, 'overview'])->name('overview');
        Route::post('/events/{eventId}/link', [App\Http\Controllers\Smt\DashboardController::class, 'addLink'])->name('add-link');
        Route::delete('/links/{id}', [App\Http\Controllers\Smt\DashboardController::class, 'destroy'])->name('destroy');
        Route::get('/profile', [App\Http\Controllers\Smt\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Smt\DashboardController::class, 'updateProfile'])->name('profile.update');
    });

    // DOCUMENTATION TEAM ROUTES
    Route::middleware('role:doc')->prefix('doc')->name('doc.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Doc\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Doc\DashboardController::class, 'overview'])->name('overview');
        Route::match(['get', 'post'], '/events/{eventId}/upload', [App\Http\Controllers\Doc\DashboardController::class, 'upload'])->name('upload');
        Route::get('/documents/{id}/download', [App\Http\Controllers\Doc\DashboardController::class, 'download'])->name('download');
        Route::delete('/documents/{id}', [App\Http\Controllers\Doc\DashboardController::class, 'destroy'])->name('destroy');
        Route::get('/profile', [App\Http\Controllers\Doc\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Doc\DashboardController::class, 'updateProfile'])->name('profile.update');
    });

    // DECORATION TEAM ROUTES
    Route::middleware('role:deco')->prefix('deco')->name('deco.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Deco\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\Deco\DashboardController::class, 'overview'])->name('overview');
        Route::match(['get', 'post'], '/events/{eventId}/plan', [App\Http\Controllers\Deco\DashboardController::class, 'createPlan'])->name('create-plan');
        Route::patch('/plans/{id}/status', [App\Http\Controllers\Deco\DashboardController::class, 'updateStatus'])->name('update-status');
        Route::get('/plans/{id}', [App\Http\Controllers\Deco\DashboardController::class, 'show'])->name('show');
        Route::get('/profile', [App\Http\Controllers\Deco\DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Deco\DashboardController::class, 'updateProfile'])->name('profile.update');
    });
});
});
