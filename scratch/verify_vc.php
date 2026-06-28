<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$vc = User::where('role', 'vc')->first();

if ($vc) {
    echo "Volunteer Coordinator found:\n";
    echo "Name: " . $vc->name . "\n";
    echo "Email: " . $vc->email . "\n";
    echo "Reg ID: " . $vc->reg_id . "\n";
    
    // Reset password just in case
    $vc->password = Hash::make('123456');
    $vc->save();
    echo "Password has been reset to: 123456\n";
} else {
    echo "No Volunteer Coordinator found in the database.\n";
    
    // Let's check all roles to see what roles exist
    $roles = User::distinct()->pluck('role');
    echo "Existing roles: " . implode(', ', $roles->toArray()) . "\n";
}
