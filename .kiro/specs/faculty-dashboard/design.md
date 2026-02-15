# Design Document: Faculty Dashboard

## Overview

This document describes the technical design for implementing a dedicated Faculty Dashboard in the CAUSE Smart Society Management System. The dashboard provides faculty members (BFE users not appointed as HOD/Patron) with read-only access to view university events, societies, and announcements.

## Architecture

The Faculty Dashboard follows the existing Laravel MVC architecture pattern used throughout the application:

```
┌─────────────────────────────────────────────────────────────────┐
│                        Browser (Client)                          │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Laravel Routes (web.php)                     │
│                   /faculty/* routes group                        │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Middleware Stack                              │
│  auth → check.password.changed → role:faculty → FacultyRedirect │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│              Faculty\DashboardController                         │
│  - index()      - events()     - societies()                    │
│  - showEvent()  - profile()    - updateProfile()                │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Eloquent Models                             │
│  User, Event, Announcement, RoleAssignment, AcademicTerm        │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      MySQL Database                              │
│  users, events, announcements, role_assignments, academic_terms │
└─────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. FacultyRedirectMiddleware

A new middleware to handle faculty role redirection based on role assignments.

```php
class FacultyRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user->role !== 'faculty') {
            return $next($request);
        }
        
        $activeTerm = AcademicTerm::getActive();
        
        if ($activeTerm) {
            // Check if appointed as HOD
            $hodAssignment = RoleAssignment::getCurrentHod($activeTerm->id);
            if ($hodAssignment && $hodAssignment->user_id === $user->id) {
                return redirect()->route('hod.dashboard');
            }
            
            // Check if appointed as Patron
            $patronAssignment = RoleAssignment::getCurrentPatron($activeTerm->id);
            if ($patronAssignment && $patronAssignment->user_id === $user->id) {
                return redirect()->route('patron.dashboard');
            }
        }
        
        return $next($request);
    }
}
```

### 2. Faculty\DashboardController

Main controller handling all faculty dashboard functionality.

```php
interface FacultyDashboardInterface
{
    // Dashboard overview with stats and recent events
    public function index(): View;
    
    // List all approved events with filtering
    public function events(Request $request): View;
    
    // Show single event details
    public function showEvent(int $id): View;
    
    // List all societies (placeholder for future)
    public function societies(): View;
    
    // Show profile page
    public function profile(): View;
    
    // Update profile (email/password)
    public function updateProfile(Request $request): RedirectResponse;
}
```

### 3. Route Definitions

```php
Route::middleware('role:faculty')->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/events', [DashboardController::class, 'events'])->name('events');
    Route::get('/events/{id}', [DashboardController::class, 'showEvent'])->name('events.show');
    Route::get('/societies', [DashboardController::class, 'societies'])->name('societies');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
});
```

### 4. View Components

```
resources/views/faculty/
├── dashboard.blade.php      # Main dashboard with stats
├── events.blade.php         # Events listing with filters
├── event-detail.blade.php   # Single event view
├── societies.blade.php      # Societies directory
└── profile.blade.php        # Profile management
```

## Data Models

### User Model Updates

Add helper method to check if user is faculty:

```php
public function isFaculty(): bool
{
    return $this->role === 'faculty';
}

public function isAppointedHod(): bool
{
    $activeTerm = AcademicTerm::getActive();
    if (!$activeTerm) return false;
    
    $assignment = RoleAssignment::getCurrentHod($activeTerm->id);
    return $assignment && $assignment->user_id === $this->id;
}

public function isAppointedPatron(): bool
{
    $activeTerm = AcademicTerm::getActive();
    if (!$activeTerm) return false;
    
    $assignment = RoleAssignment::getCurrentPatron($activeTerm->id);
    return $assignment && $assignment->user_id === $this->id;
}
```

### BulkUploadController Update

Modify role determination to use 'faculty' instead of 'patron' for BFE users:

```php
private function determineRole(string $regId): ?string
{
    if (strlen($regId) !== 9) return null;
    
    $prefix = substr($regId, 0, 3);
    $numbers = substr($regId, 3);
    
    if (!ctype_digit($numbers)) return null;
    
    if ($prefix === 'BSE') return 'student';
    if ($prefix === 'BFE') return 'faculty';  // Changed from 'patron'
    
    return null;
}
```

### Dashboard Data Queries

```php
// Approved events count
$approvedCount = Event::where('term_id', $activeTerm->id)
    ->where('status', 'approved')
    ->count();

// Upcoming events (approved with future date)
$upcomingCount = Event::where('term_id', $activeTerm->id)
    ->where('status', 'approved')
    ->where('expected_date', '>=', now())
    ->count();

// Recent approved events
$recentEvents = Event::where('term_id', $activeTerm->id)
    ->where('status', 'approved')
    ->orderBy('expected_date', 'desc')
    ->limit(5)
    ->get();

// Active announcements for faculty
$announcements = Announcement::getActiveForRole('faculty');
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Faculty Role Redirect Logic

*For any* faculty user logging into the system, if they are appointed as HOD for the active term, they SHALL be redirected to the HOD dashboard; if appointed as Patron, they SHALL be redirected to the Patron dashboard; otherwise they SHALL access the Faculty dashboard.

**Validates: Requirements 1.1, 1.2, 1.3**

### Property 2: BFE Users Get Faculty Role

*For any* user created via bulk upload with a Registration ID starting with "BFE" followed by 6 digits, the system SHALL assign them the "faculty" role in the database.

**Validates: Requirements 1.4, 1.5**

### Property 3: Dashboard Event Counts Accuracy

*For any* faculty dashboard view, the displayed approved events count SHALL equal the actual count of events with status "approved" for the active term, and the upcoming events count SHALL equal the count of approved events with expected_date >= today.

**Validates: Requirements 2.1, 2.2**

### Property 4: Recent Events Limit

*For any* faculty dashboard view, the recent events section SHALL display at most 5 events, ordered by expected_date descending.

**Validates: Requirements 2.3**

### Property 5: Events List Shows Only Approved

*For any* events listing page accessed by faculty, all displayed events SHALL have status "approved" and belong to the active term.

**Validates: Requirements 3.1**

### Property 6: Event Search Results Relevance

*For any* search query on the events page, all returned events SHALL contain the search term in either title or description fields.

**Validates: Requirements 3.5**

### Property 7: Announcements Role Filtering

*For any* announcement displayed to faculty users, the announcement SHALL be active (is_active = true) AND target_roles SHALL either be null/empty OR contain "faculty".

**Validates: Requirements 5.1, 5.4**

### Property 8: Announcements Ordering

*For any* list of announcements displayed to faculty, the announcements SHALL be ordered by created_at descending (newest first).

**Validates: Requirements 5.3**

### Property 9: Email Validation on Profile Update

*For any* email update request, if the provided email does not match valid email format, the system SHALL reject the update and return a validation error.

**Validates: Requirements 6.2**

### Property 10: Password Change Requires Current Password

*For any* password change request, if the provided current password does not match the user's actual password, the system SHALL reject the change and display an error message.

**Validates: Requirements 6.3, 6.5**

## Error Handling

### Authentication Errors
- Unauthenticated users → Redirect to login page
- Wrong role → Redirect to unauthorized page
- Faculty appointed as HOD/Patron → Redirect to appropriate dashboard

### Data Errors
- No active term → Display "No active term" message
- No events found → Display "No events yet" empty state
- No announcements → Display "No announcements" message

### Profile Update Errors
- Invalid email format → Display validation error
- Email already exists → Display "Email already taken" error
- Wrong current password → Display "Current password is incorrect" error
- Password too short → Display minimum length requirement error

## Testing Strategy

### Unit Tests
Unit tests verify specific examples and edge cases:

1. **FacultyRedirectMiddleware Tests**
   - Test faculty without appointment accesses faculty dashboard
   - Test faculty with HOD appointment redirects to HOD dashboard
   - Test faculty with Patron appointment redirects to Patron dashboard

2. **DashboardController Tests**
   - Test dashboard loads with correct data
   - Test events page with filters
   - Test profile update validation

3. **BulkUpload Role Assignment Tests**
   - Test BFE prefix assigns faculty role
   - Test BSE prefix assigns student role

### Property-Based Tests
Property tests verify universal properties across all inputs using a PBT library (Pest with faker for PHP):

1. **Property Test: Role Redirect Logic**
   - Generate random faculty users with/without role assignments
   - Verify redirect destination matches expected dashboard
   - **Feature: faculty-dashboard, Property 1: Faculty Role Redirect Logic**

2. **Property Test: BFE Role Assignment**
   - Generate random BFE registration IDs
   - Verify all get assigned faculty role
   - **Feature: faculty-dashboard, Property 2: BFE Users Get Faculty Role**

3. **Property Test: Event Counts**
   - Generate random events with various statuses
   - Verify dashboard counts match actual database counts
   - **Feature: faculty-dashboard, Property 3: Dashboard Event Counts Accuracy**

4. **Property Test: Announcement Filtering**
   - Generate announcements with various target_roles
   - Verify only appropriate ones shown to faculty
   - **Feature: faculty-dashboard, Property 7: Announcements Role Filtering**

5. **Property Test: Email Validation**
   - Generate random invalid email strings
   - Verify all are rejected by validation
   - **Feature: faculty-dashboard, Property 9: Email Validation on Profile Update**

### Test Configuration
- Minimum 100 iterations per property test
- Use database transactions for test isolation
- Mock external services if any
