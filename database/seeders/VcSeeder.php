<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VcSeeder extends Seeder
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

        $vc = User::firstOrCreate(
            ['reg_id' => 'VC-001'],
            [
                'name' => 'Dr. Ayesha Khan',
                'email' => 'vc@cause.edu.pk',
                'password' => Hash::make('123456'),
                'role' => 'vc',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ]
        );

        // Update if already exists with old parameters
        if (!$vc->wasRecentlyCreated) {
            $vc->update([
                'password' => Hash::make('123456'),
                'role' => 'vc'
            ]);
        }

        $this->command->info('Volunteer Coordinator (VC) user seeded successfully!');
    }
}
