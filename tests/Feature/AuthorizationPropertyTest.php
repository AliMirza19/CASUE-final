<?php

use App\Models\AcademicTerm;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

/**
 * Property Test: Role-Based Access Control
 * Feature: cause-society-laravel-migration, Property 4: Role-Based Access Control
 * 
 * For any user role and system functionality combination, unauthorized access 
 * attempts should be blocked and redirect to unauthorized page.
 */
test('role-based access control blocks unauthorized access', function () {
    // Run this property test 100 times with different random data
    for ($i = 0; $i < 100; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        $dashboardRoutes = [
            'admin' => '/admin/dashboard',
            'hod' => '/hod/dashboard',
            'patron' => '/patron/dashboard',
            'president' => '/president/dashboard',
            'student' => '/student/dashboard',
            'sa' => '/sa/dashboard',
            'vc' => '/vc/dashboard',
            'gd' => '/gd/dashboard',
        ];
        
        // Test each role trying to access other role's dashboards
        foreach ($roles as $userRole) {
            $user = User::factory()->create([
                'role' => $userRole,
                'password_changed' => true,
                'current_term_id' => $term->id,
            ]);
            
            $this->actingAs($user);
            
            // Test access to all dashboards
            foreach ($dashboardRoutes as $targetRole => $route) {
                $response = $this->get($route);
                
                if ($targetRole === $userRole) {
                    // Should be able to access own dashboard
                    $response->assertOk();
                } else {
                    // Should be redirected to unauthorized page
                    $response->assertRedirect('/unauthorized');
                }
            }
            
            $this->post('/logout');
        }
        
        // Clean up
        $term->delete();
    }
});

/**
 * Property Test: Session Security Management
 * Feature: cause-society-laravel-migration, Property 5: Session Security Management
 * 
 * For any authenticated user session, logout should properly destroy the session 
 * and prevent further access without re-authentication.
 */
test('session security management prevents access after logout', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        $dashboardRoutes = [
            'admin' => '/admin/dashboard',
            'hod' => '/hod/dashboard',
            'patron' => '/patron/dashboard',
            'president' => '/president/dashboard',
            'student' => '/student/dashboard',
            'sa' => '/sa/dashboard',
            'vc' => '/vc/dashboard',
            'gd' => '/gd/dashboard',
        ];
        
        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'password_changed' => true,
                'current_term_id' => $term->id,
            ]);
            
            // Login user
            $this->actingAs($user);
            
            // Should be able to access dashboard when logged in
            $dashboardRoute = $dashboardRoutes[$role];
            $response = $this->get($dashboardRoute);
            $response->assertOk();
            
            // Logout
            $response = $this->post('/logout');
            $response->assertRedirect('/login');
            
            // Should no longer be authenticated
            $this->assertGuest();
            
            // Should not be able to access dashboard after logout
            $response = $this->get($dashboardRoute);
            $response->assertRedirect('/login');
            
            // Should not be able to access any protected routes
            foreach ($dashboardRoutes as $route) {
                $response = $this->get($route);
                $response->assertRedirect('/login');
            }
        }
        
        $term->delete();
    }
});

/**
 * Property Test: Gates Authorization
 * Tests that Gates properly authorize actions based on user roles
 */
test('gates properly authorize actions based on user roles', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        // Test admin gates
        $admin = User::factory()->create([
            'role' => 'admin',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $this->actingAs($admin);
        
        expect(Gate::allows('admin-access'))->toBeTrue();
        expect(Gate::allows('manage-users'))->toBeTrue();
        expect(Gate::allows('manage-terms'))->toBeTrue();
        expect(Gate::allows('manage-system-settings'))->toBeTrue();
        expect(Gate::allows('student-access'))->toBeFalse();
        expect(Gate::allows('hod-access'))->toBeFalse();
        
        // Test student gates
        $student = User::factory()->create([
            'role' => 'student',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $this->actingAs($student);
        
        expect(Gate::allows('student-access'))->toBeTrue();
        expect(Gate::allows('submit-events'))->toBeTrue();
        expect(Gate::allows('submit-candidacy'))->toBeTrue();
        expect(Gate::allows('vote-in-elections'))->toBeTrue();
        expect(Gate::allows('admin-access'))->toBeFalse();
        expect(Gate::allows('manage-users'))->toBeFalse();
        expect(Gate::allows('manage-budget'))->toBeFalse();
        
        // Test HOD gates
        $hod = User::factory()->create([
            'role' => 'hod',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $this->actingAs($hod);
        
        expect(Gate::allows('hod-access'))->toBeTrue();
        expect(Gate::allows('manage-budget'))->toBeTrue();
        expect(Gate::allows('approve-hod-events'))->toBeTrue();
        expect(Gate::allows('admin-access'))->toBeFalse();
        expect(Gate::allows('student-access'))->toBeFalse();
        
        // Test Patron gates
        $patron = User::factory()->create([
            'role' => 'patron',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $this->actingAs($patron);
        
        expect(Gate::allows('patron-access'))->toBeTrue();
        expect(Gate::allows('approve-patron-events'))->toBeTrue();
        expect(Gate::allows('approve-candidates'))->toBeTrue();
        expect(Gate::allows('approve-graphics'))->toBeTrue();
        expect(Gate::allows('admin-access'))->toBeFalse();
        expect(Gate::allows('manage-budget'))->toBeFalse();
        
        $term->delete();
    }
});

/**
 * Property Test: Event Policy Authorization
 * Tests that Event policies properly control access to event operations
 */
test('event policies properly control access to event operations', function () {
    for ($i = 0; $i < 30; $i++) {
        $term = AcademicTerm::factory()->create();
        
        // Create users with different roles
        $student = User::factory()->create([
            'role' => 'student',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $president = User::factory()->create([
            'role' => 'president',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $patron = User::factory()->create([
            'role' => 'patron',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $admin = User::factory()->create([
            'role' => 'admin',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        $otherStudent = User::factory()->create([
            'role' => 'student',
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        // Create an event by the first student
        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'pending_president',
        ]);
        
        // Test student permissions on their own event
        $this->actingAs($student);
        expect(Gate::allows('view', $event))->toBeTrue();
        expect(Gate::allows('update', $event))->toBeTrue();
        expect(Gate::allows('delete', $event))->toBeTrue();
        expect(Gate::allows('approve', $event))->toBeFalse(); // Students can't approve
        
        // Test other student permissions on the event
        $this->actingAs($otherStudent);
        expect(Gate::allows('view', $event))->toBeFalse(); // Can't view other's events
        expect(Gate::allows('update', $event))->toBeFalse();
        expect(Gate::allows('delete', $event))->toBeFalse();
        expect(Gate::allows('approve', $event))->toBeFalse();
        
        // Test president permissions (next approver)
        $this->actingAs($president);
        expect(Gate::allows('view', $event))->toBeTrue(); // Can view events in queue
        expect(Gate::allows('approve', $event))->toBeTrue(); // Can approve as next approver
        expect(Gate::allows('reject', $event))->toBeTrue(); // Can reject
        expect(Gate::allows('update', $event))->toBeFalse(); // Can't edit others' events
        
        // Test patron permissions (not next approver yet)
        $this->actingAs($patron);
        expect(Gate::allows('view', $event))->toBeFalse(); // Not in queue yet
        expect(Gate::allows('approve', $event))->toBeFalse(); // Not next approver
        expect(Gate::allows('reject', $event))->toBeTrue(); // Can still reject
        
        // Test admin permissions
        $this->actingAs($admin);
        expect(Gate::allows('view', $event))->toBeTrue(); // Admins can view all
        expect(Gate::allows('delete', $event))->toBeTrue(); // Admins can delete
        expect(Gate::allows('update', $event))->toBeFalse(); // Can't edit others' events
        
        // Test approved event permissions
        $approvedEvent = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'approved',
        ]);
        
        $this->actingAs($student);
        expect(Gate::allows('view', $approvedEvent))->toBeTrue();
        expect(Gate::allows('update', $approvedEvent))->toBeFalse(); // Can't edit approved events
        expect(Gate::allows('delete', $approvedEvent))->toBeFalse(); // Can't delete approved events
        
        $term->delete();
    }
});

/**
 * Property Test: Password Change Requirement Enforcement
 * Tests that users who need to change password are properly redirected
 */
test('password change requirement is properly enforced', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        $dashboardRoutes = [
            'admin' => '/admin/dashboard',
            'hod' => '/hod/dashboard',
            'patron' => '/patron/dashboard',
            'president' => '/president/dashboard',
            'student' => '/student/dashboard',
            'sa' => '/sa/dashboard',
            'vc' => '/vc/dashboard',
            'gd' => '/gd/dashboard',
        ];
        
        foreach ($roles as $role) {
            // Create user who needs to change password
            $user = User::factory()->create([
                'role' => $role,
                'password_changed' => false, // Needs password change
                'current_term_id' => $term->id,
            ]);
            
            $this->actingAs($user);
            
            // Should be redirected to password change page when accessing dashboard
            $dashboardRoute = $dashboardRoutes[$role];
            $response = $this->get($dashboardRoute);
            $response->assertRedirect('/change-password');
            
            // Should be able to access password change page
            $response = $this->get('/change-password');
            $response->assertOk();
            
            // Should be able to logout
            $response = $this->post('/logout');
            $response->assertRedirect('/login');
            
            $this->assertGuest();
        }
        
        $term->delete();
    }
});

/**
 * Property Test: Cross-Role Access Prevention
 * Tests that users cannot access functionality meant for other roles
 */
test('cross-role access is properly prevented', function () {
    for ($i = 0; $i < 20; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $users = [];
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        
        // Create one user for each role
        foreach ($roles as $role) {
            $users[$role] = User::factory()->create([
                'role' => $role,
                'password_changed' => true,
                'current_term_id' => $term->id,
            ]);
        }
        
        // Test that each user can only access their own role's gates
        foreach ($users as $userRole => $user) {
            $this->actingAs($user);
            
            // Test role-specific gates
            $roleGates = [
                'admin' => ['admin-access', 'manage-users', 'manage-terms'],
                'hod' => ['hod-access', 'manage-budget', 'approve-hod-events'],
                'patron' => ['patron-access', 'approve-patron-events', 'approve-candidates'],
                'president' => ['president-access', 'approve-president-events'],
                'student' => ['student-access', 'submit-events', 'vote-in-elections'],
                'sa' => ['sa-access', 'approve-sa-events', 'coordinate-activities'],
                'vc' => ['vc-access', 'manage-volunteers'],
                'gd' => ['gd-access', 'upload-graphics'],
            ];
            
            foreach ($roleGates as $targetRole => $gates) {
                foreach ($gates as $gate) {
                    if ($targetRole === $userRole) {
                        // Should be able to access own role's gates
                        expect(Gate::allows($gate))->toBeTrue();
                    } else {
                        // Should not be able to access other roles' gates
                        expect(Gate::allows($gate))->toBeFalse();
                    }
                }
            }
        }
        
        $term->delete();
    }
});