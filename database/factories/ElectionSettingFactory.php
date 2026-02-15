<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectionSetting>
 */
class ElectionSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+2 months');
        
        return [
            'term_id' => AcademicTerm::factory(),
            'voting_enabled' => fake()->boolean(60), // 60% chance of being enabled
            'voting_start_date' => $startDate,
            'voting_end_date' => $endDate,
        ];
    }

    /**
     * Create an enabled election setting.
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'voting_enabled' => true,
        ]);
    }

    /**
     * Create a disabled election setting.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'voting_enabled' => false,
        ]);
    }

    /**
     * Create an active voting period.
     */
    public function activeVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'voting_enabled' => true,
            'voting_start_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'voting_end_date' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }

    /**
     * Create a future voting period.
     */
    public function futureVoting(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('+1 week', '+1 month');
            return [
                'voting_enabled' => true,
                'voting_start_date' => $startDate,
                'voting_end_date' => fake()->dateTimeBetween($startDate, '+2 months'),
            ];
        });
    }

    /**
     * Create a past voting period.
     */
    public function pastVoting(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-2 months', '-1 month');
            return [
                'voting_enabled' => true,
                'voting_start_date' => $startDate,
                'voting_end_date' => fake()->dateTimeBetween($startDate, '-1 week'),
            ];
        });
    }
}