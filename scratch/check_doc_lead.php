<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use App\Models\User;

$activeTerm = AcademicTerm::getActive();
if (!$activeTerm) {
    echo "No active term found.\n";
    exit;
}

$docLead = RoleAssignment::where('term_id', $activeTerm->id)
    ->where('role', 'doc')
    ->where('is_active', true)
    ->first();

if ($docLead) {
    $user = User::find($docLead->user_id);
    echo "Doc Lead: " . $user->name . " (Reg ID: " . $user->reg_id . ")\n";
    echo "User Role in DB: " . $user->role . "\n";
} else {
    echo "No Documentation Team Lead assigned for the current term.\n";
}
