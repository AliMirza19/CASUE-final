<?php
// Simple SA test - Direct query without includes
session_start();
require_once 'config/db.php';

// Simulate SA user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'sa';
$_SESSION['term_id'] = 1;
$_SESSION['name'] = 'Test SA User';

echo "<h2>Simple SA Dashboard Test</h2>";

try {
    // Direct query
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_sa' AND e.term_id = :term_id
                           ORDER BY e.updated_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetchAll();
    
    echo "<p><strong>Query executed successfully!</strong></p>";
    echo "<p>Term ID: {$_SESSION['term_id']}</p>";
    echo "<p>Found events: " . count($pending_events) . "</p>";
    
    if (!empty($pending_events)) {
        echo "<h3>Pending Events:</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Student</th><th>Status</th><th>Budget</th></tr>";
        
        foreach ($pending_events as $event) {
            echo "<tr>";
            echo "<td>{$event['id']}</td>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td>{$event['student_name']} ({$event['student_reg_id']})</td>";
            echo "<td>{$event['status']}</td>";
            echo "<td>PKR " . number_format($event['grand_total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='color: green; font-weight: bold;'>✅ Events found! The SA dashboard should work.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ No events found with status 'pending_sa' and term_id = 1</p>";
        
        // Check all pending_sa events regardless of term
        $stmt = $pdo->query("SELECT e.*, u.name as student_name FROM events e JOIN users u ON e.student_id = u.id WHERE e.status = 'pending_sa'");
        $all_pending = $stmt->fetchAll();
        
        if (!empty($all_pending)) {
            echo "<h4>All pending_sa events (any term):</h4>";
            foreach ($all_pending as $event) {
                echo "ID: {$event['id']}, Title: {$event['title']}, Term: {$event['term_id']}<br>";
            }
        } else {
            echo "<p>No pending_sa events found at all.</p>";
        }
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='check_sa_user.php'>Run SA User Check</a> | <a href='sa_dashboard.php'>Go to SA Dashboard</a>";
?>