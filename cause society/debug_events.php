<?php
// Debug script to check event statuses
require_once 'config/db.php';

try {
    echo "<h2>Event Status Debug</h2>";
    
    // Show all events with their current status
    $stmt = $pdo->query("SELECT e.id, e.title, e.status, e.term_id, u.name as student_name, u.reg_id 
                         FROM events e 
                         JOIN users u ON e.student_id = u.id 
                         ORDER BY e.created_at DESC");
    $events = $stmt->fetchAll();
    
    echo "<h3>All Events:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Student</th><th>Status</th><th>Term ID</th></tr>";
    
    foreach ($events as $event) {
        $color = '';
        switch($event['status']) {
            case 'pending_president': $color = 'background-color: #fef3c7;'; break;
            case 'pending_patron': $color = 'background-color: #dbeafe;'; break;
            case 'pending_hod': $color = 'background-color: #e9d5ff;'; break;
            case 'pending_sa': $color = 'background-color: #fed7aa;'; break;
            case 'approved': $color = 'background-color: #dcfce7;'; break;
            case 'rejected': $color = 'background-color: #fee2e2;'; break;
        }
        
        echo "<tr style='{$color}'>";
        echo "<td>{$event['id']}</td>";
        echo "<td>" . htmlspecialchars($event['title']) . "</td>";
        echo "<td>" . htmlspecialchars($event['student_name']) . " ({$event['reg_id']})</td>";
        echo "<td><strong>{$event['status']}</strong></td>";
        echo "<td>{$event['term_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show current active term
    echo "<h3>Active Term Info:</h3>";
    $stmt = $pdo->query("SELECT * FROM academic_terms WHERE status = 'active'");
    $active_term = $stmt->fetch();
    if ($active_term) {
        echo "Active Term ID: {$active_term['id']}<br>";
        echo "Term Name: {$active_term['term_name']}<br>";
    } else {
        echo "No active term found!<br>";
    }
    
    // Show budget status
    echo "<h3>Budget Status:</h3>";
    $stmt = $pdo->query("SELECT * FROM budgets");
    $budgets = $stmt->fetchAll();
    foreach ($budgets as $budget) {
        echo "Term ID: {$budget['term_id']}, Total: {$budget['total_amount']}, Remaining: {$budget['remaining_amount']}, Locked: " . ($budget['is_locked'] ? 'Yes' : 'No') . "<br>";
    }
    
    // Show users and their term assignments
    echo "<h3>Users and Term Assignments:</h3>";
    $stmt = $pdo->query("SELECT reg_id, name, role, current_term_id FROM users ORDER BY role, reg_id");
    $users = $stmt->fetchAll();
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Reg ID</th><th>Name</th><th>Role</th><th>Current Term ID</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['reg_id']}</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['current_term_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>