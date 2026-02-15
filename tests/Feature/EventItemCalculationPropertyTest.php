<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\User;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Test for Event Item Calculation Accuracy
 * 
 * Feature: cause-society-laravel-migration, Property 9: Event Item Calculation Accuracy
 * 
 * Property: For any event with items, the total amount should equal the sum of (quantity × unit_rate) for all items.
 * Validates: Requirements 3.8
 */
class EventItemCalculationPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property Test: Event Item Calculation Accuracy
     * 
     * This test generates random event items and verifies that:
     * 1. Each item's total_amount equals quantity × unit_rate
     * 2. The event's grand_total equals the sum of all item totals
     * 
     * Runs 100 iterations with randomized data to ensure the property holds universally.
     */
    public function test_event_item_calculation_accuracy_property()
    {
        // Run property test with 100 iterations
        for ($iteration = 1; $iteration <= 100; $iteration++) {
            $this->runSinglePropertyIteration($iteration);
        }
    }

    /**
     * Single iteration of the property test with random data
     */
    private function runSinglePropertyIteration(int $iteration): void
    {
        // Generate random test data
        $user = User::factory()->create(['role' => 'student']);
        $term = AcademicTerm::factory()->create();
        
        $event = Event::factory()->create([
            'student_id' => $user->id,
            'term_id' => $term->id,
            'grand_total' => 0, // Will be calculated
        ]);

        // Generate random number of items (1-10)
        $itemCount = rand(1, 10);
        $expectedGrandTotal = 0;
        $items = [];

        for ($i = 0; $i < $itemCount; $i++) {
            // Generate random item data
            $quantity = rand(1, 100);
            $unitRate = round(rand(100, 10000) / 100, 2); // Random price between 1.00 and 100.00
            $expectedItemTotal = $quantity * $unitRate;
            
            $item = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Test Item " . ($i + 1) . " (Iteration $iteration)",
                'quantity' => $quantity,
                'unit_rate' => $unitRate,
            ]);

            $items[] = $item;
            $expectedGrandTotal += $expectedItemTotal;

            // Property 1: Each item's total should equal quantity × unit_rate
            $this->assertEquals(
                $expectedItemTotal,
                $item->total_amount,
                "Item calculation failed for iteration $iteration, item $i: " .
                "Expected $quantity × $unitRate = $expectedItemTotal, got {$item->total_amount}"
            );

            // Verify the calculation method works correctly
            $this->assertEquals(
                $expectedItemTotal,
                $item->calculateTotal(),
                "Item calculateTotal() method failed for iteration $iteration, item $i"
            );
        }

        // Refresh event to get updated grand_total
        $event->refresh();

        // Property 2: Event grand_total should equal sum of all item totals
        $this->assertEquals(
            $expectedGrandTotal,
            $event->grand_total,
            "Event grand total calculation failed for iteration $iteration: " .
            "Expected sum of items = $expectedGrandTotal, got {$event->grand_total}"
        );

        // Property 3: Event calculateTotal() method should return correct sum
        $calculatedTotal = $event->calculateTotal();
        $this->assertEquals(
            $expectedGrandTotal,
            $calculatedTotal,
            "Event calculateTotal() method failed for iteration $iteration: " .
            "Expected $expectedGrandTotal, got $calculatedTotal"
        );

        // Property 4: Manual sum should match calculated totals
        $manualSum = $items->sum(fn($item) => $item->total_amount);
        $this->assertEquals(
            $expectedGrandTotal,
            $manualSum,
            "Manual sum verification failed for iteration $iteration"
        );

        // Clean up for next iteration
        $event->delete(); // Cascade delete will remove items
        $user->delete();
        $term->delete();
    }

    /**
     * Property Test: Item Total Recalculation on Update
     * 
     * Verifies that when item quantity or unit_rate changes, 
     * the total_amount is automatically recalculated correctly.
     */
    public function test_item_total_recalculation_on_update_property()
    {
        for ($iteration = 1; $iteration <= 50; $iteration++) {
            $user = User::factory()->create(['role' => 'student']);
            $term = AcademicTerm::factory()->create();
            
            $event = Event::factory()->create([
                'student_id' => $user->id,
                'term_id' => $term->id,
            ]);

            // Create initial item
            $initialQuantity = rand(1, 50);
            $initialRate = round(rand(100, 5000) / 100, 2);
            
            $item = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Update Test Item (Iteration $iteration)",
                'quantity' => $initialQuantity,
                'unit_rate' => $initialRate,
            ]);

            $initialTotal = $initialQuantity * $initialRate;
            $this->assertEquals($initialTotal, $item->total_amount);

            // Update quantity
            $newQuantity = rand(1, 50);
            $item->update(['quantity' => $newQuantity]);
            
            $expectedNewTotal = $newQuantity * $initialRate;
            $this->assertEquals(
                $expectedNewTotal,
                $item->fresh()->total_amount,
                "Item total not recalculated after quantity update in iteration $iteration"
            );

            // Update unit rate
            $newRate = round(rand(100, 5000) / 100, 2);
            $item->update(['unit_rate' => $newRate]);
            
            $expectedFinalTotal = $newQuantity * $newRate;
            $this->assertEquals(
                $expectedFinalTotal,
                $item->fresh()->total_amount,
                "Item total not recalculated after rate update in iteration $iteration"
            );

            // Clean up
            $event->delete();
            $user->delete();
            $term->delete();
        }
    }

    /**
     * Property Test: Event Grand Total Updates When Items Change
     * 
     * Verifies that the event's grand_total is automatically updated
     * when items are added, updated, or deleted.
     */
    public function test_event_grand_total_updates_when_items_change_property()
    {
        for ($iteration = 1; $iteration <= 30; $iteration++) {
            $user = User::factory()->create(['role' => 'student']);
            $term = AcademicTerm::factory()->create();
            
            $event = Event::factory()->create([
                'student_id' => $user->id,
                'term_id' => $term->id,
                'grand_total' => 0,
            ]);

            // Add first item
            $quantity1 = rand(1, 20);
            $rate1 = round(rand(100, 2000) / 100, 2);
            $item1 = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Item 1 (Iteration $iteration)",
                'quantity' => $quantity1,
                'unit_rate' => $rate1,
            ]);

            $event->refresh();
            $expectedTotal = $quantity1 * $rate1;
            $this->assertEquals($expectedTotal, $event->grand_total);

            // Add second item
            $quantity2 = rand(1, 20);
            $rate2 = round(rand(100, 2000) / 100, 2);
            $item2 = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Item 2 (Iteration $iteration)",
                'quantity' => $quantity2,
                'unit_rate' => $rate2,
            ]);

            $event->refresh();
            $expectedTotal = ($quantity1 * $rate1) + ($quantity2 * $rate2);
            $this->assertEquals($expectedTotal, $event->grand_total);

            // Update first item
            $newQuantity1 = rand(1, 20);
            $item1->update(['quantity' => $newQuantity1]);

            $event->refresh();
            $expectedTotal = ($newQuantity1 * $rate1) + ($quantity2 * $rate2);
            $this->assertEquals($expectedTotal, $event->grand_total);

            // Delete second item
            $item2->delete();

            $event->refresh();
            $expectedTotal = $newQuantity1 * $rate1;
            $this->assertEquals($expectedTotal, $event->grand_total);

            // Clean up
            $event->delete();
            $user->delete();
            $term->delete();
        }
    }

    /**
     * Property Test: Zero and Decimal Handling
     * 
     * Verifies that the calculation works correctly with edge cases
     * like zero quantities, zero rates, and decimal values.
     */
    public function test_zero_and_decimal_handling_property()
    {
        for ($iteration = 1; $iteration <= 25; $iteration++) {
            $user = User::factory()->create(['role' => 'student']);
            $term = AcademicTerm::factory()->create();
            
            $event = Event::factory()->create([
                'student_id' => $user->id,
                'term_id' => $term->id,
            ]);

            // Test with zero quantity
            $item1 = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Zero Quantity Item (Iteration $iteration)",
                'quantity' => 0,
                'unit_rate' => rand(100, 1000) / 100,
            ]);
            $this->assertEquals(0, $item1->total_amount);

            // Test with zero rate
            $item2 = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Zero Rate Item (Iteration $iteration)",
                'quantity' => rand(1, 10),
                'unit_rate' => 0,
            ]);
            $this->assertEquals(0, $item2->total_amount);

            // Test with decimal values
            $quantity = rand(1, 10);
            $rate = round(rand(1, 10000) / 100, 2); // Up to 2 decimal places
            $item3 = EventItem::create([
                'event_id' => $event->id,
                'item_name' => "Decimal Item (Iteration $iteration)",
                'quantity' => $quantity,
                'unit_rate' => $rate,
            ]);
            
            $expectedTotal = round($quantity * $rate, 2);
            $this->assertEquals($expectedTotal, $item3->total_amount);

            // Clean up
            $event->delete();
            $user->delete();
            $term->delete();
        }
    }
}