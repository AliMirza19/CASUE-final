# Design Document

## Overview

This design document outlines the implementation of fully functional role-based dashboards for the CAUSE Smart Society Management System Laravel application. The system replicates the functionality of the existing PHP "cause society" project using Laravel's MVC architecture, Blade templates, and Eloquent ORM.

## Architecture

### Technology Stack
- **Framework**: Laravel 11.x
- **Database**: MySQL via Eloquent ORM
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel's built-in Auth with custom role middleware
- **Session**: Database-driven sessions

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── TermController.php
│   │   │   └── UserController.php
│   │   ├── Student/
│   │   │   ├── DashboardController.php
│   │   │   ├── EventController.php
│   │   │   └── CandidateController.php
│   │   ├── President/
│   │   │   └── DashboardController.php
│   │   ├── Patron/
│   │   │   └── DashboardController.php
│   │   ├── Hod/
│   │   │   └── DashboardController.php
│   │   ├── Sa/
│   │   │   └── DashboardController.php
│   │   ├── Vc/
│   │   │   └── DashboardController.php
│   │   └── Gd/
│   │       └── DashboardController.php
│   └── Middleware/
│       ├── CheckRole.php
│       └── CheckPasswordChanged.php
├── Models/
│   ├── User.php
│   ├── AcademicTerm.php
│   ├── Event.php
│   ├── EventItem.php
│   ├── EventGraphic.php
│   ├── EventVolunteer.php
│   ├── Budget.php
│   ├── CandidateProfile.php
│   ├── Vote.php
│   ├── ElectionSetting.php
│   ├── ActivityLog.php
│   └── Announcement.php
resources/
├── views/
│   ├── layouts/
│   │   └── dashboard.blade.php
│   ├── dashboards/
│   │   ├── admin.blade.php
│   │   ├── student.blade.php
│   │   ├── president.blade.php
│   │   ├── patron.blade.php
│   │   ├── hod.blade.php
│   │   ├── sa.blade.php
│   │   ├── vc.blade.php
│   │   └── gd.blade.php
│   ├── admin/
│   │   ├── terms/
│   │   └── users/
│   ├── student/
│   │   └── events/
│   └── components/
```

## Components and Interfaces

### 1. Dashboard Layout Component
Base layout for all dashboards with:
- Responsive sidebar navigation
- Header with user info and logout
- Flash message display
- Role-specific sidebar links

### 2. Event Workflow State Machine
```
pending_president → pending_patron → pending_hod → pending_sa → approved
       ↓                  ↓               ↓            ↓
   rejected           rejected        rejected     rejected
       ↓
revision_needed → (student edits) → pending_president
```

### 3. Role-Based Access Control
Middleware checks:
- `auth` - User is authenticated
- `check.password.changed` - User has changed default password
- `role:{role}` - User has specific role

## Data Models

### Event Status Enum
```php
'pending_president'  // Initial submission
'pending_patron'     // After president approval
'pending_hod'        // After patron approval
'pending_sa'         // After HOD approval
'approved'           // Final approval
'rejected'           // Rejected at any stage
'revision_needed'    // Sent back for student revision
'completed'          // Event completed
```

### Budget Model
```php
Budget {
    term_id: int (FK)
    total_amount: decimal(15,2)
    remaining_amount: decimal(15,2)
    is_locked: boolean
}
```

### Election Settings Model
```php
ElectionSetting {
    term_id: int (FK)
    voting_enabled: boolean
    voting_start_date: datetime
    voting_end_date: datetime
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Role-Based Access Control
*For any* authenticated user attempting to access a dashboard route, the system SHALL only allow access if the user's role matches the required role for that route.
**Validates: Requirements 12.3**

### Property 2: Event Status Progression
*For any* event, the status SHALL only transition through the defined workflow states in order (pending_president → pending_patron → pending_hod → pending_sa → approved), with rejection possible at any stage.
**Validates: Requirements 3.3, 4.2, 5.3, 6.3**

### Property 3: Budget Constraint
*For any* event approval by HOD, the event's grand_total SHALL NOT exceed the remaining budget for the term.
**Validates: Requirements 5.4**

### Property 4: Single Vote Per Term
*For any* student in a given term, the system SHALL allow at most one vote to be cast.
**Validates: Requirements 10.3**

### Property 5: Password Change Enforcement
*For any* user with password_changed=false, the system SHALL redirect to password change page before allowing dashboard access.
**Validates: Requirements 12.2**

## Error Handling

### Validation Errors
- Display inline validation errors on forms
- Use Laravel's `$errors` bag in Blade templates
- Flash error messages for failed operations

### Authorization Errors
- Redirect to `/unauthorized` page with appropriate message
- Log unauthorized access attempts

### Database Errors
- Wrap critical operations in transactions
- Display user-friendly error messages
- Log detailed errors for debugging

## Testing Strategy

### Unit Tests
- Model relationship tests
- Event status transition tests
- Budget calculation tests
- Vote uniqueness tests

### Feature Tests
- Dashboard access by role
- Event creation and approval workflow
- Budget management
- Election voting

### Property-Based Tests
- Role access control verification
- Event workflow state machine
- Budget constraint enforcement
- Vote uniqueness per term
