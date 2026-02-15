<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\EventGraphic;
use App\Models\EventVolunteer;
use App\Models\User;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_can_be_created_with_required_fields()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();

        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test Description',
            'student_id' => $user->id,
            'term_id' => $term->id,
            'expected_date' => now()->addDays(30),
            'venue' => 'Test Venue',
            'grand_total' => 5000.00,
        ]);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->title);
        $this->assertEquals('pending_president', $event->status);
    }

    public function test_event_belongs_to_student_and_term()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
        ]);

        $this->assertInstanceOf(User::class, $event->student);
        $this->assertInstanceOf(AcademicTerm::class, $event->term);
        $this->assertEquals($user->id, $event->student->id);
        $this->assertEquals($term->id, $event->term->id);
    }

    public function test_event_has_many_items()
    {
        $event = Event::factory()->create();
        $items = EventItem::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertCount(3, $event->items);
        $this->assertInstanceOf(EventItem::class, $event->items->first());
    }

    public function test_event_status_helper_methods()
    {
        $pendingEvent = Event::factory()->create(['status' => 'pending_president']);
        $approvedEvent = Event::factory()->create(['status' => 'approved']);
        $rejectedEvent = Event::factory()->create(['status' => 'rejected']);

        $this->assertTrue($pendingEvent->isPending());
        $this->assertFalse($pendingEvent->isApproved());
        $this->assertFalse($pendingEvent->isRejected());

        $this->assertFalse($approvedEvent->isPending());
        $this->assertTrue($approvedEvent->isApproved());
        $this->assertFalse($approvedEvent->isRejected());

        $this->assertFalse($rejectedEvent->isPending());
        $this->assertFalse($rejectedEvent->isApproved());
        $this->assertTrue($rejectedEvent->isRejected());
    }

    public function test_event_workflow_methods()
    {
        $event = Event::factory()->create(['status' => 'pending_president']);
        
        $this->assertEquals('president', $event->getNextApprover());
        $this->assertEquals('pending_patron', $event->getNextStatus());

        $event->status = 'pending_patron';
        $this->assertEquals('patron', $event->getNextApprover());
        $this->assertEquals('pending_hod', $event->getNextStatus());

        $event->status = 'pending_hod';
        $this->assertEquals('hod', $event->getNextApprover());
        $this->assertEquals('pending_sa', $event->getNextStatus());

        $event->status = 'pending_sa';
        $this->assertEquals('sa', $event->getNextApprover());
        $this->assertEquals('approved', $event->getNextStatus());

        $event->status = 'approved';
        $this->assertNull($event->getNextApprover());
        $this->assertNull($event->getNextStatus());
    }

    public function test_event_item_calculation_accuracy()
    {
        $event = Event::factory()->create(['grand_total' => 0]);
        
        // Create items with known values
        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 1',
            'quantity' => 2,
            'unit_rate' => 100.50,
            'total_amount' => 201.00, // This will be auto-calculated
        ]);

        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 2',
            'quantity' => 3,
            'unit_rate' => 75.25,
            'total_amount' => 225.75, // This will be auto-calculated
        ]);

        // Refresh the event to get updated grand_total
        $event->refresh();

        $expectedTotal = 201.00 + 225.75; // 426.75
        $this->assertEquals($expectedTotal, $event->grand_total);
        $this->assertEquals($expectedTotal, $event->calculateTotal());
    }

    public function test_event_item_auto_calculation()
    {
        $event = Event::factory()->create();
        
        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Test Item',
            'quantity' => 5,
            'unit_rate' => 123.45,
        ]);

        // Total should be automatically calculated
        $expectedTotal = 5 * 123.45; // 617.25
        $this->assertEquals($expectedTotal, $item->total_amount);
    }

    public function test_event_graphics_relationship()
    {
        $event = Event::factory()->create();
        $designer = User::factory()->create(['role' => 'gd']);
        
        $graphic = EventGraphic::factory()->create([
            'event_id' => $event->id,
            'gd_id' => $designer->id,
        ]);

        $this->assertCount(1, $event->graphics);
        $this->assertInstanceOf(EventGraphic::class, $event->graphics->first());
        $this->assertEquals($designer->id, $event->graphics->first()->designer->id);
    }

    public function test_event_volunteers_relationship()
    {
        $event = Event::factory()->create();
        $coordinator = User::factory()->create(['role' => 'vc']);
        
        $volunteer = EventVolunteer::factory()->create([
            'event_id' => $event->id,
            'vc_id' => $coordinator->id,
        ]);

        $this->assertCount(1, $event->volunteers);
        $this->assertInstanceOf(EventVolunteer::class, $event->volunteers->first());
        $this->assertEquals($coordinator->id, $event->volunteers->first()->coordinator->id);
    }

    public function test_event_graphic_status_methods()
    {
        $graphic = EventGraphic::factory()->create(['status' => 'pending_patron']);
        
        $this->assertTrue($graphic->isPending());
        $this->assertFalse($graphic->isApproved());
        $this->assertFalse($graphic->isRejected());

        $graphic->status = 'approved';
        $this->assertFalse($graphic->isPending());
        $this->assertTrue($graphic->isApproved());
        $this->assertFalse($graphic->isRejected());

        $graphic->status = 'rejected';
        $this->assertFalse($graphic->isPending());
        $this->assertFalse($graphic->isApproved());
        $this->assertTrue($graphic->isRejected());
    }
}