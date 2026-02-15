<?php
// Setup tables for Graphic Designer and Volunteer Coordinator
require_once 'config/db.php';

echo "<h2>Setting up GD & VC Tables</h2>";

try {
    // Event Graphics table banao (Epic E7)
    echo "<h3>Creating event_graphics table...</h3>";
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
    echo "✅ event_graphics table created<br>";
    
    // Event Volunteers table banao
    echo "<h3>Creating event_volunteers table...</h3>";
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
    echo "✅ event_volunteers table created<br>";
    
    // Check if GD and VC users exist, create if not
    echo "<h3>Checking GD and VC users...</h3>";
    
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
    
    echo "<br>🎉 <strong>All tables and users created successfully!</strong><br>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='gd_dashboard.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>GD Dashboard</a>";
echo "<a href='vc_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>VC Dashboard</a>";
echo "</div>";
?>