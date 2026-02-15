<?php

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Property Test: Authentication Success Redirects to Correct Dashboard
 * Feature: cause-society-laravel-migration, Property 1: Authentication Success Redirects to Correct Dashboard
 * 
 * For any valid user credentials (registration ID and password), authenticating should 
 * redirect the user to their role-specific dashboard based on their assigned role.
 */
test('authentication success redirects to correct dashboard', function () {
    // Run this property test 100 times with different random data
    for ($i = 0; $i < 100; $i++) {
        // Create an academic term
        $term = AcademicTerm::factory()->create();
        
        // Test each role
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        
        foreach ($roles as $role) {
            // Create a user with a known password
            $password = 'test123456';
            $user = User::factory()->create([
                'role' => $role,
                'password' => Hash::make($password),
                'password_changed' => true, // Skip password change requirement
                'current_term_id' => $term->id,
            ]);
            
            // Attempt login with valid credentials
            $response = $this->post('/login', [
                'reg_id' => $user->reg_id,
                'password' => $password,
            ]);
            
            // Should redirect to appropriate dashboard
            $expectedRoutes = [
                'admin' => '/admin/dashboard',
                'hod' => '/hod/dashboard',
                'patron' => '/patron/dashboard',
                'president' => '/president/dashboard',
                'student' => '/student/dashboard',
                'sa' => '/sa/dashboard',
                'vc' => '/vc/dashboard',
                'gd' => '/gd/dashboard',
            ];
            
            $expectedRoute = $expectedRoutes[$role];
            $response->assertRedirect($expectedRoute);
            
            // User should be authenticated
            $this->assertAuthenticatedAs($user);
            
            // Logout for next iteration
            $this->post('/logout');
            $this->assertGuest();
        }
        
        // Clean up for next iteration
        User::query()->delete();
        AcademicTerm::query()->delete();
    }
});

/**
 * Property Test: Invalid Credentials Are Rejected
 * Feature: cause-society-laravel-migration, Property 2: Invalid Credentials Are Rejected
 * 
 * For any invalid credentials (wrong registration ID or password), authentication 
 * attempts should be rejected with appropriate error messages and remain on the login page.
 */
test('invalid credentials are rejected', function () {
    for ($i = 0; $i < 100; $i++) {
        $term = AcademicTerm::factory()->create();
        
        // Create a valid user
        $validUser = User::factory()->create([
            'password' => Hash::make('correctpassword'),
            'current_term_id' => $term->id,
        ]);
        
        // Test invalid registration ID
        $response = $this->post('/login', [
            'reg_id' => 'INVALID-999',
            'password' => 'correctpassword',
        ]);
        
        $response->assertSessionHasErrors(['reg_id']);
        $this->assertGuest();
        
        // Test invalid password
        $response = $this->post('/login', [
            'reg_id' => $validUser->reg_id,
            'password' => 'wrongpassword',
        ]);
        
        $response->assertSessionHasErrors(['reg_id']);
        $this->assertGuest();
        
        // Test both invalid
        $response = $this->post('/login', [
            'reg_id' => 'INVALID-999',
            'password' => 'wrongpassword',
        ]);
        
        $response->assertSessionHasErrors(['reg_id']);
        $this->assertGuest();
        
        // Clean up
        $term->delete();
    }
});

/**
 * Property Test: First-Time Login Forces Password Change
 * Feature: cause-society-laravel-migration, Property 3: First-Time Login Forces Password Change
 * 
 * For any user with password_changed=false, login attempts should redirect to 
 * password change page before allowing dashboard access.
 */
test('first-time login forces password change', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        // Create user who needs to change password
        $password = 'defaultpass123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
            'password_changed' => false, // First-time user
            'current_term_id' => $term->id,
        ]);
        
        // Login with valid credentials
        $response = $this->post('/login', [
            'reg_id' => $user->reg_id,
            'password' => $password,
        ]);
        
        // Should redirect to password change page
        $response->assertRedirect('/change-password');
        
        // User should be authenticated
        $this->assertAuthenticatedAs($user);
        
        // Accessing dashboard should redirect to password change
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
        
        $dashboardRoute = $dashboardRoutes[$user->role];
        $response = $this->get($dashboardRoute);
        $response->assertRedirect('/change-password');
        
        // After changing password, should be able to access dashboard
        $newPassword = 'newpassword123';
        $response = $this->post('/change-password', [
            'current_password' => $password,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        
        // Should redirect to dashboard after password change
        $response->assertRedirect($dashboardRoute);
        
        // User should have password_changed = true
        $user->refresh();
        expect($user->password_changed)->toBeTrue();
        
        // Should now be able to access dashboard directly
        $response = $this->get($dashboardRoute);
        $response->assertOk();
        
        // Logout for cleanup
        $this->post('/logout');
        $term->delete();
    }
});

/**
 * Property Test: Password Change Validation
 * Tests that password change requires correct current password and valid new password
 */
test('password change validation works correctly', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $currentPassword = 'currentpass123';
        $user = User::factory()->create([
            'password' => Hash::make($currentPassword),
            'password_changed' => false,
            'current_term_id' => $term->id,
        ]);
        
        // Login user
        $this->actingAs($user);
        
        // Test wrong current password
        $response = $this->post('/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        
        $response->assertSessionHasErrors(['current_password']);
        
        // Test password confirmation mismatch
        $response = $this->post('/change-password', [
            'current_password' => $currentPassword,
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);
        
        $response->assertSessionHasErrors(['password']);
        
        // Test same password as current
        $response = $this->post('/change-password', [
            'current_password' => $currentPassword,
            'password' => $currentPassword,
            'password_confirmation' => $currentPassword,
        ]);
        
        $response->assertSessionHasErrors(['password']);
        
        // Test valid password change
        $newPassword = 'validnewpass123';
        $response = $this->post('/change-password', [
            'current_password' => $currentPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        
        $response->assertRedirect();
        
        // Verify password was changed
        $user->refresh();
        expect($user->password_changed)->toBeTrue();
        expect(Hash::check($newPassword, $user->password))->toBeTrue();
        
        // Logout and cleanup
        $this->post('/logout');
        $term->delete();
    }
});

/**
 * Property Test: Session Security Management
 * Tests that logout properly destroys sessions and prevents further access
 */
test('session security management works correctly', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        $user = User::factory()->create([
            'password_changed' => true,
            'current_term_id' => $term->id,
        ]);
        
        // Login user
        $this->actingAs($user);
        
        // Should be able to access dashboard
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
        
        $dashboardRoute = $dashboardRoutes[$user->role];
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
        
        // Clean up
        $term->delete();
    }
});

/**
 * Property Test: Role-Based Dashboard Access
 * Tests that users can only access their role-specific dashboard
 */
test('users can only access their role-specific dashboard', function () {
    for ($i = 0; $i < 20; $i++) {
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
        
        foreach ($roles as $userRole) {
            $user = User::factory()->create([
                'role' => $userRole,
                'password_changed' => true,
                'current_term_id' => $term->id,
            ]);
            
            $this->actingAs($user);
            
            // Should be able to access own dashboard
            $ownDashboard = $dashboardRoutes[$userRole];
            $response = $this->get($ownDashboard);
            $response->assertOk();
            
            // Should not be able to access other dashboards
            foreach ($dashboardRoutes as $role => $route) {
                if ($role !== $userRole) {
                    $response = $this->get($route);
                    $response->assertRedirect('/unauthorized');
                }
            }
            
            $this->post('/logout');
        }
        
        $term->delete();
    }
});