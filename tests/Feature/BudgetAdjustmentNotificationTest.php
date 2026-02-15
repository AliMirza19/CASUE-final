<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Budget;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetAdjustmentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $patron;
    protected $hod;
    protected $term;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'term_name' => 'Spring 2024',
            'start_date' => now(),
            'end_date' => now()->addMonths(4),
            'is_active' => true
        ]);

        $this->student = User::factory()->create(['role' => 'student', 'current_term_id' => $this->term->id]);
        $this->patron = User::factory()->create(['role' => 'patron', 'current_term_id' => $this->term->id]);
        $this->hod = User::factory()->create(['role' => 'hod', 'current_term_id' => $this->term->id]);

        Budget::create([
            'term_id' => $this->term->id,
            'total_amount' => 10000,
            'remaining_amount' => 10000
        ]);
    }

    public function test_patron_can_adjust_budget_and_student_is_notified()
    {
        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Description',
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'status' => 'pending_patron',
            'grand_total' => 200,
            'expected_date' => now()->addDays(7),
            'venue' => 'Venue'
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Pizza',
            'quantity' => 2,
            'unit_rate' => 100,
            'total_amount' => 200
        ]);

        $response = $this->actingAs($this->patron)->post(route('patron.approve', $event->id), [
            'action' => 'approve',
            'comments' => 'Budget adjusted.',
            'items' => [
                $item->id => [
                    'quantity' => 1,
                    'unit_rate' => 80,
                    'is_approved' => '1',
                    'comment' => 'Too expensive'
                ]
            ]
        ]);

        $response->assertRedirect();
        $item->refresh();
        $event->refresh();

        $this->assertEquals(1, $item->quantity);
        $this->assertEquals(80, $item->unit_rate);
        $this->assertEquals(80, $event->grand_total);
        $this->assertEquals('pending_hod', $event->status);

        // Check notification
        $message = Message::where('receiver_id', $this->student->id)->first();
        $this->assertNotNull($message);
        $this->assertStringContainsString('budget adjustments', $message->message_text);
        $this->assertStringContainsString('Pizza', $message->message_text);
        $this->assertStringContainsString('Quantity: 2 -> 1', $message->message_text);
        $this->assertStringContainsString('Rate: 100 -> 80', $message->message_text);
    }

    public function test_hod_can_adjust_budget_and_student_is_notified()
    {
        $event = Event::create([
            'title' => 'Test Event HOD',
            'description' => 'Description',
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'status' => 'pending_hod',
            'grand_total' => 80,
            'expected_date' => now()->addDays(7),
            'venue' => 'Venue'
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Soda',
            'quantity' => 1,
            'unit_rate' => 80,
            'total_amount' => 80,
            'is_approved_by_patron' => true
        ]);

        $response = $this->actingAs($this->hod)->post(route('hod.approve', $event->id), [
            'action' => 'approve',
            'comments' => 'Final adjustment.',
            'items' => [
                $item->id => [
                    'quantity' => 1,
                    'unit_rate' => 50
                ]
            ]
        ]);

        $response->assertRedirect();
        $item->refresh();
        $event->refresh();

        $this->assertEquals(50, $item->unit_rate);
        $this->assertEquals(50, $event->grand_total);
        $this->assertEquals('pending_sa', $event->status);

        // Check budget deduction
        $budget = Budget::where('term_id', $this->term->id)->first();
        $this->assertEquals(9950, $budget->remaining_amount);

        // Check notification
        $message = Message::where('receiver_id', $this->student->id)
            ->where('message_text', 'like', '%finalized budget review%')
            ->first();
        $this->assertNotNull($message);
        $this->assertStringContainsString('Soda', $message->message_text);
        $this->assertStringContainsString('Rate: 80 -> 50', $message->message_text);
    }
}
