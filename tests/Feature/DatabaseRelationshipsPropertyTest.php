<?php

use App\Models\AcademicTerm;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Budget;
use App\Models\CandidateProfile;
use App\Models\ElectionSetting;
use App\Models\Event;
use App\Models\EventGraphic;
use App\Models\EventItem;
use App\Models\EventVolunteer;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Property Test: Database Relationship Integrity
 * Feature: cause-society-laravel-migration, Property 6: Database Relationship Integrity
 * 
 * For any database operation involving foreign keys, the system should enforce 
 * referential integrity and prevent orphaned records.
 */
test('database relationships maintain referential integrity', function () {
    // Run this property test 100 times with different random data
    for ($i = 0; $i < 100; $i++) {
        // Create a random academic term
        $term = AcademicTerm::factory()->create();
        
        // Create random users with different roles
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        $users = collect();
        
        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'current_term_id' => $term->id,
            ]);
            $users->push($user);
        }
        
        $student = $users->where('role', 'student')->first();
        $gd = $users->where('role', 'gd')->first();
        $vc = $users->where('role', 'vc')->first();
        
        // Test User -> AcademicTerm relationship
        expect($student->currentTerm)->toBeInstanceOf(AcademicTerm::class);
        expect($student->currentTerm->id)->toBe($term->id);
        
        // Create an event and test Event -> User and Event -> AcademicTerm relationships
        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
        ]);
        
        expect($event->student)->toBeInstanceOf(User::class);
        expect($event->student->id)->toBe($student->id);
        expect($event->term)->toBeInstanceOf(AcademicTerm::class);
        expect($event->term->id)->toBe($term->id);
        
        // Test reverse relationships
        expect($student->events)->toContain($event);
        expect($term->events)->toContain($event);
        
        // Create event items and test EventItem -> Event relationship
        $eventItem = EventItem::factory()->create([
            'event_id' => $event->id,
        ]);
        
        expect($eventItem->event)->toBeInstanceOf(Event::class);
        expect($eventItem->event->id)->toBe($event->id);
        expect($event->items)->toContain($eventItem);
        
        // Create budget and test Budget -> AcademicTerm relationship
        $budget = Budget::factory()->create([
            'term_id' => $term->id,
        ]);
        
        expect($budget->term)->toBeInstanceOf(AcademicTerm::class);
        expect($budget->term->id)->toBe($term->id);
        expect($term->budget)->toBeInstanceOf(Budget::class);
        
        // Create candidate profile and test CandidateProfile -> User relationship
        $candidateProfile = CandidateProfile::factory()->create([
            'student_id' => $student->id,
        ]);
        
        expect($candidateProfile->student)->toBeInstanceOf(User::class);
        expect($candidateProfile->student->id)->toBe($student->id);
        expect($student->candidateProfile)->toBeInstanceOf(CandidateProfile::class);
        
        // Create vote and test Vote relationships
        $vote = Vote::factory()->create([
            'student_id' => $student->id,
            'candidate_id' => $candidateProfile->id,
            'term_id' => $term->id,
        ]);
        
        expect($vote->student)->toBeInstanceOf(User::class);
        expect($vote->candidate)->toBeInstanceOf(CandidateProfile::class);
        expect($vote->term)->toBeInstanceOf(AcademicTerm::class);
        
        // Create event graphic and test EventGraphic relationships
        $eventGraphic = EventGraphic::factory()->create([
            'event_id' => $event->id,
            'gd_id' => $gd->id,
        ]);
        
        expect($eventGraphic->event)->toBeInstanceOf(Event::class);
        expect($eventGraphic->designer)->toBeInstanceOf(User::class);
        expect($eventGraphic->designer->role)->toBe('gd');
        
        // Create event volunteer and test EventVolunteer relationships
        $eventVolunteer = EventVolunteer::factory()->create([
            'event_id' => $event->id,
            'vc_id' => $vc->id,
        ]);
        
        expect($eventVolunteer->event)->toBeInstanceOf(Event::class);
        expect($eventVolunteer->coordinator)->toBeInstanceOf(User::class);
        expect($eventVolunteer->coordinator->role)->toBe('vc');
        
        // Create activity log and test ActivityLog relationships
        $activityLog = ActivityLog::factory()->create([
            'user_id' => $student->id,
            'related_event_id' => $event->id,
        ]);
        
        expect($activityLog->user)->toBeInstanceOf(User::class);
        expect($activityLog->event)->toBeInstanceOf(Event::class);
        
        // Create announcement and test Announcement -> User relationship
        $admin = $users->where('role', 'admin')->first();
        $announcement = Announcement::factory()->create([
            'created_by' => $admin->id,
        ]);
        
        expect($announcement->creator)->toBeInstanceOf(User::class);
        expect($announcement->creator->role)->toBe('admin');
        
        // Test cascade delete behavior
        // When we delete a term, related records should be handled properly
        $termId = $term->id;
        $term->delete();
        
        // Users should have current_term_id set to null (ON DELETE SET NULL)
        $student->refresh();
        expect($student->current_term_id)->toBeNull();
        
        // Budget should be deleted (ON DELETE CASCADE)
        expect(Budget::where('term_id', $termId)->exists())->toBeFalse();
        
        // Events should be deleted (ON DELETE CASCADE)
        expect(Event::where('term_id', $termId)->exists())->toBeFalse();
        
        // Event items should be deleted (CASCADE through events)
        expect(EventItem::where('event_id', $event->id)->exists())->toBeFalse();
        
        // Votes should be deleted (ON DELETE CASCADE)
        expect(Vote::where('term_id', $termId)->exists())->toBeFalse();
        
        // Clean up for next iteration
        User::query()->delete();
        AcademicTerm::query()->delete();
    }
});

/**
 * Property Test: Foreign Key Constraint Enforcement
 * Tests that invalid foreign key values are rejected
 */
test('foreign key constraints prevent invalid references', function () {
    for ($i = 0; $i < 50; $i++) {
        // Try to create records with invalid foreign keys
        $nonExistentId = 99999;
        
        // Test User with invalid current_term_id
        expect(fn() => User::factory()->create(['current_term_id' => $nonExistentId]))
            ->toThrow(Exception::class);
        
        // Test Event with invalid student_id
        $term = AcademicTerm::factory()->create();
        expect(fn() => Event::factory()->create([
            'student_id' => $nonExistentId,
            'term_id' => $term->id,
        ]))->toThrow(Exception::class);
        
        // Test Event with invalid term_id
        $user = User::factory()->create(['current_term_id' => $term->id]);
        expect(fn() => Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $nonExistentId,
        ]))->toThrow(Exception::class);
        
        // Clean up
        $term->delete();
    }
});

/**
 * Property Test: Unique Constraint Enforcement
 * Tests that unique constraints are properly enforced
 */
test('unique constraints prevent duplicate records', function () {
    for ($i = 0; $i < 50; $i++) {
        $term = AcademicTerm::factory()->create();
        
        // Test unique reg_id constraint on users
        $regId = 'TEST-' . rand(1000, 9999);
        User::factory()->create([
            'reg_id' => $regId,
            'current_term_id' => $term->id,
        ]);
        
        expect(fn() => User::factory()->create([
            'reg_id' => $regId,
            'current_term_id' => $term->id,
        ]))->toThrow(Exception::class);
        
        // Test unique email constraint on users
        $email = 'test' . rand(1000, 9999) . '@example.com';
        User::factory()->create([
            'email' => $email,
            'current_term_id' => $term->id,
        ]);
        
        expect(fn() => User::factory()->create([
            'email' => $email,
            'current_term_id' => $term->id,
        ]))->toThrow(Exception::class);
        
        // Test unique term_id constraint on budgets
        Budget::factory()->create(['term_id' => $term->id]);
        
        expect(fn() => Budget::factory()->create(['term_id' => $term->id]))
            ->toThrow(Exception::class);
        
        // Clean up
        $term->delete();
    }
});