<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = [
            'Submitted new event request',
            'Approved event request',
            'Rejected event request',
            'Updated event details',
            'Uploaded event graphics',
            'Assigned volunteers to event',
            'Updated budget allocation',
            'Submitted candidate profile',
            'Cast vote in election',
            'Created system announcement'
        ];
        
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        
        return [
            'user_id' => User::factory(),
            'user_role' => fake()->randomElement($roles),
            'action_text' => fake()->randomElement($actions),
            'related_event_id' => fake()->optional()->randomElement([null, Event::factory()]),
        ];
    }

    /**
     * Create an activity log for a specific role.
     */
    public function forRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'user_role' => $role,
        ]);
    }

    /**
     * Create an activity log related to an event.
     */
    public function withEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'related_event_id' => Event::factory(),
        ]);
    }
}