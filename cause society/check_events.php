<?php
require_once 'config/db.php';

echo "Checking events 12 and 14:\n";
$stmt = $pdo->query("SELECT id, title, status FROM events WHERE id IN (12, 14)");
$events = $stmt->fetchAll();
foreach ($events as $event) {
    echo "Event {$event['id']}: {$event['title']} - Status: '{$event['status']}'\n";
}

echo "\nAll events and their statuses:\n";
$stmt = $pdo->query("SELECT id, title, status FROM events ORDER BY id");
$all_events = $stmt->fetchAll();
foreach ($all_events as $event) {
    echo "Event {$event['id']}: {$event['title']} - Status: '{$event['status']}'\n";
}

// Manually set event 12 to pending_sa
echo "\nManually setting event 12 to pending_sa...\n";
$stmt = $pdo->prepare("UPDATE events SET status = 'pending_sa' WHERE id = 12");
$result = $stmt->execute();
echo $result ? "✅ Success" : "❌ Failed";

// Verify
echo "\nVerifying...\n";
$stmt = $pdo->query("SELECT id, title, status FROM events WHERE status = 'pending_sa'");
$pending_sa = $stmt->fetchAll();
echo "Events with pending_sa status: " . count($pending_sa) . "\n";
foreach ($pending_sa as $event) {
    echo "- Event {$event['id']}: {$event['title']}\n";
}
?>