<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FacultyTestSeeder extends Seeder
{
    /**
     * Create a test faculty user for testing the faculty dashboard.
     */
    public function run(): void
    {
        // Create test faculty user if not exists
        User::firstOrCreate(
            ['reg_id' => 'BFE100001'],
            [
                'name' => 'Dr. Test Faculty',
                'email' => 'faculty@test.com',
                'password' => Hash::make('Welcome@123'),
                'role' => 'faculty',
                'password_changed' => true,
            ]
        );

        $this->command->info('Test faculty user created:');
        $this->command->info('  Registration ID: BFE100001');
        $this->command->info('  Email: faculty@test.com');
        $this->command->info('  Password: Welcome@123');
    }
}
