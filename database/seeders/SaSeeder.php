<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SaSeeder extends Seeder
{
    public function run(): void
    {
        // Try to get or create active term
        $activeTerm = AcademicTerm::firstOrCreate(
            ['status' => 'active'],
            [
                'term_name' => 'Fall 2024',
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-31'
            ]
        );

        $sa = User::firstOrCreate(
            ['reg_id' => 'SA-001'],
            [
                'name' => 'Fatima Ali',
                'email' => 'sa@cause.edu.pk',
                'password' => Hash::make('123456'),
                'role' => 'sa',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ]
        );

        // Update if already exists with old parameters
        if (!$sa->wasRecentlyCreated) {
            $sa->update([
                'password' => Hash::make('123456'),
                'role' => 'sa'
            ]);
        }

        $this->command->info('Student Affair (SA) user seeded successfully!');
    }
}
