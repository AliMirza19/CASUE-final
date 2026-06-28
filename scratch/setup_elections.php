<?php

use App\Models\AcademicTerm;
use App\Models\ElectionSetting;
use App\Models\User;
use App\Models\CandidateApplication;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting election activation...\n";

$activeTerm = AcademicTerm::getActive();
if (!$activeTerm) {
    echo "No active term found. Creating one...\n";
    $activeTerm = AcademicTerm::create([
        'term_name' => 'Spring 2026',
        'is_active' => true,
        'start_date' => now(),
        'end_date' => now()->addMonths(6),
    ]);
}

echo "Active Term: " . $activeTerm->term_name . "\n";

$election = ElectionSetting::updateOrCreate(
    ['term_id' => $activeTerm->id],
    [
        'is_active' => true,
        'registration_start' => now()->subDays(1),
        'registration_end' => now()->addDays(7),
        'voting_start' => now()->addDays(8),
        'voting_end' => now()->addDays(10),
    ]
);

echo "Election activated for " . $activeTerm->term_name . "\n";

// Add 5 students for review
$students = User::where('role', 'student')->take(5)->get();

if ($students->count() < 5) {
    echo "Found only " . $students->count() . " students. Creating more...\n";
    for ($i = $students->count(); $i < 5; $i++) {
        User::create([
            'name' => "Student Test " . ($i + 1),
            'email' => "student" . ($i + 1) . "@test.com",
            'password' => bcrypt('password'),
            'role' => 'student',
            'reg_id' => 'CUST-S-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
        ]);
    }
    $students = User::where('role', 'student')->take(5)->get();
}

foreach ($students as $student) {
    CandidateApplication::updateOrCreate(
        ['student_id' => $student->id],
        [
            'manifesto_text' => "I want to lead the CAUSE society to new heights by organizing more technical workshops and social events. My goal is to bridge the gap between students and industry.",
            'status' => 'pending',
        ]
    );
    echo "Added student " . $student->name . " for review.\n";
}

echo "Done!\n";
