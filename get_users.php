<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = ['hod', 'patron', 'president', 'vp', 'gs', 'treasurer', 'gd', 'sa', 'vc', 'media_head', 'event_head'];
$users = App\Models\User::whereIn('role', $roles)->get();

foreach($users as $u) {
    echo $u->role . ' | ' . $u->email . ' | ' . $u->reg_id . "\n";
}
