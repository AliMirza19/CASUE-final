# Requirements Document

## Introduction

This document defines the requirements for implementing a dedicated Faculty Dashboard in the CAUSE Smart Society Management System. Faculty members (users with Registration IDs starting with BFE) who are not currently appointed as HOD or Patron will have access to a read-only dashboard to view university events, societies, and announcements.

## Glossary

- **Faculty_Member**: A user with a Registration ID starting with "BFE" and role "faculty" who is not currently appointed as HOD or Patron
- **System**: The CAUSE Smart Society Management System
- **Event**: A society event that goes through the approval workflow
- **Society**: A student organization within the university
- **Announcement**: A notification or message from HOD or Admin to users
- **Office_Bearer**: A student holding a position in a society (President, VP, etc.)
- **Active_Term**: The currently active academic term in the system

## Requirements

### Requirement 1: Faculty Role Recognition

**User Story:** As a faculty member, I want the system to recognize my BFE registration ID and assign me the correct role, so that I can access the appropriate dashboard.

#### Acceptance Criteria

1. WHEN a user with Registration ID starting with "BFE" logs in AND is not appointed as HOD or Patron for the active term, THEN THE System SHALL redirect them to the Faculty Dashboard
2. WHEN a faculty member is appointed as HOD for the active term, THEN THE System SHALL redirect them to the HOD Dashboard instead
3. WHEN a faculty member is appointed as Patron for the active term, THEN THE System SHALL redirect them to the Patron Dashboard instead
4. THE System SHALL store faculty users with role "faculty" in the database
5. WHEN bulk uploading users with BFE prefix, THEN THE System SHALL assign them the "faculty" role

### Requirement 2: Faculty Dashboard Overview

**User Story:** As a faculty member, I want to see an overview of university events on my dashboard, so that I can stay informed about society activities.

#### Acceptance Criteria

1. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display a count of approved events for the active term
2. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display a count of upcoming events (events with expected_date >= today)
3. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display the 5 most recent approved events with title, date, and venue
4. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display the count of active societies
5. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display unread announcements count

### Requirement 3: View All Events

**User Story:** As a faculty member, I want to browse all approved and upcoming events, so that I can see what activities are happening in the university.

#### Acceptance Criteria

1. WHEN a faculty member visits the events page, THEN THE System SHALL display a list of all approved events for the active term
2. WHEN displaying events, THEN THE System SHALL show event title, description, expected date, venue, and organizing team
3. WHEN a faculty member clicks on an event, THEN THE System SHALL display the full event details including budget breakdown
4. THE System SHALL allow faculty members to filter events by status (approved, upcoming, past)
5. THE System SHALL allow faculty members to search events by title or description

### Requirement 4: Society Directory

**User Story:** As a faculty member, I want to view a directory of all active societies, so that I can know which student organizations exist and who leads them.

#### Acceptance Criteria

1. WHEN a faculty member visits the societies page, THEN THE System SHALL display a list of all active societies
2. WHEN displaying societies, THEN THE System SHALL show society name, description, and patron name
3. WHEN displaying societies, THEN THE System SHALL show current office bearers (President, Vice President, etc.)
4. WHEN a faculty member clicks on a society, THEN THE System SHALL display full society details and recent events

### Requirement 5: View Announcements

**User Story:** As a faculty member, I want to view announcements from HOD and Admin, so that I can stay updated on important university communications.

#### Acceptance Criteria

1. WHEN a faculty member accesses the dashboard, THEN THE System SHALL display active announcements targeted to "faculty" or "all" roles
2. WHEN displaying announcements, THEN THE System SHALL show title, message, creator name, and creation date
3. THE System SHALL order announcements by creation date (newest first)
4. WHEN an announcement is marked inactive, THEN THE System SHALL NOT display it to faculty members

### Requirement 6: Faculty Profile Management

**User Story:** As a faculty member, I want to manage my profile and security settings, so that I can keep my information up to date.

#### Acceptance Criteria

1. WHEN a faculty member visits the profile page, THEN THE System SHALL display their current name, email, and registration ID
2. WHEN a faculty member updates their email, THEN THE System SHALL validate the email format and save the change
3. WHEN a faculty member changes their password, THEN THE System SHALL require the current password for verification
4. WHEN a faculty member changes their password, THEN THE System SHALL enforce minimum password requirements
5. IF a faculty member enters an incorrect current password, THEN THE System SHALL display an error message and reject the change

### Requirement 7: Faculty Dashboard UI/UX

**User Story:** As a faculty member, I want a professional and consistent user interface, so that I can navigate the system easily.

#### Acceptance Criteria

1. THE System SHALL display a sidebar with links: Dashboard, All Events, Societies, Profile, and Logout
2. THE System SHALL use the purple theme consistent with other dashboards
3. THE System SHALL highlight the currently active sidebar link
4. THE System SHALL display the faculty member's name and role in the header
5. THE System SHALL be responsive and work on mobile devices
