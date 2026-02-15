<?php
// Create a test event with pending_sa status
require_once 'config/db.php';

try {
    // Get active term
    $stmt = $pdo->query("SELECT id FROM academic_terms WHERE status = 'active' LIMIT 1");
    $term = $stmt->fetch();
    $term_id = $term ? $term['id'] : 1;
    
    // Get a student user
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'student' LIMIT 1");
    $student = $stmt->fetch();
    $student_id = $student ? $student['id'] : 1;
    
    // Create test event
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at) 
                           VALUES (:title, :desc, :student_id, :term_id, :date, :venue, :budget, 'pending_sa', NOW())");
    $stmt->execute([
        'title' => 'Test SA Event - ' . date('Y-m-d H:i:s'),
        'desc' => 'This is a test event created to debug SA dashboard',
        'student_id' => $student_id,
        'term_id' => $term_id,
        'date' => date('Y-m-d', strtotime('+7 days')),
        'venue' => 'Test Venue',
        'budget' => 5000.00
    ]);
    
    $event_id = $pdo->lastInsertId();
    
    // Create test event items
    $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron) 
                           VALUES (:event_id, :name, :qty, :rate, :total, 1)");
    $stmt->execute([
        'event_id' => $event_id,
        'name' => 'Test Item 1',
        'qty' => 10,
        'rate' => 300.00,
        'total' => 3000.00
    ]);
    
    $stmt->execute([
        'event_id' => $event_id,
        'name' => 'Test Item 2',
        'qty' => 5,
        'rate' => 400.00,
        'total' => 2000.00
    ]);
    
    echo "<h2>✅ Test Event Created Successfully!</h2>";
    echo "<p><strong>Event ID:</strong> {$event_id}</p>";
    echo "<p><strong>Status:</strong> pending_sa</p>";
    echo "<p><strong>Term ID:</strong> {$term_id}</p>";
    echo "<p><strong>Budget:</strong> PKR 5,000.00</p>";
    
    echo "<br><div style='margin: 20px 0;'>";
    echo "<a href='sa_dashboard.php' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Go to SA Dashboard</a>";
    echo "<a href='debug_sa_issue.php' style='background: #7C3AED; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Debug Info</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    die("Error creating test event: " . $e->getMessage());
}
?>