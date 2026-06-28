<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Event;
use App\Models\AcademicTerm;

$user = User::where('email', 'vc@cause.com')->first();
$activeTerm = AcademicTerm::where('status', 'active')->first();

if ($user && $activeTerm) {
    echo "User Term: " . $user->current_term_id . "\n";
    echo "Active Term: " . $activeTerm->id . "\n";
    
    if ($user->current_term_id != $activeTerm->id) {
        echo "Fixing User Term mismatch...\n";
        $user->update(['current_term_id' => $activeTerm->id]);
    }
}

$events = Event::where('status', 'approved')->get();
echo "Total Approved Events: " . $events->count() . "\n";
foreach ($events as $e) {
    echo "Event: " . $e->title . " | Term: " . $e->term_id . "\n";
    if ($activeTerm && $e->term_id != $activeTerm->id) {
        echo "Updating event term to match active term...\n";
        $e->update(['term_id' => $activeTerm->id]);
    }
}

echo "Verification complete.\n";
