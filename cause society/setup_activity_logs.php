<?php
// Setup Activity Logs System - Final Polish
require_once 'config/db.php';

echo "<h2>Setting up Activity Logs & Final Polish</h2>";

try {
    // Activity Logs table banao
    echo "<h3>Creating activity_logs table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_role VARCHAR(20) NOT NULL,
        action_text TEXT NOT NULL,
        related_event_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (related_event_id) REFERENCES events(id) ON DELETE SET NULL,
        INDEX idx_user_role (user_role),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ activity_logs table created<br>";
    
    // Sample activity logs create karo
    echo "<h3>Creating Sample Activity Logs...</h3>";
    
    // Get user IDs for different roles
    $users = [];
    $roles = ['admin', 'hod', 'student', 'patron', 'president', 'sa', 'gd', 'vc'];
    
    foreach ($roles as $role) {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = ? LIMIT 1");
        $stmt->execute([$role]);
        $user = $stmt->fetch();
        if ($user) {
            $users[$role] = $user;
        }
    }
    
    // Sample activities
    $activities = [
        ['admin', 'New academic term "Fall 2024" created and activated'],
        ['hod', 'Term budget of PKR 500,000 has been set and locked'],
        ['hod', 'Patron "Prof. Muhammad Khan" assigned for current term'],
        ['student', 'New event "Tech Conference 2024" submitted for approval'],
        ['president', 'Event "Tech Conference 2024" reviewed and forwarded to Patron'],
        ['patron', 'Event budget items reviewed and approved for "Tech Conference 2024"'],
        ['hod', 'Event "Tech Conference 2024" given final approval with PKR 25,000 budget'],
        ['sa', 'Event "Tech Conference 2024" approved by Student Affairs'],
        ['gd', 'Graphics design uploaded for "Tech Conference 2024"'],
        ['vc', 'Volunteers assigned for "Tech Conference 2024"'],
        ['student', 'Candidate profile submitted for Society President election'],
        ['patron', 'Election candidate profile approved'],
        ['student', 'Vote cast in Society President election']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, created_at) 
                           VALUES (?, ?, ?, DATE_SUB(NOW(), INTERVAL ? HOUR))");
    
    foreach ($activities as $index => $activity) {
        $role = $activity[0];
        $action = $activity[1];
        
        if (isset($users[$role])) {
            $stmt->execute([
                $users[$role]['id'],
                $role,
                $action,
                (count($activities) - $index) * 2 // Spread activities over time
            ]);
        }
    }
    
    echo "✅ Created " . count($activities) . " sample activity logs<br>";
    
    // Announcements table banao (bonus feature)
    echo "<h3>Creating announcements table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_by INT NOT NULL,
        target_roles JSON,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ announcements table created<br>";
    
    // Sample announcements
    if (isset($users['admin'])) {
        $announcements = [
            [
                'Society Elections 2024',
                'Voting for Society President is now open! Cast your vote through the student portal. Voting ends on December 31, 2024.',
                '["student", "president", "patron"]'
            ],
            [
                'Budget Allocation Update',
                'Term budget has been finalized. All departments can now submit event proposals through the proper channels.',
                '["hod", "patron", "student"]'
            ],
            [
                'Graphics Design Guidelines',
                'New guidelines for event graphics have been published. Please ensure all designs follow CAUSE branding standards.',
                '["gd", "student"]'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, created_by, target_roles) VALUES (?, ?, ?, ?)");
        
        foreach ($announcements as $announcement) {
            $stmt->execute([
                $announcement[0],
                $announcement[1], 
                $users['admin']['id'],
                $announcement[2]
            ]);
        }
        
        echo "✅ Created sample announcements<br>";
    }
    
    echo "<br>🎉 <strong>Activity Logs System Setup Complete!</strong><br>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='test_activity_logs.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Test Activity Logs</a>";
echo "<a href='final_setup.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Run Final Setup</a>";
echo "</div>";
?>