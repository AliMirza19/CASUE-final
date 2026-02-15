<?php
require_once 'config/db.php';

echo "<h2>Fix Event Status ENUM</h2>";

try {
    // Add 'pending_sa' to the status ENUM
    echo "<h3>Adding 'pending_sa' to status ENUM...</h3>";
    $sql = "ALTER TABLE events MODIFY COLUMN status ENUM('pending_president','pending_patron','pending_hod','pending_sa','approved','rejected','completed') DEFAULT 'pending_president'";
    $pdo->exec($sql);
    echo "✅ Successfully added 'pending_sa' to status ENUM<br>";
    
    // Verify the change
    echo "<h3>Verifying ENUM Change:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM events LIKE 'status'");
    $column = $stmt->fetch();
    echo "Status column type: {$column['Type']}<br>";
    
    // Now update event 12 to pending_sa
    echo "<h3>Updating Event 12 to pending_sa:</h3>";
    $stmt = $pdo->prepare("UPDATE events SET status = 'pending_sa' WHERE id = 12");
    $result = $stmt->execute();
    echo $result ? "✅ Success" : "❌ Failed";
    echo "<br>";
    
    // Verify the update
    $stmt = $pdo->query("SELECT id, title, status FROM events WHERE id = 12");
    $event = $stmt->fetch();
    echo "Event 12 status: '{$event['status']}'<br>";
    
    // Also update event 14
    echo "<h3>Updating Event 14 to pending_sa:</h3>";
    $stmt = $pdo->prepare("UPDATE events SET status = 'pending_sa' WHERE id = 14");
    $result = $stmt->execute();
    echo $result ? "✅ Success" : "❌ Failed";
    echo "<br>";
    
    // Create a new test event with pending_sa status
    echo "<h3>Creating New Test Event:</h3>";
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                           VALUES ('Final SA Test Event', 'Test event with correct ENUM', 4, 1, '2026-01-20', 'Final Test Hall', 8000.00, 'pending_sa', NOW(), NOW())");
    $result = $stmt->execute();
    
    if ($result) {
        $new_id = $pdo->lastInsertId();
        echo "✅ Created new event with ID: {$new_id}<br>";
        
        // Add items
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron, patron_comment) 
                               VALUES (?, ?, ?, ?, ?, 1, 'Approved for SA testing')");
        
        $items = [
            [$new_id, 'Audio Equipment', 1, 4000, 4000],
            [$new_id, 'Catering', 20, 200, 4000]
        ];
        
        foreach ($items as $item) {
            $stmt->execute($item);
        }
        echo "✅ Added test items<br>";
    }
    
    // Final verification - test SA query
    echo "<h3>Final SA Query Test:</h3>";
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_sa' AND e.term_id = 1");
    $stmt->execute();
    $sa_events = $stmt->fetchAll();
    
    echo "Found " . count($sa_events) . " events for SA dashboard:<br>";
    if (!empty($sa_events)) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Student</th><th>Budget</th></tr>";
        foreach ($sa_events as $event) {
            echo "<tr>";
            echo "<td>{$event['id']}</td>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td>{$event['student_name']} ({$event['student_reg_id']})</td>";
            echo "<td>PKR " . number_format($event['grand_total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>🎉 <strong>SA Dashboard should now work perfectly!</strong><br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='sa_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Test SA Dashboard</a>";
echo "<a href='hod_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Test HOD Dashboard</a>";
echo "</div>";
?>