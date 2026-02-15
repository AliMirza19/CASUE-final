<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default academic term
        $activeTerm = AcademicTerm::create([
            'term_name' => 'Fall 2024',
            'status' => 'active',
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31',
        ]);

        // Create default users with properly hashed password
        $defaultPassword = Hash::make('123456');

        $users = [
            [
                'reg_id' => 'ADMIN-001',
                'name' => 'System Administrator',
                'email' => 'admin@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'admin',
                'password_changed' => false,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'HOD-001',
                'name' => 'Dr. Ahmed Khan',
                'email' => 'hod@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'hod',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'STU-001',
                'name' => 'Ali Hassan',
                'email' => 'student@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'student',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'PRES-001',
                'name' => 'Sarah Ahmed',
                'email' => 'president@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'president',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'PAT-001',
                'name' => 'Prof. Muhammad Khan',
                'email' => 'patron@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'patron',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'SA-001',
                'name' => 'Fatima Ali',
                'email' => 'sa@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'sa',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'VC-001',
                'name' => 'Dr. Ayesha Khan',
                'email' => 'vc@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'vc',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
            [
                'reg_id' => 'GD-001',
                'name' => 'Hassan Ali',
                'email' => 'gd@cause.edu.pk',
                'password' => $defaultPassword,
                'role' => 'gd',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('Default academic term and users created successfully!');
        $this->command->info('Default login credentials:');
        $this->command->info('Admin: ADMIN-001 / 123456 (password change required)');
        $this->command->info('All other users: [REG_ID] / 123456');
    }
}
