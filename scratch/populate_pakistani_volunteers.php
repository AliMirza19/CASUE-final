<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Support\Facades\DB;

// 1. Reset all volunteer pool flags
User::where('is_volunteer_pool', true)->update(['is_volunteer_pool' => false]);

// 2. Remove existing volunteer assignments to start fresh (optional, but requested to "remove and add new")
Volunteer::truncate();

// 3. Pakistani names and details
$pakistaniVolunteers = [
    ['name' => 'Muhammad Ahmed', 'reg_id' => 'BSE223001', 'email' => 'ahmed@cause.com', 'skills' => 'Management, Leadership'],
    ['name' => 'Fatima Zahra', 'reg_id' => 'BSE223002', 'email' => 'fatima@cause.com', 'skills' => 'Documentation, Social Media'],
    ['name' => 'Ali Raza', 'reg_id' => 'BSE223003', 'email' => 'ali@cause.com', 'skills' => 'Technical Support, AV'],
    ['name' => 'Zainab Bibi', 'reg_id' => 'BSE223004', 'email' => 'zainab@cause.com', 'skills' => 'Decoration, Creative'],
    ['name' => 'Umar Farooq', 'reg_id' => 'BSE223005', 'email' => 'umar@cause.com', 'skills' => 'Security, Logistics'],
    ['name' => 'Ayesha Siddiqua', 'reg_id' => 'BSE223006', 'email' => 'ayesha@cause.com', 'skills' => 'Photography, Content'],
    ['name' => 'Hassan Khan', 'reg_id' => 'BSE223007', 'email' => 'hassan@cause.com', 'skills' => 'Graphic Design, Editing'],
    ['name' => 'Bilal Ahmed', 'reg_id' => 'BSE223008', 'email' => 'bilal@cause.com', 'skills' => 'Finance, Planning'],
];

foreach ($pakistaniVolunteers as $v) {
    // Find or create user by reg_id
    $user = User::updateOrCreate(
        ['reg_id' => $v['reg_id']],
        [
            'name' => $v['name'],
            'email' => $v['email'],
            'role' => 'student',
            'password' => bcrypt('123456'),
            'is_volunteer_pool' => true,
            'skills' => $v['skills'],
            'current_term_id' => 1 // Active term
        ]
    );
    
    echo "Added/Updated: " . $user->name . " (" . $user->reg_id . ")\n";
}

echo "Volunteer Pool populated with Pakistani names successfully.\n";
