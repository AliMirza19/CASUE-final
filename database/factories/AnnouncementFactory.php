<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->paragraphs(2, true),
            'created_by' => User::factory()->admin(),
            'target_roles' => fake()->optional()->randomElements($roles, fake()->numberBetween(1, 4)),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Create an active announcement.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive announcement.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create an announcement for all roles.
     */
    public function forAllRoles(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_roles' => null,
        ]);
    }

    /**
     * Create an announcement for specific roles.
     */
    public function forRoles(array $roles): static
    {
        return $this->state(fn (array $attributes) => [
            'target_roles' => $roles,
        ]);
    }
}