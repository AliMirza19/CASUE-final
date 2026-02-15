<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\User;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for Event Item Calculation
 * 
 * These tests verify specific examples and edge cases for event item calculations.
 * Complements the property-based tests with concrete scenarios.
 */
class EventItemCalculationUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_item_calculation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
            'grand_total' => 0,
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Test Item',
            'quantity' => 5,
            'unit_rate' => 123.45,
        ]);

        $expectedTotal = 5 * 123.45; // 617.25
        $this->assertEquals($expectedTotal, $item->total_amount);
        
        $event->refresh();
        $this->assertEquals($expectedTotal, $event->grand_total);
    }

    public function test_multiple_items_calculation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
            'grand_total' => 0,
        ]);

        // Item 1: 3 × 100.00 = 300.00
        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 1',
            'quantity' => 3,
            'unit_rate' => 100.00,
        ]);

        // Item 2: 2 × 75.50 = 151.00
        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 2',
            'quantity' => 2,
            'unit_rate' => 75.50,
        ]);

        // Item 3: 1 × 49.99 = 49.99
        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 3',
            'quantity' => 1,
            'unit_rate' => 49.99,
        ]);

        $event->refresh();
        $expectedGrandTotal = 300.00 + 151.00 + 49.99; // 500.99
        $this->assertEquals($expectedGrandTotal, $event->grand_total);
    }

    public function test_zero_quantity_calculation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Zero Quantity Item',
            'quantity' => 0,
            'unit_rate' => 100.00,
        ]);

        $this->assertEquals(0, $item->total_amount);
    }

    public function test_zero_rate_calculation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Free Item',
            'quantity' => 5,
            'unit_rate' => 0.00,
        ]);

        $this->assertEquals(0, $item->total_amount);
    }

    public function test_decimal_precision_calculation()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Decimal Item',
            'quantity' => 3,
            'unit_rate' => 33.33,
        ]);

        $expectedTotal = 3 * 33.33; // 99.99
        $this->assertEquals($expectedTotal, $item->total_amount);
    }

    public function test_item_update_recalculates_totals()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
            'grand_total' => 0,
        ]);

        $item = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Updatable Item',
            'quantity' => 2,
            'unit_rate' => 50.00,
        ]);

        // Initial calculation: 2 × 50.00 = 100.00
        $this->assertEquals(100.00, $item->total_amount);
        
        $event->refresh();
        $this->assertEquals(100.00, $event->grand_total);

        // Update quantity: 5 × 50.00 = 250.00
        $item->update(['quantity' => 5]);
        $this->assertEquals(250.00, $item->fresh()->total_amount);
        
        $event->refresh();
        $this->assertEquals(250.00, $event->grand_total);

        // Update rate: 5 × 75.00 = 375.00
        $item->update(['unit_rate' => 75.00]);
        $this->assertEquals(375.00, $item->fresh()->total_amount);
        
        $event->refresh();
        $this->assertEquals(375.00, $event->grand_total);
    }

    public function test_item_deletion_updates_event_total()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
            'grand_total' => 0,
        ]);

        // Create two items
        $item1 = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 1',
            'quantity' => 2,
            'unit_rate' => 100.00,
        ]); // 200.00

        $item2 = EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item 2',
            'quantity' => 3,
            'unit_rate' => 50.00,
        ]); // 150.00

        $event->refresh();
        $this->assertEquals(350.00, $event->grand_total); // 200.00 + 150.00

        // Delete one item
        $item1->delete();

        $event->refresh();
        $this->assertEquals(150.00, $event->grand_total); // Only item2 remains
    }

    public function test_calculate_total_method_accuracy()
    {
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
        ]);

        // Create items with known totals
        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item A',
            'quantity' => 4,
            'unit_rate' => 25.75,
        ]); // 103.00

        EventItem::create([
            'event_id' => $event->id,
            'item_name' => 'Item B',
            'quantity' => 7,
            'unit_rate' => 12.50,
        ]); // 87.50

        $expectedTotal = 103.00 + 87.50; // 190.50
        $calculatedTotal = $event->calculateTotal();
        
        $this->assertEquals($expectedTotal, $calculatedTotal);
    }
}