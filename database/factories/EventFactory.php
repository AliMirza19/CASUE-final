<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            'pending_president',
            'pending_patron',
            'pending_hod',
            'pending_sa',
            'approved',
            'rejected',
            'completed'
        ];
        
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'student_id' => User::factory()->student(),
            'term_id' => AcademicTerm::factory(),
            'expected_date' => fake()->dateTimeBetween('now', '+6 months'),
            'venue' => fake()->randomElement([
                'Main Auditorium',
                'Conference Hall',
                'Seminar Room A',
                'Outdoor Ground',
                'Library Hall',
                'Computer Lab'
            ]),
            'grand_total' => fake()->randomFloat(2, 1000, 50000),
            'guest_speaker_name' => fake()->optional()->name(),
            'guest_speaker_designation' => fake()->optional()->jobTitle(),
            'faculty_mentor_id' => null,
            'status' => fake()->randomElement($statuses),
            'rejection_reason' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create a pending event.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_president',
            'rejection_reason' => null,
        ]);
    }

    /**
     * Create an approved event.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'rejection_reason' => null,
        ]);
    }

    /**
     * Create a rejected event.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}