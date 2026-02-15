<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventItem>
 */
class EventItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $items = [
            'Sound System',
            'Projector',
            'Chairs',
            'Tables',
            'Microphones',
            'Decorations',
            'Catering',
            'Photography',
            'Security',
            'Transportation'
        ];
        
        $quantity = fake()->numberBetween(1, 20);
        $unitRate = fake()->randomFloat(2, 100, 5000);
        
        return [
            'event_id' => Event::factory(),
            'item_name' => fake()->randomElement($items),
            'quantity' => $quantity,
            'unit_rate' => $unitRate,
            'total_amount' => $quantity * $unitRate,
            'is_approved_by_patron' => fake()->boolean(70), // 70% chance of approval
            'patron_comment' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create an approved item.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved_by_patron' => true,
            'patron_comment' => fake()->optional()->sentence(),
        ]);
    }

    /**
     * Create a rejected item.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved_by_patron' => false,
            'patron_comment' => fake()->sentence(),
        ]);
    }
}