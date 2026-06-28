<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicTerm;
use App\Models\RoleAssignment;

$activeTerm = AcademicTerm::where('status', 'active')->first();

if (!$activeTerm) {
    echo "No active term found\n";
    exit;
}

echo "Active Term: " . $activeTerm->term_name . "\n";
echo "--------------------------------------------------\n";

$assignments = RoleAssignment::where('term_id', $activeTerm->id)
    ->where('is_active', 1)
    ->with('user')
    ->get();

foreach ($assignments as $a) {
    echo "Role: " . strtoupper($a->role) . "\n";
    echo "Name: " . $a->user->name . "\n";
    echo "Registration ID: " . $a->user->reg_id . "\n";
    echo "Email: " . $a->user->email . "\n";
    echo "Password: (Default: password123)\n"; // Assuming default password as per previous context
    echo "--------------------------------------------------\n";
}
