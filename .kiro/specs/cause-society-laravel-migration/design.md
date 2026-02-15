# Design Document

## Overview

This document outlines the design for migrating the CAUSE Smart Society Management System from core PHP to Laravel framework. The migration will preserve all existing functionality while modernizing the codebase to use Laravel's MVC architecture, Eloquent ORM, and built-in security features.

The system manages society events, elections, budget tracking, and multi-role user management for an educational institution. It supports eight distinct user roles with role-based dashboards and complex approval workflows.

## Architecture

### Framework Architecture
- **Framework**: Laravel 11.x with PHP 8.2+
- **Architecture Pattern**: Model-View-Controller (MVC)
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel's built-in authentication system
- **Authorization**: Laravel Gates and Policies
- **Frontend**: Blade templating with Tailwind CSS
- **Session Management**: Laravel's session handling

### Application Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Admin/
│   │   ├── Student/
│   │   ├── HOD/
│   │   └── ...
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
├── Providers/
└── Services/
```

### Database Architecture
- **Primary Database**: MySQL (cause_db)
- **ORM**: Laravel Eloquent
- **Migrations**: Laravel migration system
- **Relationships**: Eloquent relationships for data integrity
- **Indexing**: Proper database indexing for performance

## Components and Interfaces

### Authentication System

#### User Model
```php
class User extends Authenticatable
{
    protected $fillable = [
        'reg_id', 'name', 'email', 'password', 'role', 
        'password_changed', 'current_term_id'
    ];
    
    // Relationships
    public function currentTerm(): BelongsTo
    public function events(): HasMany
    public function candidateProfile(): HasOne
    public function activityLogs(): HasMany
}
```

#### Role-Based Authentication
- **Middleware**: Custom middleware for each role (AdminMiddleware, StudentMiddleware, etc.)
- **Guards**: Default web guard with session-based authentication
- **Policies**: Model-specific authorization policies
- **Gates**: Action-based authorization gates

### Event Management System

#### Event Model
```php
class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'student_id', 'term_id',
        'expected_date', 'venue', 'grand_total', 'status',
        'team_member_1', 'team_member_2', 'team_member_3',
        'rejection_reason'
    ];
    
    // Relationships
    public function student(): BelongsTo
    public function term(): BelongsTo
    public function items(): HasMany
    public function graphics(): HasMany
    public function volunteers(): HasMany
}
```

#### Event Workflow Service
```php
class EventWorkflowService
{
    public function submitEvent(array $data): Event
    public function approveEvent(Event $event, User $approver): bool
    public function rejectEvent(Event $event, User $approver, string $reason): bool
    public function getNextApprover(Event $event): ?string
    public function notifyNextApprover(Event $event): void
}
```

### Budget Management System

#### Budget Model
```php
class Budget extends Model
{
    protected $fillable = [
        'term_id', 'total_amount', 'remaining_amount', 'is_locked'
    ];
    
    public function term(): BelongsTo
    public function deductAmount(float $amount): bool
    public function addAmount(float $amount): bool
}
```

#### Budget Service
```php
class BudgetService
{
    public function setBudget(AcademicTerm $term, float $amount): Budget
    public function checkAvailability(float $amount, AcademicTerm $term): bool
    public function deductEventCost(Event $event): bool
    public function getBudgetStatus(AcademicTerm $term): array
}
```

### Election Management System

#### Candidate Profile Model
```php
class CandidateProfile extends Model
{
    protected $fillable = [
        'student_id', 'manifesto', 'photo_url', 'experience',
        'vp_name', 'status', 'patron_feedback'
    ];
    
    public function student(): BelongsTo
    public function votes(): HasMany
}
```

#### Election Service
```php
class ElectionService
{
    public function submitCandidacy(User $student, array $data): CandidateProfile
    public function enableVoting(AcademicTerm $term, array $settings): ElectionSetting
    public function castVote(User $voter, CandidateProfile $candidate): Vote
    public function getResults(AcademicTerm $term): array
}
```

### Dashboard System

#### Dashboard Controllers
- **AdminController**: System administration and user management
- **StudentController**: Event submission and election participation
- **HODController**: Budget management and event approvals
- **PatronController**: Event and candidate approvals
- **PresidentController**: Initial event approvals
- **SAController**: Final event approvals and system coordination
- **VCController**: Volunteer coordination
- **GDController**: Graphics design and upload

#### Dashboard Service
```php
class DashboardService
{
    public function getStatsForRole(User $user): array
    public function getRecentActivity(User $user): Collection
    public function getQuickActions(User $user): array
    public function getNotifications(User $user): Collection
}
```

## Data Models

### Core Models

#### Academic Term
```php
class AcademicTerm extends Model
{
    protected $fillable = ['term_name', 'status', 'start_date', 'end_date'];
    
    public function users(): HasMany
    public function events(): HasMany
    public function budget(): HasOne
    public function electionSetting(): HasOne
    public function votes(): HasMany
}
```

#### Event Item
```php
class EventItem extends Model
{
    protected $fillable = [
        'event_id', 'item_name', 'quantity', 'unit_rate',
        'total_amount', 'is_approved_by_patron', 'patron_comment'
    ];
    
    public function event(): BelongsTo
}
```

#### Event Graphics
```php
class EventGraphic extends Model
{
    protected $fillable = [
        'event_id', 'gd_id', 'design_category', 'image_path',
        'image_link', 'status', 'patron_feedback'
    ];
    
    public function event(): BelongsTo
    public function designer(): BelongsTo
}
```

#### Activity Log
```php
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_role', 'action_text', 'related_event_id'
    ];
    
    public function user(): BelongsTo
    public function event(): BelongsTo
}
```

### Database Relationships

#### User Relationships
- User hasMany Events (as student)
- User hasOne CandidateProfile
- User hasMany ActivityLogs
- User belongsTo AcademicTerm (current term)

#### Event Relationships
- Event belongsTo User (student)
- Event belongsTo AcademicTerm
- Event hasMany EventItems
- Event hasMany EventGraphics
- Event hasMany EventVolunteers

#### Election Relationships
- CandidateProfile belongsTo User (student)
- Vote belongsTo User (voter)
- Vote belongsTo CandidateProfile
- Vote belongsTo AcademicTerm

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Authentication Success Redirects to Correct Dashboard
*For any* valid user credentials (registration ID and password), authenticating should redirect the user to their role-specific dashboard based on their assigned role.
**Validates: Requirements 1.1**

### Property 2: Invalid Credentials Are Rejected
*For any* invalid credentials (wrong registration ID or password), authentication attempts should be rejected with appropriate error messages and remain on the login page.
**Validates: Requirements 1.2**

### Property 3: First-Time Login Forces Password Change
*For any* user with password_changed=false, login attempts should redirect to password change page before allowing dashboard access.
**Validates: Requirements 1.3**

### Property 4: Role-Based Access Control
*For any* user role and system functionality combination, unauthorized access attempts should be blocked and redirect to unauthorized page.
**Validates: Requirements 1.4, 1.5**

### Property 5: Session Security Management
*For any* authenticated user session, logout should properly destroy the session and prevent further access without re-authentication.
**Validates: Requirements 1.6**

### Property 6: Database Relationship Integrity
*For any* database operation involving foreign keys, the system should enforce referential integrity and prevent orphaned records.
**Validates: Requirements 2.2**

### Property 7: Event Workflow Progression
*For any* event in the approval workflow, approvals should progress through the correct sequence: pending_president → pending_patron → pending_hod → pending_sa → approved.
**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**

### Property 8: Event Rejection Handling
*For any* event at any approval stage, rejection should set status to "rejected" and record the rejection reason.
**Validates: Requirements 3.6**

### Property 9: Event Item Calculation Accuracy
*For any* event with items, the total amount should equal the sum of (quantity × unit_rate) for all items.
**Validates: Requirements 3.8**

### Property 10: Budget Deduction on Approval
*For any* approved event, the event's grand total should be deducted from the term's remaining budget.
**Validates: Requirements 4.2**

### Property 11: Budget Validation Prevents Over-spending
*For any* event approval attempt, if the event cost exceeds remaining budget, the approval should be prevented with appropriate warning.
**Validates: Requirements 4.3**

### Property 12: Budget Status Display Accuracy
*For any* budget-related dashboard, displayed budget information should match actual database values for total and remaining amounts.
**Validates: Requirements 4.4**

### Property 13: Voting Integrity - No Duplicate Votes
*For any* student and academic term combination, only one vote should be allowed per student per term.
**Validates: Requirements 5.5**

### Property 14: Voting Period Access Control
*For any* voting attempt, votes should only be accepted during active voting periods as defined by election settings.
**Validates: Requirements 5.4**

### Property 15: Graphics Approval Workflow
*For any* uploaded graphic, it should require patron approval before being visible to students.
**Validates: Requirements 6.2**

### Property 16: Role-Specific Dashboard Content
*For any* user role, the dashboard should display content and actions appropriate only to that role.
**Validates: Requirements 7.1, 7.2, 7.3**

### Property 17: Activity Logging Completeness
*For any* significant user action, an activity log entry should be created with user ID, role, action description, and timestamp.
**Validates: Requirements 8.1, 8.2**

### Property 18: Term Association Requirement
*For any* system activity (events, votes, budgets), it should be associated with a valid academic term.
**Validates: Requirements 9.3**

### Property 19: Active Term Requirement
*For any* term-dependent operation, it should be prevented when no active academic term exists.
**Validates: Requirements 9.4**

### Property 20: Error Handling and Logging
*For any* system error or exception, it should be properly caught, logged, and handled gracefully without exposing sensitive information.
**Validates: Requirements 10.7**

## Error Handling

### Exception Handling Strategy
- **Global Exception Handler**: Laravel's exception handler will catch and log all exceptions
- **Custom Exception Classes**: Domain-specific exceptions for business logic errors
- **Validation Errors**: Laravel's form request validation with custom error messages
- **Database Errors**: Eloquent exceptions handled with user-friendly messages
- **Authentication Errors**: Proper handling of login failures and session timeouts

### Error Response Format
```php
// API Error Response
{
    "success": false,
    "message": "User-friendly error message",
    "errors": {
        "field": ["Validation error details"]
    }
}

// Web Error Response
// Redirect with flash messages for form errors
// Custom error pages for 404, 403, 500 errors
```

### Logging Strategy
- **Application Logs**: All significant actions and errors
- **Security Logs**: Authentication attempts and authorization failures
- **Performance Logs**: Slow queries and performance bottlenecks
- **Audit Logs**: User activities stored in activity_logs table

## Testing Strategy

### Dual Testing Approach
The system will use both unit testing and property-based testing to ensure comprehensive coverage:

- **Unit Tests**: Verify specific examples, edge cases, and error conditions
- **Property Tests**: Verify universal properties across all inputs using randomized testing
- Both approaches are complementary and necessary for comprehensive coverage

### Unit Testing Focus Areas
- **Authentication Flow**: Login, logout, password change scenarios
- **Event Workflow**: Specific approval and rejection scenarios
- **Budget Calculations**: Specific budget deduction and validation cases
- **Election Process**: Voting and candidate management scenarios
- **Database Operations**: Model relationships and data integrity
- **API Endpoints**: Request/response validation and error handling

### Property-Based Testing Configuration
- **Testing Framework**: Laravel's built-in PHPUnit with custom property test helpers
- **Minimum Iterations**: 100 iterations per property test
- **Test Tagging**: Each property test tagged with format: **Feature: cause-society-laravel-migration, Property {number}: {property_text}**
- **Data Generation**: Custom factories for generating test data (users, events, budgets, etc.)

### Property Test Implementation
Each correctness property will be implemented as a property-based test that:
1. Generates random valid inputs using Laravel factories
2. Executes the system operation
3. Verifies the expected property holds true
4. Reports any counterexamples for debugging

### Test Data Management
- **Database Transactions**: Each test runs in a transaction that's rolled back
- **Factory Classes**: Laravel factories for generating realistic test data
- **Seeders**: Consistent test data setup for integration tests
- **Test Database**: Separate test database configuration

### Continuous Integration
- **Automated Testing**: All tests run on every commit
- **Code Coverage**: Minimum 80% code coverage requirement
- **Performance Testing**: Database query performance monitoring
- **Security Testing**: Automated security vulnerability scanning

## Implementation Notes

### Migration Strategy
1. **Phase 1**: Database migration and basic Laravel setup
2. **Phase 2**: Authentication and user management
3. **Phase 3**: Event management workflow
4. **Phase 4**: Budget and election systems
5. **Phase 5**: Dashboard and UI implementation
6. **Phase 6**: Testing and optimization

### Data Migration Plan
- **Export Existing Data**: SQL dumps from current PHP application
- **Transform Data**: Convert to Laravel-compatible format
- **Import via Seeders**: Use Laravel seeders for data import
- **Validation**: Verify data integrity after migration

### Security Considerations
- **Password Hashing**: Laravel's bcrypt hashing (already compatible)
- **CSRF Protection**: Laravel's built-in CSRF middleware
- **SQL Injection Prevention**: Eloquent ORM parameter binding
- **XSS Prevention**: Blade template escaping
- **Session Security**: Secure session configuration
- **File Upload Security**: Validation and sanitization for graphics uploads

### Performance Optimization
- **Database Indexing**: Proper indexes on frequently queried columns
- **Query Optimization**: Eager loading for relationships
- **Caching Strategy**: Laravel's cache system for frequently accessed data
- **Asset Optimization**: Laravel Mix for CSS/JS compilation
- **Database Connection Pooling**: Optimized database connections
