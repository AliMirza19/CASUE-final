<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use App\Models\RoleAssignment;

$event = Event::where('status', 'approved')->latest()->first();
if ($event) {
    echo "Event: {$event->title}, Term ID: {$event->term_id}\n";
    $hodAssignment = RoleAssignment::getCurrentHod($event->term_id);
    if ($hodAssignment) {
        $hod = $hodAssignment->user;
        echo "Assigned HOD: {$hod->name}, Sig: {$hod->digital_signature}, Stamp: {$hod->digital_stamp}\n";
    } else {
        echo "No HOD assignment found for term {$event->term_id}\n";
    }
} else {
    echo "No approved events found.\n";
}
