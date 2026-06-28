<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RoleAssignment;

$hods = User::where('role', 'hod')->get();
echo "HODs by Role:\n";
foreach ($hods as $hod) {
    echo "ID: {$hod->id}, Name: {$hod->name}, Sig: {$hod->digital_signature}, Stamp: {$hod->digital_stamp}\n";
}

$assignments = RoleAssignment::where('role', 'hod')->where('is_active', true)->get();
echo "\nHOD Assignments:\n";
foreach ($assignments as $a) {
    $u = $a->user;
    echo "Term ID: {$a->term_id}, User: {$u->name}, Sig: {$u->digital_signature}\n";
}
