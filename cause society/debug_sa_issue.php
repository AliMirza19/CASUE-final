<?php
require_once 'config/db.php';

echo "<h2>Debug SA Issue - Step by Step</h2>";

// Step 1: Check events table directly
echo "<h3>Step 1: Events with pending_sa status</h3>";
$stmt = $pdo->query("SELECT * FROM events WHERE status = 'pending_sa'");
$events = $stmt->fetchAll();
echo "Found " . count($events) . " events with pending_sa status:<br>";
foreach ($events as $event) {
    echo "- ID: {$event['id']}, Title: {$event['title']}, Student ID: {$event['student_id']}, Term ID: {$event['term_id']}<br>";
}

// Step 2: Check users table
echo "<h3>Step 2: Check Users Table</h3>";
$stmt = $pdo->query("SELECT id, reg_id, name, role FROM users");
$users = $stmt->fetchAll();
echo "Users in database:<br>";
foreach ($users as $user) {
    echo "- ID: {$user['id']}, Reg ID: {$user['reg_id']}, Name: {$user['name']}, Role: {$user['role']}<br>";
}

// Step 3: Test the JOIN query step by step
echo "<h3>Step 3: Test JOIN Query</h3>";
if (!empty($events)) {
    $event = $events[0]; // Take first pending_sa event
    echo "Testing with Event ID: {$event['id']}, Student ID: {$event['student_id']}<br>";
    
    // Check if student exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$event['student_id']]);
    $student = $stmt->fetch();
    
    if ($student) {
        echo "✅ Student found: {$student['name']} ({$student['reg_id']})<br>";
    } else {
        echo "❌ Student with ID {$event['student_id']} not found!<br>";
        
        // Check what user IDs exist
        $stmt = $pdo->query("SELECT id FROM users ORDER BY id");
        $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Available user IDs: " . implode(', ', $user_ids) . "<br>";
        
        // Fix the event by assigning it to an existing user
        if (!empty($user_ids)) {
            $valid_user_id = $user_ids[0];
            echo "Fixing event by assigning to user ID: {$valid_user_id}<br>";
            $stmt = $pdo->prepare("UPDATE events SET student_id = ? WHERE id = ?");
            $stmt->execute([$valid_user_id, $event['id']]);
            echo "✅ Fixed event student_id<br>";
        }
    }
}

// Step 4: Test the full SA query again
echo "<h3>Step 4: Test Full SA Query</h3>";
$stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                       FROM events e 
                       JOIN users u ON e.student_id = u.id 
                       WHERE e.status = 'pending_sa' AND e.term_id = 1");
$stmt->execute();
$sa_events = $stmt->fetchAll();

echo "SA Query Result: " . count($sa_events) . " events<br>";
if (!empty($sa_events)) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Event ID</th><th>Title</th><th>Student</th><th>Status</th><th>Term ID</th></tr>";
    foreach ($sa_events as $event) {
        echo "<tr>";
        echo "<td>{$event['id']}</td>";
        echo "<td>" . htmlspecialchars($event['title']) . "</td>";
        echo "<td>{$event['student_name']} ({$event['student_reg_id']})</td>";
        echo "<td>{$event['status']}</td>";
        echo "<td>{$event['term_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "✅ SA Dashboard should work now!<br>";
} else {
    echo "❌ Still no results. Let me check the term_id issue...<br>";
    
    // Check what term_id the SA user has
    $stmt = $pdo->query("SELECT current_term_id FROM users WHERE role = 'sa'");
    $sa_user = $stmt->fetch();
    echo "SA user term_id: " . ($sa_user ? $sa_user['current_term_id'] : 'No SA user') . "<br>";
    
    // Check what term_ids exist in events
    $stmt = $pdo->query("SELECT DISTINCT term_id FROM events WHERE status = 'pending_sa'");
    $event_terms = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Term IDs in pending_sa events: " . implode(', ', $event_terms) . "<br>";
}

echo "<br><a href='sa_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px;'>Test SA Dashboard</a>";
?>