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
        // Disable foreign key constraints for truncation
        \Schema::disableForeignKeyConstraints();
        \App\Models\User::truncate();
        \App\Models\StudentProfile::truncate();
        \App\Models\FacultyProfile::truncate();
        \App\Models\AcademicTerm::truncate();
        \App\Models\RoleAssignment::truncate();
        \Schema::enableForeignKeyConstraints();

        $faker = \Faker\Factory::create();
        $defaultPassword = Hash::make('password123');

        // Create default academic term
        $activeTerm = AcademicTerm::create([
            'term_name' => 'Spring 2026',
            'status' => 'active',
            'start_date' => '2026-02-01',
            'end_date' => '2026-06-30',
        ]);

        // 1. Create Admin
        User::create([
            'reg_id' => 'ADMIN-001',
            'name' => 'System Administrator',
            'email' => 'admin@cause.edu.pk',
            'password' => $defaultPassword,
            'role' => 'admin',
            'password_changed' => true,
            'current_term_id' => $activeTerm->id,
        ]);

        // 2. Create Faculty Members
        $facultyRoles = [
            ['rank' => 'Assistant Professor', 'title' => 'Dr.', 'count' => 6],
            ['rank' => 'Professor', 'title' => 'Dr.', 'count' => 6],
            ['rank' => 'Associate Professor', 'title' => '', 'count' => 6],
            ['rank' => 'Lecturer', 'title' => '', 'count' => 6],
            ['rank' => 'Lab Engineer', 'title' => '', 'count' => 6],
        ];

        foreach ($facultyRoles as $roleConfig) {
            for ($i = 1; $i <= $roleConfig['count']; $i++) {
                $name = $faker->name;
                $email = strtolower(str_replace(' ', '.', $name)) . '@cause.edu.pk';
                $user = User::create([
                    'reg_id' => 'FAC-' . strtoupper(substr($roleConfig['rank'], 0, 3)) . '-' . $i . rand(100, 999),
                    'name' => ($roleConfig['title'] ? $roleConfig['title'] . ' ' : '') . $name,
                    'email' => $email,
                    'password' => $defaultPassword,
                    'role' => 'faculty',
                    'password_changed' => true,
                    'current_term_id' => $activeTerm->id,
                ]);

                \App\Models\FacultyProfile::create([
                    'user_id' => $user->id,
                    'title' => $roleConfig['title'] ?: 'Mr./Ms.',
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'cnic_passport' => $faker->numerify('#####-#######-#'),
                    'dob' => $faker->date('Y-m-d', '1990-01-01'),
                    'mobile_number' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'province' => $faker->randomElement(['Punjab', 'Sindh', 'KPK', 'Balochistan']),
                    'city' => $faker->city,
                    'contract_type' => $faker->randomElement(['Permanent', 'Contract', 'Visiting']),
                    'academic_rank' => $roleConfig['rank'],
                    'joining_date' => $faker->date('Y-m-d', 'now'),
                    'highest_degree_name' => 'PhD in Computer Science',
                    'highest_degree_type' => 'Doctorate',
                    'field_of_study' => 'Computer Science',
                    'degree_country' => 'Pakistan',
                    'university_name' => 'CUST University',
                    'degree_start_date' => '2015-01-01',
                    'degree_end_date' => '2019-01-01',
                ]);
            }
        }

        // 3. Create 20 Students
        for ($i = 1; $i <= 20; $i++) {
            $name = $faker->name;
            $rollNo = 'BSE223' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $user = User::create([
                'reg_id' => $rollNo,
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
                'password' => $defaultPassword,
                'role' => 'student',
                'password_changed' => true,
                'current_term_id' => $activeTerm->id,
            ]);

            \App\Models\StudentProfile::create([
                'user_id' => $user->id,
                'roll_no' => $rollNo,
                'father_name' => $faker->name('male'),
                'gender' => $faker->randomElement(['Male', 'Female']),
                'admission_date' => '2022-09-01',
                'nationality' => 'Pakistani',
                'cnic_number' => $faker->numerify('#####-#######-#'),
                'passport_number' => '',
                'dob' => $faker->date('Y-m-d', '2004-01-01'),
                'phone_number' => $faker->phoneNumber,
                'domicile_district' => 'Islamabad',
                'domicile_province' => 'Punjab',
                'mailing_address' => $faker->address,
                'city' => $faker->city,
                'ssc_degree_name' => 'Matric',
                'ssc_board_name' => 'FBISE',
                'ssc_total_marks' => 1100,
                'ssc_obtained_marks' => rand(800, 1050),
                'hssc_degree_name' => 'FSc',
                'hssc_degree_nomenclature' => 'Pre-Engineering',
                'hssc_board_name' => 'FBISE',
                'hssc_total_marks' => 1100,
                'hssc_obtained_marks' => rand(800, 1050),
            ]);
        }

        $this->command->info('Database wiped and seeded with new CAUSE structure!');
    }
}
