# Implementation Plan: Faculty Dashboard

## Overview

This plan implements a dedicated Faculty Dashboard for BFE users who are not appointed as HOD or Patron. The implementation follows incremental steps, building on existing Laravel infrastructure.

## Tasks

- [x] 1. Update BulkUploadController for Faculty Role
  - [x] 1.1 Modify determineRole() method to return 'faculty' for BFE prefix instead of 'patron'
    - Change line `if ($prefix === 'BFE') return 'patron';` to `return 'faculty';`
    - Update sample CSV comments to reflect faculty role
    - _Requirements: 1.4, 1.5_

- [x] 2. Update Login Redirect Logic
  - [x] 2.1 Add 'faculty' to dashboard routes array in routes/web.php root redirect
    - Add `'faculty' => 'faculty.dashboard'` to $dashboardRoutes array
    - _Requirements: 1.1_
  
  - [x] 2.2 Create FacultyRedirectMiddleware
    - Create `app/Http/Middleware/FacultyRedirectMiddleware.php`
    - Check if faculty user is appointed as HOD or Patron for active term
    - Redirect to appropriate dashboard if appointed
    - _Requirements: 1.2, 1.3_
  
  - [x] 2.3 Register middleware in bootstrap/app.php
    - Add alias 'faculty.redirect' for FacultyRedirectMiddleware
    - _Requirements: 1.2, 1.3_

- [x] 3. Create Faculty Controller and Routes
  - [x] 3.1 Create Faculty\DashboardController
    - Create `app/Http/Controllers/Faculty/DashboardController.php`
    - Implement index() method with dashboard stats
    - Implement events() method with filtering
    - Implement showEvent() method for event details
    - Implement societies() method (placeholder)
    - Implement profile() and updateProfile() methods
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 6.1, 6.2, 6.3, 6.4, 6.5_
  
  - [x] 3.2 Add Faculty routes to web.php
    - Add faculty route group with middleware
    - Define routes: dashboard, events, events/{id}, societies, profile
    - _Requirements: 7.1_

- [x] 4. Create Faculty Dashboard View
  - [x] 4.1 Create resources/views/faculty/dashboard.blade.php
    - Display approved events count card
    - Display upcoming events count card
    - Display societies count card
    - Display announcements count card
    - Show 5 most recent approved events
    - Show active announcements section
    - Include sidebar with navigation links
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 5.1, 5.2, 5.3, 7.1, 7.2, 7.3, 7.4_

- [x] 5. Create Events Listing View
  - [x] 5.1 Create resources/views/faculty/events.blade.php
    - Display all approved events in card/table format
    - Add filter dropdown (All, Upcoming, Past)
    - Add search input for title/description
    - Show event title, date, venue, team members
    - Link to event detail page
    - _Requirements: 3.1, 3.2, 3.4, 3.5, 7.1, 7.2_

- [x] 6. Create Event Detail View
  - [x] 6.1 Create resources/views/faculty/event-detail.blade.php
    - Display full event information
    - Show description, date, venue
    - Show team members
    - Show budget breakdown (items table)
    - Back button to events list
    - _Requirements: 3.3, 7.2_

- [x] 7. Create Societies View (Placeholder)
  - [x] 7.1 Create resources/views/faculty/societies.blade.php
    - Display "Coming Soon" message
    - Placeholder for future society directory feature
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 8. Create Profile View
  - [x] 8.1 Create resources/views/faculty/profile.blade.php
    - Display current user info (name, email, reg_id)
    - Email update form with validation
    - Password change form with current password verification
    - Success/error message display
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 7.1, 7.2_

- [x] 9. Update User Model
  - [x] 9.1 Add faculty helper methods to User model
    - Add isFaculty() method
    - Add isAppointedHod() method
    - Add isAppointedPatron() method
    - _Requirements: 1.1, 1.2, 1.3_

- [x] 10. Checkpoint - Test Faculty Dashboard Flow
  - Ensure all tests pass, ask the user if questions arise.
  - Test login as faculty user
  - Verify dashboard displays correct data
  - Test events listing and filtering
  - Test profile update functionality

- [ ]* 11. Write Property Tests
  - [ ]* 11.1 Write property test for role redirect logic
    - **Property 1: Faculty Role Redirect Logic**
    - **Validates: Requirements 1.1, 1.2, 1.3**
  
  - [ ]* 11.2 Write property test for BFE role assignment
    - **Property 2: BFE Users Get Faculty Role**
    - **Validates: Requirements 1.4, 1.5**
  
  - [ ]* 11.3 Write property test for dashboard event counts
    - **Property 3: Dashboard Event Counts Accuracy**
    - **Validates: Requirements 2.1, 2.2**
  
  - [ ]* 11.4 Write property test for announcement filtering
    - **Property 7: Announcements Role Filtering**
    - **Validates: Requirements 5.1, 5.4**

- [x] 12. Final Checkpoint
  - Ensure all tests pass, ask the user if questions arise.
  - Verify complete faculty dashboard functionality
  - Test edge cases (no events, no announcements)

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Society feature is a placeholder - full implementation requires Society model
- Purple theme CSS classes already exist in the project (cause-purple)
- Existing dashboard layout can be reused from other role dashboards
