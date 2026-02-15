<?php
// Check SA user and fix term ID issue
require_once 'config/db.php';

try {
    echo "<h2>SA User Check & Fix</h2>";
    
    // Check if SA user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'sa'");
    $sa_users = $stmt->fetchAll();
    
    echo "<h3>SA Users in Database:</h3>";
    if (empty($sa_users)) {
        echo "❌ No SA users found! Creating SA user...<br>";
        
        // Create SA user
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'SA-001',
            'name' => 'Fatima Ali',
            'email' => 'sa@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'sa',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✅ SA user created: SA-001 / 123456<br>";
    } else {
        foreach ($sa_users as $user) {
            echo "Found SA User: {$user['reg_id']} | Name: {$user['name']} | Term ID: {$user['current_term_id']}<br>";
        }
    }
    
    // Get active term
    $stmt = $pdo->query("SELECT * FROM academic_terms WHERE status = 'active'");
    $active_term = $stmt->fetch();
    echo "<h3>Active Term:</h3>";
    if ($active_term) {
        echo "Term ID: {$active_term['id']} | Name: {$active_term['term_name']}<br>";
        
        // Update SA user to have correct term ID
        $stmt = $pdo->prepare("UPDATE users SET current_term_id = :term_id WHERE role = 'sa'");
        $stmt->execute(['term_id' => $active_term['id']]);
        echo "✅ Updated SA user term ID to: {$active_term['id']}<br>";
    } else {
        echo "❌ No active term found!<br>";
    }
    
    // Check events with pending_sa status
    echo "<h3>Events with pending_sa status:</h3>";
    $stmt = $pdo->query("SELECT e.*, u.name as student_name FROM events e JOIN users u ON e.student_id = u.id WHERE e.status = 'pending_sa'");
    $pending_events = $stmt->fetchAll();
    
    if (empty($pending_events)) {
        echo "❌ No events with pending_sa status. Creating test event...<br>";
        
        // Get student ID
        $stmt = $pdo->query("SELECT id FROM users WHERE role = 'student' LIMIT 1");
        $student = $stmt->fetch();
        $student_id = $student ? $student['id'] : 1;
        
        // Create test event
        $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                               VALUES (:title, :desc, :student_id, :term_id, :date, :venue, :budget, 'pending_sa', NOW(), NOW())");
        $stmt->execute([
            'title' => 'SA Test Event - ' . date('H:i:s'),
            'desc' => 'Test event for SA dashboard',
            'student_id' => $student_id,
            'term_id' => $active_term['id'],
            'date' => date('Y-m-d', strtotime('+7 days')),
            'venue' => 'Test Hall',
            'budget' => 10000.00
        ]);
        
        $event_id = $pdo->lastInsertId();
        echo "✅ Created test event with ID: {$event_id}<br>";
        
        // Add test items
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron) 
                               VALUES (:event_id, :name, :qty, :rate, :total, 1)");
        $stmt->execute([
            'event_id' => $event_id,
            'name' => 'Test Item',
            'qty' => 1,
            'rate' => 10000,
            'total' => 10000
        ]);
        echo "✅ Added test item<br>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Student</th><th>Term ID</th><th>Budget</th></tr>";
        foreach ($pending_events as $event) {
            echo "<tr>";
            echo "<td>{$event['id']}</td>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td>{$event['student_name']}</td>";
            echo "<td>{$event['term_id']}</td>";
            echo "<td>PKR " . number_format($event['grand_total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Final verification
    echo "<h3>Final Verification:</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE status = 'pending_sa' AND term_id = :term_id");
    $stmt->execute(['term_id' => $active_term['id']]);
    $count = $stmt->fetch()['count'];
    
    echo "Events matching SA criteria: <strong>{$count}</strong><br>";
    
    if ($count > 0) {
        echo "✅ SA Dashboard should now show events!<br>";
    } else {
        echo "❌ Still no matching events. There might be another issue.<br>";
    }
    
    echo "<br><div style='margin: 20px 0;'>";
    echo "<a href='sa_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Test SA Dashboard Now</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>