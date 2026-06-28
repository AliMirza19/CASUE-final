<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PresidentSeeder extends Seeder
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

        $president = User::firstOrCreate(
            ['reg_id' => 'PRES-001'],
            [
                'name' => 'Sarah Ahmed',
                'email' => 'president@cause.edu.pk',
                'password' => Hash::make('123456'),
                'role' => 'president',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ]
        );

        // Make sure it's updated in case it already exists but with wrong details
        if (!$president->wasRecentlyCreated) {
            $president->update([
                'password' => Hash::make('123456'),
                'role' => 'president'
            ]);
        }

        $this->command->info('President user seeded successfully!');
    }
}
