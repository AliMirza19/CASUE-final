<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd'];
        
        return [
            'reg_id' => fake()->unique()->regexify('[A-Z]{2,4}-[0-9]{3}'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement($roles),
            'password_changed' => fake()->boolean(80), // 80% chance of true
            'current_term_id' => null, // Will be set by the test or relationship
        ];
    }

    /**
     * Indicate that the user needs to change password.
     */
    public function needsPasswordChange(): static
    {
        return $this->state(fn (array $attributes) => [
            'password_changed' => false,
        ]);
    }

    /**
     * Create a user with a specific role.
     */
    public function role(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    /**
     * Create a student user.
     */
    public function student(): static
    {
        return $this->role('student');
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->role('admin');
    }
}
