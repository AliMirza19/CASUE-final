<?php
require_once 'config/db.php';

echo "<h2>Direct SA Fix</h2>";

try {
    // First, let's see the exact structure of the events table
    echo "<h3>Events Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo "- {$col['Field']}: {$col['Type']} (Default: {$col['Default']})<br>";
    }
    
    // Check if there are any triggers
    echo "<h3>Check for Triggers:</h3>";
    $stmt = $pdo->query("SHOW TRIGGERS LIKE 'events'");
    $triggers = $stmt->fetchAll();
    if (empty($triggers)) {
        echo "No triggers found on events table.<br>";
    } else {
        foreach ($triggers as $trigger) {
            echo "Trigger: {$trigger['Trigger']} - {$trigger['Event']} {$trigger['Timing']}<br>";
        }
    }
    
    // Try a different approach - direct SQL
    echo "<h3>Direct SQL Update:</h3>";
    $pdo->exec("UPDATE events SET status = 'pending_sa' WHERE id = 12");
    echo "Executed direct UPDATE command<br>";
    
    // Check immediately
    $stmt = $pdo->query("SELECT id, status FROM events WHERE id = 12");
    $event = $stmt->fetch();
    echo "Event 12 status after update: '{$event['status']}'<br>";
    
    // Try inserting a completely new event with pending_sa status
    echo "<h3>Creating New Event with pending_sa:</h3>";
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                           VALUES ('Direct SA Test', 'Direct test for SA', 4, 1, '2026-01-15', 'Test Venue', 5000.00, 'pending_sa', NOW(), NOW())");
    $result = $stmt->execute();
    
    if ($result) {
        $new_id = $pdo->lastInsertId();
        echo "✅ Created new event with ID: {$new_id}<br>";
        
        // Check its status immediately
        $stmt = $pdo->prepare("SELECT id, status FROM events WHERE id = ?");
        $stmt->execute([$new_id]);
        $new_event = $stmt->fetch();
        echo "New event status: '{$new_event['status']}'<br>";
        
        // Add items for this event
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron) 
                               VALUES (?, 'Direct Test Item', 1, 5000, 5000, 1)");
        $stmt->execute([$new_id]);
        echo "✅ Added items to new event<br>";
        
        // Test SA query with this event
        echo "<h3>Testing SA Query:</h3>";
        $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                               FROM events e 
                               JOIN users u ON e.student_id = u.id 
                               WHERE e.status = 'pending_sa' AND e.term_id = 1");
        $stmt->execute();
        $sa_events = $stmt->fetchAll();
        
        echo "SA Query found: " . count($sa_events) . " events<br>";
        foreach ($sa_events as $event) {
            echo "- Event {$event['id']}: {$event['title']} by {$event['student_name']}<br>";
        }
        
        if (count($sa_events) > 0) {
            echo "✅ SA Dashboard should work now!<br>";
        }
    } else {
        echo "❌ Failed to create new event<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='sa_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px;'>Test SA Dashboard</a>";
?>