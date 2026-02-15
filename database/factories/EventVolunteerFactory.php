<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventVolunteer>
 */
class EventVolunteerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            'Registration Desk',
            'Sound System Operator',
            'Photography',
            'Security',
            'Crowd Management',
            'Technical Support',
            'Hospitality',
            'Setup & Cleanup'
        ];
        
        return [
            'event_id' => Event::factory(),
            'vc_id' => User::factory()->role('vc'),
            'volunteer_name' => fake()->name(),
            'volunteer_contact' => fake()->optional()->phoneNumber(),
            'role_description' => fake()->randomElement($roles),
            'assigned_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}