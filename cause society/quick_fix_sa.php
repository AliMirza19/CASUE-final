<?php
require_once 'config/db.php';

// Fix events 12 and 14 to be pending_sa
$stmt = $pdo->prepare("UPDATE events SET status = 'pending_sa' WHERE id IN (12, 14)");
$stmt->execute();

echo "✅ Fixed events 12 and 14 to pending_sa status<br>";

// Verify the fix
$stmt = $pdo->prepare("SELECT e.*, u.name as student_name 
                       FROM events e 
                       JOIN users u ON e.student_id = u.id 
                       WHERE e.status = 'pending_sa' AND e.term_id = 1");
$stmt->execute();
$sa_events = $stmt->fetchAll();

echo "Found " . count($sa_events) . " events for SA dashboard:<br>";
foreach ($sa_events as $event) {
    echo "- Event ID {$event['id']}: {$event['title']} by {$event['student_name']}<br>";
}

echo "<br><a href='sa_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px;'>Test SA Dashboard Now</a>";
?>