<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\User;
use App\Models\AcademicTerm;
use App\Models\Budget;
use App\Models\ActivityLog;
use App\Services\EventWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class EventWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private EventWorkflowService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventWorkflowService();
    }

    public function test_submit_event_creates_event_with_items()
    {
        $student = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'student_id' => $student->id,
            'term_id' => $term->id,
            'expected_date' => now()->addDays(30)->format('Y-m-d'),
            'venue' => 'Test Venue',
            'items' => [
                [
                    'item_name' => 'Item 1',
                    'quantity' => 2,
                    'unit_rate' => 100.00,
                ],
                [
                    'item_name' => 'Item 2',
                    'quantity' => 3,
                    'unit_rate' => 50.00,
                ],
            ],
        ];

        $event = $this->service->submitEvent($eventData);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->title);
        $this->assertEquals('pending_president', $event->status);
        $this->assertEquals(350.00, $event->grand_total); // (2*100) + (3*50)
        $this->assertCount(2, $event->items);

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $student->id,
            'user_role' => 'student',
            'related_event_id' => $event->id,
        ]);
    }

    public function test_approve_event_workflow_progression()
    {
        $student = User::factory()->create(['role' => 'student']);
        $president = User::factory()->create(['role' => 'president']);
        $patron = User::factory()->create(['role' => 'patron']);
        $hod = User::factory()->create(['role' => 'hod']);
        $sa = User::factory()->create(['role' => 'sa']);
        $term = AcademicTerm::factory()->create();

        // Create budget for the term
        Budget::factory()->create([
            'term_id' => $term->id,
            'total_amount' => 10000.00,
            'remaining_amount' => 10000.00,
        ]);

        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'pending_president',
            'grand_total' => 1000.00,
        ]);

        // President approval
        $this->assertTrue($this->service->approveEvent($event, $president));
        $event->refresh();
        $this->assertEquals('pending_patron', $event->status);

        // Patron approval
        $this->assertTrue($this->service->approveEvent($event, $patron));
        $event->refresh();
        $this->assertEquals('pending_hod', $event->status);

        // HOD approval
        $this->assertTrue($this->service->approveEvent($event, $hod));
        $event->refresh();
        $this->assertEquals('pending_sa', $event->status);

        // SA approval (final)
        $this->assertTrue($this->service->approveEvent($event, $sa));
        $event->refresh();
        $this->assertEquals('approved', $event->status);

        // Check budget deduction
        $budget = Budget::where('term_id', $term->id)->first();
        $this->assertEquals(9000.00, $budget->remaining_amount); // 10000 - 1000
    }

    public function test_reject_event_sets_status_and_reason()
    {
        $student = User::factory()->create(['role' => 'student']);
        $president = User::factory()->create(['role' => 'president']);
        $term = AcademicTerm::factory()->create();

        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'pending_president',
        ]);

        $rejectionReason = 'Insufficient details provided';
        $this->assertTrue($this->service->rejectEvent($event, $president, $rejectionReason));

        $event->refresh();
        $this->assertEquals('rejected', $event->status);
        $this->assertEquals($rejectionReason, $event->rejection_reason);

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $president->id,
            'user_role' => 'president',
            'related_event_id' => $event->id,
        ]);
    }

    public function test_unauthorized_user_cannot_approve()
    {
        $student = User::factory()->create(['role' => 'student']);
        $wrongUser = User::factory()->create(['role' => 'patron']); // Wrong role for pending_president
        $term = AcademicTerm::factory()->create();

        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'pending_president',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("User with role 'patron' cannot approve event at status 'pending_president'");

        $this->service->approveEvent($event, $wrongUser);
    }

    public function test_insufficient_budget_prevents_approval()
    {
        $student = User::factory()->create(['role' => 'student']);
        $sa = User::factory()->create(['role' => 'sa']);
        $term = AcademicTerm::factory()->create();

        // Create budget with insufficient funds
        Budget::factory()->create([
            'term_id' => $term->id,
            'total_amount' => 1000.00,
            'remaining_amount' => 500.00, // Less than event cost
        ]);

        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'pending_sa', // Ready for final approval
            'grand_total' => 1000.00, // More than available budget
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient budget');

        $this->service->approveEvent($event, $sa);
    }

    public function test_get_pending_events_for_role()
    {
        $term = AcademicTerm::factory()->create();
        
        // Create events with different statuses
        Event::factory()->create(['status' => 'pending_president', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'pending_president', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'pending_patron', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'approved', 'term_id' => $term->id]);

        $presidentEvents = $this->service->getPendingEventsForRole('president');
        $patronEvents = $this->service->getPendingEventsForRole('patron');

        $this->assertCount(2, $presidentEvents);
        $this->assertCount(1, $patronEvents);
    }

    public function test_can_user_approve_event()
    {
        $president = User::factory()->create(['role' => 'president']);
        $patron = User::factory()->create(['role' => 'patron']);
        $term = AcademicTerm::factory()->create();

        $event = Event::factory()->create([
            'status' => 'pending_president',
            'term_id' => $term->id,
        ]);

        $this->assertTrue($this->service->canUserApproveEvent($event, $president));
        $this->assertFalse($this->service->canUserApproveEvent($event, $patron));
    }

    public function test_resubmit_rejected_event()
    {
        $student = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();

        $event = Event::factory()->create([
            'student_id' => $student->id,
            'term_id' => $term->id,
            'status' => 'rejected',
            'rejection_reason' => 'Some reason',
        ]);

        $this->assertTrue($this->service->resubmitEvent($event, $student));

        $event->refresh();
        $this->assertEquals('pending_president', $event->status);
        $this->assertNull($event->rejection_reason);
    }

    public function test_workflow_statistics()
    {
        $term = AcademicTerm::factory()->create();

        // Create events with various statuses
        Event::factory()->create(['status' => 'pending_president', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'pending_patron', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'approved', 'term_id' => $term->id]);
        Event::factory()->create(['status' => 'rejected', 'term_id' => $term->id]);

        $stats = $this->service->getWorkflowStatistics($term);

        $this->assertEquals(4, $stats['total_events']);
        $this->assertEquals(1, $stats['pending_president']);
        $this->assertEquals(1, $stats['pending_patron']);
        $this->assertEquals(1, $stats['approved']);
        $this->assertEquals(1, $stats['rejected']);
    }
}