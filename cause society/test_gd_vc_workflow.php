<?php
// Test GD & VC Workflow
require_once 'config/db.php';

echo "<h2>🧪 Testing GD & VC Workflow</h2>";

try {
    // Step 1: Create tables if they don't exist
    echo "<h3>Step 1: Setting up Tables</h3>";
    
    // Event Graphics table
    $sql = "CREATE TABLE IF NOT EXISTS event_graphics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        gd_id INT NOT NULL,
        design_category ENUM('poster', 'banner', 'social_media') NOT NULL,
        image_path VARCHAR(500),
        image_link VARCHAR(500),
        status ENUM('pending_patron', 'approved', 'rejected') DEFAULT 'pending_patron',
        patron_feedback TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (gd_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✅ event_graphics table ready<br>";
    
    // Event Volunteers table
    $sql = "CREATE TABLE IF NOT EXISTS event_volunteers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        vc_id INT NOT NULL,
        volunteer_name VARCHAR(255) NOT NULL,
        volunteer_contact VARCHAR(50),
        role_description VARCHAR(255) NOT NULL,
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (vc_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✅ event_volunteers table ready<br>";
    
    // Step 2: Check/Create GD and VC users
    echo "<h3>Step 2: Setting up GD & VC Users</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'gd'");
    $gd_count = $stmt->fetch()['count'];
    
    if ($gd_count == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                               VALUES ('GD-001', 'Hassan Ali', 'gd@cause.edu.pk', ?, 'gd', 1, 1)");
        $stmt->execute([$hashedPassword]);
        echo "✅ Created GD user: GD-001 / 123456<br>";
    } else {
        echo "✅ GD user already exists<br>";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'vc'");
    $vc_count = $stmt->fetch()['count'];
    
    if ($vc_count == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                               VALUES ('VC-001', 'Dr. Ayesha Khan', 'vc@cause.edu.pk', ?, 'vc', 1, 1)");
        $stmt->execute([$hashedPassword]);
        echo "✅ Created VC user: VC-001 / 123456<br>";
    } else {
        echo "✅ VC user already exists<br>";
    }
    
    // Step 3: Find an approved event for testing
    echo "<h3>Step 3: Testing with Approved Event</h3>";
    $stmt = $pdo->query("SELECT * FROM events WHERE status = 'approved' LIMIT 1");
    $approved_event = $stmt->fetch();
    
    if (!$approved_event) {
        echo "❌ No approved events found. Creating test approved event...<br>";
        
        // Create test approved event
        $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                               VALUES ('GD VC Test Event', 'Test event for graphics and volunteers', 4, 1, '2026-02-15', 'Test Auditorium', 25000.00, 'approved', NOW(), NOW())");
        $stmt->execute();
        $approved_event_id = $pdo->lastInsertId();
        echo "✅ Created test approved event with ID: {$approved_event_id}<br>";
    } else {
        $approved_event_id = $approved_event['id'];
        echo "✅ Using existing approved event: {$approved_event['title']} (ID: {$approved_event_id})<br>";
    }
    
    // Step 4: Test GD workflow - Create graphics
    echo "<h3>Step 4: Testing GD Workflow</h3>";
    
    // Get GD user ID
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'gd' LIMIT 1");
    $gd_user = $stmt->fetch();
    $gd_id = $gd_user['id'];
    
    // Create test graphics
    $stmt = $pdo->prepare("INSERT INTO event_graphics (event_id, gd_id, design_category, image_link, status, created_at) 
                           VALUES (?, ?, ?, ?, 'pending_patron', NOW())");
    
    $graphics = [
        [$approved_event_id, $gd_id, 'poster', 'https://via.placeholder.com/800x600/7C3AED/FFFFFF?text=Event+Poster'],
        [$approved_event_id, $gd_id, 'banner', 'https://via.placeholder.com/1200x400/7C3AED/FFFFFF?text=Event+Banner'],
        [$approved_event_id, $gd_id, 'social_media', 'https://via.placeholder.com/600x600/7C3AED/FFFFFF?text=Social+Media+Post']
    ];
    
    foreach ($graphics as $graphic) {
        $stmt->execute($graphic);
    }
    echo "✅ Created 3 test graphics (poster, banner, social media)<br>";
    
    // Step 5: Test VC workflow - Assign volunteers
    echo "<h3>Step 5: Testing VC Workflow</h3>";
    
    // Get VC user ID
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'vc' LIMIT 1");
    $vc_user = $stmt->fetch();
    $vc_id = $vc_user['id'];
    
    // Create test volunteers
    $stmt = $pdo->prepare("INSERT INTO event_volunteers (event_id, vc_id, volunteer_name, volunteer_contact, role_description, assigned_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())");
    
    $volunteers = [
        [$approved_event_id, $vc_id, 'Ahmed Hassan', '0300-1234567', 'Registration Desk'],
        [$approved_event_id, $vc_id, 'Fatima Khan', '0301-2345678', 'Security & Crowd Control'],
        [$approved_event_id, $vc_id, 'Ali Raza', '0302-3456789', 'Stage Management'],
        [$approved_event_id, $vc_id, 'Sara Ahmed', '0303-4567890', 'Photography/Videography'],
        [$approved_event_id, $vc_id, 'Hassan Ali', '', 'General Support']
    ];
    
    foreach ($volunteers as $volunteer) {
        $stmt->execute($volunteer);
    }
    echo "✅ Assigned 5 test volunteers with different roles<br>";
    
    // Step 6: Verify data
    echo "<h3>Step 6: Verification</h3>";
    
    // Check graphics
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event_graphics WHERE event_id = ?");
    $stmt->execute([$approved_event_id]);
    $graphics_count = $stmt->fetch()['count'];
    echo "Graphics created: <strong>{$graphics_count}</strong><br>";
    
    // Check volunteers
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event_volunteers WHERE event_id = ?");
    $stmt->execute([$approved_event_id]);
    $volunteers_count = $stmt->fetch()['count'];
    echo "Volunteers assigned: <strong>{$volunteers_count}</strong><br>";
    
    // Check patron pending graphics
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM event_graphics WHERE status = 'pending_patron'");
    $pending_graphics = $stmt->fetch()['count'];
    echo "Graphics pending patron approval: <strong>{$pending_graphics}</strong><br>";
    
    echo "<br>🎉 <strong>GD & VC Workflow Test Completed Successfully!</strong><br>";
    
    echo "<h3>Login Credentials:</h3>";
    echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px;'>";
    echo "<strong>Graphic Designer:</strong> GD-001 / 123456<br>";
    echo "<strong>Volunteer Coordinator:</strong> VC-001 / 123456<br>";
    echo "<strong>Patron (for graphics approval):</strong> PAT-001 / 123456<br>";
    echo "<strong>Student (to view graphics & volunteers):</strong> STU-001 / 123456<br>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='gd_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>GD Dashboard</a>";
echo "<a href='vc_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>VC Dashboard</a>";
echo "<a href='patron_dashboard.php' style='background: #dc2626; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Patron Dashboard</a>";
echo "<a href='student_dashboard.php' style='background: #16a34a; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Student Dashboard</a>";
echo "</div>";
?>