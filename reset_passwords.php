<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

$password = Hash::make('password123');
$roles = ['hod', 'patron', 'president', 'vp', 'gs', 'treasurer', 'team_lead', 'gd', 'sa', 'vc', 'media_head', 'event_head'];

// Reset from role assignments
$assignments = App\Models\RoleAssignment::with('user')->get();
foreach($assignments as $a) {
    if ($a->user) {
        $a->user->update(['password' => $password]);
    }
}

// Reset from users table
$users = App\Models\User::whereNotIn('role', ['student', 'faculty', 'admin'])->get();
foreach($users as $u) {
    $u->update(['password' => $password]);
}

echo "Passwords reset successfully.\n";
