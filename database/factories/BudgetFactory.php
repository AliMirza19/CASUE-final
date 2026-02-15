<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 50000, 500000);
        $spentAmount = fake()->randomFloat(2, 0, $totalAmount * 0.8); // Max 80% spent
        $remainingAmount = $totalAmount - $spentAmount;
        
        return [
            'term_id' => AcademicTerm::factory(),
            'total_amount' => $totalAmount,
            'remaining_amount' => $remainingAmount,
            'is_locked' => fake()->boolean(20), // 20% chance of being locked
        ];
    }

    /**
     * Create a locked budget.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
        ]);
    }

    /**
     * Create an unlocked budget.
     */
    public function unlocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => false,
        ]);
    }

    /**
     * Create a budget with full amount remaining.
     */
    public function full(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'remaining_amount' => $attributes['total_amount'],
            ];
        });
    }

    /**
     * Create a budget with no amount remaining.
     */
    public function empty(): static
    {
        return $this->state(fn (array $attributes) => [
            'remaining_amount' => 0,
        ]);
    }
}