<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates for role-based access control
        
        // Admin Gates
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-terms', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-system-settings', function (User $user) {
            return $user->isAdmin();
        });

        // HOD Gates
        Gate::define('hod-access', function (User $user) {
            return $user->hasRole('hod');
        });

        Gate::define('manage-budget', function (User $user) {
            return $user->hasRole('hod');
        });

        Gate::define('approve-hod-events', function (User $user) {
            return $user->hasRole('hod');
        });

        // Patron Gates
        Gate::define('patron-access', function (User $user) {
            return $user->hasRole('patron');
        });

        Gate::define('approve-patron-events', function (User $user) {
            return $user->hasRole('patron');
        });

        Gate::define('approve-candidates', function (User $user) {
            return $user->hasRole('patron');
        });

        Gate::define('approve-graphics', function (User $user) {
            return $user->hasRole('patron');
        });

        // President Gates
        Gate::define('president-access', function (User $user) {
            return $user->hasRole('president');
        });

        Gate::define('approve-president-events', function (User $user) {
            return $user->hasRole('president');
        });

        // Student Gates
        Gate::define('student-access', function (User $user) {
            return $user->isStudent();
        });

        Gate::define('submit-events', function (User $user) {
            return $user->isStudent();
        });

        Gate::define('submit-candidacy', function (User $user) {
            return $user->isStudent();
        });

        Gate::define('vote-in-elections', function (User $user) {
            return $user->isStudent();
        });

        // SA Gates
        Gate::define('sa-access', function (User $user) {
            return $user->hasRole('sa');
        });

        Gate::define('approve-sa-events', function (User $user) {
            return $user->hasRole('sa');
        });

        Gate::define('coordinate-activities', function (User $user) {
            return $user->hasRole('sa');
        });

        // VC Gates
        Gate::define('vc-access', function (User $user) {
            return $user->hasRole('vc');
        });

        Gate::define('manage-volunteers', function (User $user) {
            return $user->hasRole('vc');
        });

        // GD Gates
        Gate::define('gd-access', function (User $user) {
            return $user->hasRole('gd');
        });

        Gate::define('upload-graphics', function (User $user) {
            return $user->hasRole('gd');
        });

        // Event-specific Gates
        Gate::define('view-event', function (User $user, Event $event) {
            // Students can view their own events
            if ($user->isStudent() && $event->student_id === $user->id) {
                return true;
            }

            // Approvers can view events in their approval queue
            $nextApprover = $event->getNextApprover();
            if ($nextApprover && $user->hasRole($nextApprover)) {
                return true;
            }

            // Admins and SA can view all events
            return $user->isAdmin() || $user->hasRole('sa');
        });

        Gate::define('edit-event', function (User $user, Event $event) {
            // Only the student who submitted the event can edit it
            // And only if it's still pending
            return $user->isStudent() && 
                   $event->student_id === $user->id && 
                   $event->isPending();
        });

        Gate::define('approve-event', function (User $user, Event $event) {
            $nextApprover = $event->getNextApprover();
            return $nextApprover && $user->hasRole($nextApprover);
        });

        Gate::define('reject-event', function (User $user, Event $event) {
            // Any approver in the chain can reject
            $approverRoles = ['president', 'patron', 'hod', 'sa'];
            return in_array($user->role, $approverRoles) && $event->isPending();
        });

        // System-wide Gates
        Gate::define('access-dashboard', function (User $user, string $role) {
            return $user->hasRole($role);
        });

        Gate::define('view-activity-logs', function (User $user) {
            // Admins can view all logs, others can view their own
            return $user->isAdmin();
        });

        Gate::define('manage-announcements', function (User $user) {
            return $user->isAdmin() || $user->hasRole('sa');
        });

        // Election Gates
        Gate::define('manage-elections', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-election-results', function (User $user) {
            return true; // All authenticated users can view results
        });

        // Budget Gates
        Gate::define('view-budget-status', function (User $user) {
            // HOD, Admin, and SA can view budget status
            return $user->isAdmin() || $user->hasRole('hod') || $user->hasRole('sa');
        });
    }
}