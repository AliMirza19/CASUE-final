<?php
// Test script to manually forward an event from patron to HOD
require_once 'config/db.php';

try {
    // Find a pending_patron event
    $stmt = $pdo->query("SELECT * FROM events WHERE status = 'pending_patron' LIMIT 1");
    $event = $stmt->fetch();
    
    if ($event) {
        echo "<h2>Found Event to Forward:</h2>";
        echo "ID: {$event['id']}<br>";
        echo "Title: " . htmlspecialchars($event['title']) . "<br>";
        echo "Current Status: {$event['status']}<br>";
        echo "Term ID: {$event['term_id']}<br><br>";
        
        // Update status to pending_hod
        $stmt = $pdo->prepare("UPDATE events SET status = 'pending_hod' WHERE id = :id");
        $stmt->execute(['id' => $event['id']]);
        
        echo "✓ Event status updated to 'pending_hod'<br>";
        echo "<a href='hod_dashboard.php'>Check HOD Dashboard</a> | <a href='debug_events.php'>View All Events</a>";
        
    } else {
        echo "No events with status 'pending_patron' found.<br>";
        echo "<a href='debug_events.php'>View All Events</a>";
    }
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>