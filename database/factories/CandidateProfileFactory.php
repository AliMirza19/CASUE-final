<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateProfile>
 */
class CandidateProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'manifesto' => fake()->paragraphs(3, true),
            'photo_url' => fake()->optional()->imageUrl(400, 400, 'people'),
            'experience' => fake()->optional()->paragraphs(2, true),
            'vp_name' => fake()->optional()->name(),
            'status' => fake()->randomElement(['pending_patron', 'approved', 'rejected']),
            'patron_feedback' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create an approved candidate profile.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'patron_feedback' => fake()->optional()->sentence(),
        ]);
    }

    /**
     * Create a rejected candidate profile.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'patron_feedback' => fake()->sentence(),
        ]);
    }

    /**
     * Create a pending candidate profile.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_patron',
            'patron_feedback' => null,
        ]);
    }
}