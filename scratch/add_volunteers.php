<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Event;
use App\Models\Volunteer;
use App\Models\AcademicTerm;

$vc = User::where('role', 'vc')->first();
if (!$vc) {
    echo "Volunteer Coordinator not found.\n";
    exit;
}

// 1. Remove existing volunteers that have "Prof." in their name
$profVolunteers = Volunteer::whereHas('user', function($query) {
    $query->where('name', 'like', 'Prof.%');
})->get();

foreach ($profVolunteers as $pv) {
    echo "Removing Prof. volunteer: " . $pv->user->name . "\n";
    $pv->delete();
}

// 2. Find 8 BSE students who do NOT have "Prof." in their name
$students = User::where('role', 'student')
    ->where('reg_id', 'like', 'BSE%')
    ->where('name', 'not like', 'Prof.%')
    ->limit(8)
    ->get();

echo "Found " . $students->count() . " valid BSE students (excluding Prof.).\n";

// Find an approved event
$event = Event::where('status', 'approved')->first();

if (!$event) {
    echo "No approved event found.\n";
    exit;
}

echo "Assigning students to event: " . $event->title . "\n";

foreach ($students as $student) {
    // Add to pool
    $student->update(['is_volunteer_pool' => true]);
    
    // Assign to event if not already assigned
    $exists = Volunteer::where('event_id', $event->id)
        ->where('user_id', $student->id)
        ->exists();
        
    if (!$exists) {
        Volunteer::create([
            'event_id' => $event->id,
            'user_id' => $student->id,
            'role_description' => 'Event Support',
            'assigned_by' => $vc->id,
            'status' => 'assigned'
        ]);
        echo "Assigned student: " . $student->name . " (" . $student->reg_id . ")\n";
    } else {
        echo "Student " . $student->name . " already assigned.\n";
    }
}

echo "Done.\n";
