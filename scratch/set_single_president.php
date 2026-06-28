<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// 1. Find Sarah Ahmed or create her if she doesn't exist
$sarah = User::where('name', 'like', '%Sarah Ahmed%')->first();

if (!$sarah) {
    echo "Sarah Ahmed not found. Creating her as the only President...\n";
    $sarah = User::create([
        'name' => 'Sarah Ahmed',
        'email' => 'president@cause.com',
        'password' => bcrypt('123456'),
        'role' => 'president',
        'reg_id' => 'BSE223500'
    ]);
} else {
    echo "Found Sarah Ahmed (ID: {$sarah->id}). Setting her role to President...\n";
    $sarah->update(['role' => 'president']);
}

// 2. Remove president role from everyone else
$others = User::where('role', 'president')
    ->where('id', '!=', $sarah->id)
    ->get();

echo "Removing President role from " . $others->count() . " other users...\n";

foreach ($others as $other) {
    echo "Updating {$other->name} (ID: {$other->id}) to student role...\n";
    $other->update(['role' => 'student']);
}

echo "Done. Sarah Ahmed is now the only President.\n";
