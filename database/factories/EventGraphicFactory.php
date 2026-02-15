<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventGraphic>
 */
class EventGraphicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'gd_id' => User::factory()->role('gd'),
            'design_category' => fake()->randomElement(['poster', 'banner', 'social_media']),
            'image_path' => fake()->optional()->filePath(),
            'image_link' => fake()->optional()->url(),
            'status' => fake()->randomElement(['pending_patron', 'approved', 'rejected']),
            'patron_feedback' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create an approved graphic.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'patron_feedback' => fake()->optional()->sentence(),
        ]);
    }

    /**
     * Create a rejected graphic.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'patron_feedback' => fake()->sentence(),
        ]);
    }

    /**
     * Create a poster graphic.
     */
    public function poster(): static
    {
        return $this->state(fn (array $attributes) => [
            'design_category' => 'poster',
        ]);
    }

    /**
     * Create a banner graphic.
     */
    public function banner(): static
    {
        return $this->state(fn (array $attributes) => [
            'design_category' => 'banner',
        ]);
    }

    /**
     * Create a social media graphic.
     */
    public function socialMedia(): static
    {
        return $this->state(fn (array $attributes) => [
            'design_category' => 'social_media',
        ]);
    }
}