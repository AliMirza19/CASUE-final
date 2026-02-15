<?php
// Debug event ID 12 specifically
require_once 'config/db.php';

try {
    echo "<h2>Debug Event ID 12</h2>";
    
    // Check if event 12 exists
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = 12");
    $stmt->execute();
    $event = $stmt->fetch();
    
    if ($event) {
        echo "<h3>Event 12 Details:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        foreach ($event as $key => $value) {
            echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
        }
        echo "</table>";
        
        // Check why it's not matching the SA query
        echo "<h3>SA Query Test:</h3>";
        echo "Event Status: <strong>{$event['status']}</strong><br>";
        echo "Event Term ID: <strong>{$event['term_id']}</strong><br>";
        echo "Expected: status = 'pending_sa' AND term_id = 1<br>";
        
        if ($event['status'] === 'pending_sa' && $event['term_id'] == 1) {
            echo "✅ Event should match SA query!<br>";
        } else {
            echo "❌ Event doesn't match criteria:<br>";
            if ($event['status'] !== 'pending_sa') {
                echo "- Status is '{$event['status']}' instead of 'pending_sa'<br>";
            }
            if ($event['term_id'] != 1) {
                echo "- Term ID is '{$event['term_id']}' instead of 1<br>";
            }
        }
        
        // Test the exact SA query
        echo "<h3>Testing SA Query:</h3>";
        $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                               FROM events e 
                               JOIN users u ON e.student_id = u.id 
                               WHERE e.status = 'pending_sa' AND e.term_id = :term_id");
        $stmt->execute(['term_id' => 1]);
        $results = $stmt->fetchAll();
        
        echo "Query returned " . count($results) . " results<br>";
        
        if (!empty($results)) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Term ID</th><th>Student</th></tr>";
            foreach ($results as $result) {
                echo "<tr>";
                echo "<td>{$result['id']}</td>";
                echo "<td>" . htmlspecialchars($result['title']) . "</td>";
                echo "<td>{$result['status']}</td>";
                echo "<td>{$result['term_id']}</td>";
                echo "<td>{$result['student_name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "❌ Event ID 12 not found in database!<br>";
        
        // Check what events do exist
        echo "<h3>All Events in Database:</h3>";
        $stmt = $pdo->query("SELECT id, title, status, term_id FROM events ORDER BY id DESC LIMIT 10");
        $all_events = $stmt->fetchAll();
        
        if (!empty($all_events)) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Term ID</th></tr>";
            foreach ($all_events as $evt) {
                echo "<tr>";
                echo "<td>{$evt['id']}</td>";
                echo "<td>" . htmlspecialchars($evt['title']) . "</td>";
                echo "<td>{$evt['status']}</td>";
                echo "<td>{$evt['term_id']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No events found in database!<br>";
        }
    }
    
    // Manual fix - create event with explicit values
    echo "<h3>Manual Fix - Creating New Test Event:</h3>";
    
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $success = $stmt->execute([
        'MANUAL SA TEST EVENT',
        'Manually created test event for SA dashboard',
        1, // student_id
        1, // term_id
        date('Y-m-d', strtotime('+5 days')),
        'Manual Test Venue',
        8000.00,
        'pending_sa'
    ]);
    
    if ($success) {
        $new_event_id = $pdo->lastInsertId();
        echo "✅ Manually created event with ID: {$new_event_id}<br>";
        
        // Verify it was created correctly
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$new_event_id]);
        $new_event = $stmt->fetch();
        
        echo "New event status: {$new_event['status']}<br>";
        echo "New event term_id: {$new_event['term_id']}<br>";
        
        // Test query again
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE status = 'pending_sa' AND term_id = 1");
        $stmt->execute();
        $count = $stmt->fetch()['count'];
        echo "Total pending_sa events for term 1: <strong>{$count}</strong><br>";
        
    } else {
        echo "❌ Failed to create manual event<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<br><br><a href='sa_dashboard.php' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test SA Dashboard</a>";
?>