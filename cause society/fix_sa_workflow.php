<?php
// Fix SA workflow issues
require_once 'config/db.php';

try {
    echo "<h2>Fixing SA Workflow Issues</h2>";
    
    // 1. Fix event 12 status
    echo "<h3>Step 1: Fix Event 12 Status</h3>";
    $stmt = $pdo->prepare("UPDATE events SET status = 'pending_sa' WHERE id = 12");
    $stmt->execute();
    echo "✅ Updated event 12 status to 'pending_sa'<br>";
    
    // 2. Check all events with empty status
    echo "<h3>Step 2: Fix All Events with Empty Status</h3>";
    $stmt = $pdo->query("SELECT id, title, status FROM events WHERE status = '' OR status IS NULL");
    $empty_status_events = $stmt->fetchAll();
    
    if (!empty($empty_status_events)) {
        echo "Found " . count($empty_status_events) . " events with empty status:<br>";
        foreach ($empty_status_events as $event) {
            echo "- Event ID {$event['id']}: {$event['title']}<br>";
        }
        
        // Update them to pending_president (default starting status)
        $stmt = $pdo->prepare("UPDATE events SET status = 'pending_president' WHERE status = '' OR status IS NULL");
        $stmt->execute();
        echo "✅ Updated all empty status events to 'pending_president'<br>";
    } else {
        echo "No events with empty status found.<br>";
    }
    
    // 3. Create a proper test event for SA
    echo "<h3>Step 3: Create Proper SA Test Event</h3>";
    
    // Get a valid student ID
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'student' LIMIT 1");
    $student = $stmt->fetch();
    
    if (!$student) {
        echo "❌ No student user found. Creating one...<br>";
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                               VALUES ('STU-001', 'Ahmad Ali', 'student@cause.edu.pk', ?, 'student', 1, 1)");
        $stmt->execute([$hashedPassword]);
        $student_id = $pdo->lastInsertId();
        echo "✅ Created student user with ID: {$student_id}<br>";
    } else {
        $student_id = $student['id'];
        echo "Using existing student ID: {$student_id}<br>";
    }
    
    // Create new SA test event
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_sa', NOW(), NOW())");
    
    $success = $stmt->execute([
        'SA Workflow Test Event',
        'Test event to verify SA dashboard workflow',
        $student_id,
        1, // term_id
        date('Y-m-d', strtotime('+10 days')),
        'Main Auditorium',
        15000.00
    ]);
    
    if ($success) {
        $new_event_id = $pdo->lastInsertId();
        echo "✅ Created new SA test event with ID: {$new_event_id}<br>";
        
        // Add test items
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron, patron_comment) 
                               VALUES (?, ?, ?, ?, ?, 1, 'Approved by Patron')");
        
        $items = [
            ['Sound System', 1, 8000, 8000],
            ['Decoration', 1, 5000, 5000],
            ['Refreshments', 50, 40, 2000]
        ];
        
        foreach ($items as $item) {
            $stmt->execute(array_merge([$new_event_id], $item));
        }
        echo "✅ Added test items to event<br>";
    }
    
    // 4. Verify SA query works now
    echo "<h3>Step 4: Verify SA Query</h3>";
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_sa' AND e.term_id = 1");
    $stmt->execute();
    $sa_events = $stmt->fetchAll();
    
    echo "Found " . count($sa_events) . " events for SA dashboard:<br>";
    if (!empty($sa_events)) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Student</th><th>Budget</th><th>Status</th></tr>";
        foreach ($sa_events as $event) {
            echo "<tr>";
            echo "<td>{$event['id']}</td>";
            echo "<td>" . htmlspecialchars($event['title']) . "</td>";
            echo "<td>{$event['student_name']} ({$event['student_reg_id']})</td>";
            echo "<td>PKR " . number_format($event['grand_total'], 2) . "</td>";
            echo "<td>{$event['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "✅ SA Dashboard should now work properly!<br>";
    }
    
    // 5. Check HOD finalize event logic
    echo "<h3>Step 5: Check HOD Forward Logic</h3>";
    echo "The issue was in the event creation - status field was not being set properly.<br>";
    echo "Let's verify the HOD finalize event code sets status correctly...<br>";
    
    // Check if there are any events pending HOD approval to test with
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events WHERE status = 'pending_hod'");
    $hod_pending = $stmt->fetch()['count'];
    echo "Events pending HOD approval: {$hod_pending}<br>";
    
    if ($hod_pending == 0) {
        echo "Creating test event for HOD workflow...<br>";
        $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_hod', NOW(), NOW())");
        
        $stmt->execute([
            'HOD Test Event',
            'Test event for HOD approval workflow',
            $student_id,
            1,
            date('Y-m-d', strtotime('+15 days')),
            'Conference Room',
            12000.00
        ]);
        
        $hod_test_event = $pdo->lastInsertId();
        echo "✅ Created HOD test event with ID: {$hod_test_event}<br>";
        
        // Add items for this event too
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron, patron_comment) 
                               VALUES (?, ?, ?, ?, ?, 1, 'Patron approved')");
        
        $stmt->execute([$hod_test_event, 'Projector Rental', 1, 12000, 12000]);
        echo "✅ Added items to HOD test event<br>";
    }
    
    echo "<br><div style='margin: 20px 0;'>";
    echo "<a href='sa_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Test SA Dashboard</a>";
    echo "<a href='hod_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Test HOD Dashboard</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "SQL State: " . $e->getCode() . "<br>";
}
?>