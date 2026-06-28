<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use App\Models\User;

$activeTerm = AcademicTerm::getActive();
echo "Active Term: " . ($activeTerm ? $activeTerm->term_name : "None") . "\n\n";

$roles = ['gd', 'photo', 'video', 'smt', 'doc', 'deco', 'sa'];

foreach ($roles as $role) {
    $assignment = RoleAssignment::where('role', $role)->where('is_active', true)->latest()->first();
    if ($assignment) {
        $user = User::find($assignment->user_id);
        echo "Role: " . strtoupper($role) . "\n";
        echo "User: " . ($user ? $user->name : "Unknown") . " (Reg: " . ($user ? $user->reg_id : "N/A") . ")\n";
        echo "User Role in DB: " . ($user ? $user->role : "N/A") . "\n";
        echo "-------------------\n";
    } else {
        echo "Role: " . strtoupper($role) . " - No active assignment found.\n";
        echo "-------------------\n";
    }
}
