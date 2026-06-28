<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users from Role Assignments:\n";
$assignments = App\Models\RoleAssignment::with('user')->get();
foreach($assignments as $a) {
    if ($a->user) {
        echo "Role: {$a->role} | Name: {$a->user->name} | Email: {$a->user->email} | Reg ID: {$a->user->reg_id}\n";
    }
}

echo "\nUsers with non-default roles in Users table:\n";
$users = App\Models\User::whereNotIn('role', ['student', 'faculty', 'admin'])->get();
foreach($users as $u) {
    echo "Role: {$u->role} | Name: {$u->name} | Email: {$u->email} | Reg ID: {$u->reg_id}\n";
}

