<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicTerm>
 */
class AcademicTermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seasons = ['Fall', 'Spring', 'Summer'];
        $years = [2023, 2024, 2025, 2026];
        
        $season = fake()->randomElement($seasons);
        $year = fake()->randomElement($years);
        
        // Generate start and end dates based on season
        $startDate = match($season) {
            'Fall' => fake()->dateTimeBetween("$year-09-01", "$year-09-30"),
            'Spring' => fake()->dateTimeBetween("$year-01-01", "$year-01-31"),
            'Summer' => fake()->dateTimeBetween("$year-06-01", "$year-06-30"),
        };
        
        $endDate = match($season) {
            'Fall' => fake()->dateTimeBetween("$year-12-01", "$year-12-31"),
            'Spring' => fake()->dateTimeBetween("$year-05-01", "$year-05-31"),
            'Summer' => fake()->dateTimeBetween("$year-08-01", "$year-08-31"),
        };
        
        return [
            'term_name' => "$season $year",
            'status' => fake()->randomElement(['active', 'inactive']),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Create an active term.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Create an inactive term.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}