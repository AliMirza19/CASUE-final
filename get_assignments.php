<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = ['hod', 'patron', 'president', 'vp', 'gs', 'treasurer', 'team_lead', 'gd', 'sa', 'vc', 'media_head', 'event_head'];

$assignments = App\Models\RoleAssignment::with('user')->whereIn('role', $roles)->get();

foreach($assignments as $a) {
    if ($a->user) {
        echo "Role: {$a->role} | Name: {$a->user->name} | Email: {$a->user->email} | Reg ID: {$a->user->reg_id}\n";
    }
}
