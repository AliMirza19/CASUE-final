<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RoleAssignment;

$sarah = User::where('name', 'like', '%Sarah Ahmed%')->first();

if (!$sarah) {
    die("Sarah Ahmed not found. Please create her first.");
}

echo "Ensuring Sarah Ahmed is the ONLY President in all tables...\n";

// 1. Update Users Table
User::where('role', 'president')->where('id', '!=', $sarah->id)->update(['role' => 'student']);
$sarah->update(['role' => 'president']);

// 2. Update Role Assignments Table
RoleAssignment::where('role', 'president')->where('user_id', '!=', $sarah->id)->delete();

// Ensure Sarah is assigned as President for the active term
$activeTerm = \App\Models\AcademicTerm::where('status', 'active')->first();
if ($activeTerm) {
    RoleAssignment::updateOrCreate(
        ['user_id' => $sarah->id, 'term_id' => $activeTerm->id],
        ['role' => 'president']
    );
}

// 3. Specifically check Fatima Zahra
$fatima = User::where('name', 'like', '%Fatima Zahra%')->first();
if ($fatima) {
    echo "Found Fatima Zahra (ID: {$fatima->id}). Ensuring she is a student/volunteer...\n";
    $fatima->update(['role' => 'student']);
    RoleAssignment::where('user_id', $fatima->id)->where('role', 'president')->delete();
}

echo "Cleanup complete. Sarah Ahmed is now the unique President in all systems.\n";
