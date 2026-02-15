<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
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
            'candidate_id' => CandidateProfile::factory(),
            'term_id' => AcademicTerm::factory(),
            'voted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}