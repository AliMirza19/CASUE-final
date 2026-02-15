# Requirements Document

## Introduction

This document defines the requirements for implementing all role-based dashboards in the CAUSE Smart Society Management System Laravel application. The system manages university society events, elections, and user management with 8 distinct user roles.

## Glossary

- **System**: The CAUSE Smart Society Management System Laravel application
- **Dashboard**: Role-specific landing page with relevant statistics and actions
- **Event**: A society event proposal submitted by students for approval
- **Term**: An academic term/semester during which events are organized
- **Approval_Workflow**: The multi-stage approval process for events (Student → President → Patron → HOD → SA)
- **Budget**: Financial allocation for events within a term
- **Election**: Society president election system
- **Candidate_Profile**: Student's election candidacy submission

## Requirements

### Requirement 1: Admin Dashboard

**User Story:** As an admin, I want to manage academic terms, users, and view system-wide statistics, so that I can oversee the entire society management system.

#### Acceptance Criteria

1. WHEN an admin logs in, THE System SHALL display the admin dashboard with term selection dropdown
2. WHEN an admin selects a term, THE System SHALL show term-specific statistics (events count, budget spent, pending/approved events)
3. THE System SHALL display total terms count, current active term name, and current HOD name
4. WHEN the active term is expired, THE System SHALL show an alert prompting term management
5. THE System SHALL provide quick action links to: Manage Terms, View All Events, Bulk Upload Users
6. WHEN an admin clicks Manage Terms, THE System SHALL allow creating, activating, and completing academic terms
7. WHEN an admin uploads a CSV file, THE System SHALL bulk create user accounts

### Requirement 2: Student Dashboard

**User Story:** As a student, I want to submit event proposals and participate in elections, so that I can contribute to society activities.

#### Acceptance Criteria

1. WHEN a student logs in, THE System SHALL display their event submission statistics (total, approved, pending)
2. WHEN the system is inactive (no budget set), THE System SHALL disable event submission and show a warning
3. WHEN voting is enabled and active, THE System SHALL display a prominent voting call-to-action
4. IF the student has voted, THEN THE System SHALL show "View Results" instead of "Vote Now"
5. THE System SHALL display candidate profile status if the student has submitted one
6. THE System SHALL provide quick actions: Request New Event, View My Events
7. THE System SHALL display recent activity log entries

### Requirement 3: President Dashboard

**User Story:** As a society president, I want to review and approve/reject event proposals, so that I can ensure quality events are forwarded for further approval.

#### Acceptance Criteria

1. WHEN a president logs in, THE System SHALL display counts of pending reviews, awaiting revisions, and approved events
2. THE System SHALL list all events with status 'pending_president' for review
3. WHEN reviewing an event, THE System SHALL allow approving or requesting revisions with comments
4. THE System SHALL display events sent back for student revision
5. THE System SHALL display approved events awaiting student forwarding to Patron

### Requirement 4: Patron Dashboard

**User Story:** As a patron, I want to review events, candidate profiles, and graphics designs, so that I can ensure quality and appropriateness.

#### Acceptance Criteria

1. WHEN a patron logs in, THE System SHALL display counts of pending event reviews, candidate reviews, and graphics reviews
2. THE System SHALL list events with status 'pending_patron' for review
3. WHEN reviewing an event, THE System SHALL allow approving/rejecting individual budget items with comments
4. THE System SHALL allow reviewing and approving/rejecting candidate profiles
5. THE System SHALL allow reviewing and approving/rejecting graphics designs

### Requirement 5: HOD Dashboard

**User Story:** As an HOD, I want to manage term budgets and give final approval to events, so that I can control society finances.

#### Acceptance Criteria

1. WHEN an HOD logs in, THE System SHALL display budget overview and pending approvals count
2. THE System SHALL allow setting and locking term budget
3. THE System SHALL list events with status 'pending_hod' for final approval
4. WHEN approving an event, THE System SHALL deduct the event budget from remaining term budget
5. THE System SHALL display analytics: events by status, budget utilization

### Requirement 6: SA (Student Affairs) Dashboard

**User Story:** As SA staff, I want to review HOD-approved events, so that I can give final institutional approval.

#### Acceptance Criteria

1. WHEN SA logs in, THE System SHALL display pending SA reviews count
2. THE System SHALL list events with status 'pending_sa' for review
3. WHEN SA approves an event, THE System SHALL change status to 'approved'
4. THE System SHALL display all approved events for the current term

### Requirement 7: VC (Volunteer Coordinator) Dashboard

**User Story:** As a volunteer coordinator, I want to assign volunteers to approved events, so that events have adequate support.

#### Acceptance Criteria

1. WHEN VC logs in, THE System SHALL display approved events needing volunteer assignment
2. THE System SHALL allow adding volunteers with name, contact, and role description
3. THE System SHALL display volunteer assignments per event

### Requirement 8: GD (Graphics Designer) Dashboard

**User Story:** As a graphics designer, I want to upload designs for approved events, so that events have promotional materials.

#### Acceptance Criteria

1. WHEN GD logs in, THE System SHALL display approved events needing graphics
2. THE System SHALL allow uploading designs with category (poster, banner, social_media)
3. THE System SHALL display design approval status from Patron

### Requirement 9: Event Request System

**User Story:** As a student, I want to submit detailed event proposals with budget items, so that my event can be reviewed and approved.

#### Acceptance Criteria

1. WHEN a student submits an event, THE System SHALL require: title, description, expected date, venue
2. THE System SHALL allow adding multiple budget items with name, quantity, unit rate
3. THE System SHALL calculate grand total from budget items
4. WHEN submitted, THE System SHALL set status to 'pending_president'
5. THE System SHALL allow adding up to 3 team members by registration ID

### Requirement 10: Election System

**User Story:** As a student, I want to vote for society president candidates, so that I can participate in society governance.

#### Acceptance Criteria

1. WHEN voting is enabled by admin, THE System SHALL display voting portal to students
2. THE System SHALL only allow voting within the configured voting period
3. THE System SHALL ensure each student can vote only once per term
4. THE System SHALL display approved candidates with manifesto and photo
5. WHEN voting ends, THE System SHALL display election results

### Requirement 11: Activity Logging

**User Story:** As a user, I want to see activity history, so that I can track actions taken on events and the system.

#### Acceptance Criteria

1. WHEN any significant action occurs, THE System SHALL log it with user, role, action text, and timestamp
2. THE System SHALL display relevant activity logs on each dashboard
3. THE System SHALL allow filtering activity logs by user role

### Requirement 12: Authentication & Authorization

**User Story:** As a user, I want secure role-based access, so that I can only access features relevant to my role.

#### Acceptance Criteria

1. THE System SHALL authenticate users by registration ID and password
2. WHEN a new user logs in for the first time, THE System SHALL require password change
3. THE System SHALL restrict dashboard access based on user role
4. THE System SHALL maintain session security with CSRF protection
