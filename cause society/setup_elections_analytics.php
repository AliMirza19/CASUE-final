<?php
// Setup Elections and Analytics Tables
require_once 'config/db.php';

echo "<h2>Setting up Elections & Analytics Tables</h2>";

try {
    // Candidate Profiles table banao (Epic E8)
    echo "<h3>Creating candidate_profiles table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS candidate_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        manifesto TEXT NOT NULL,
        photo_url VARCHAR(500),
        experience TEXT,
        vp_name VARCHAR(255),
        status ENUM('pending_patron', 'approved', 'rejected') DEFAULT 'pending_patron',
        patron_feedback TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_student_candidate (student_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ candidate_profiles table created<br>";
    
    // Votes table banao (Election System)
    echo "<h3>Creating votes table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        candidate_id INT NOT NULL,
        term_id INT NOT NULL,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (candidate_id) REFERENCES candidate_profiles(id) ON DELETE CASCADE,
        FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
        UNIQUE KEY unique_student_vote (student_id, term_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ votes table created<br>";
    
    // Election Settings table banao (Voting control)
    echo "<h3>Creating election_settings table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS election_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        term_id INT NOT NULL,
        voting_enabled TINYINT(1) DEFAULT 0,
        voting_start_date DATETIME,
        voting_end_date DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
        UNIQUE KEY unique_term_election (term_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ election_settings table created<br>";
    
    // Create test candidate profiles
    echo "<h3>Creating Test Candidate Profiles...</h3>";
    
    // Get student users
    $stmt = $pdo->query("SELECT id, name, reg_id FROM users WHERE role = 'student' LIMIT 3");
    $students = $stmt->fetchAll();
    
    if (!empty($students)) {
        $manifestos = [
            "I envision a society where every student's voice is heard and every idea is valued. My focus will be on improving academic support, organizing more engaging events, and creating better communication channels between students and administration. Together, we can build a stronger, more inclusive CAUSE community.",
            
            "As your representative, I pledge to work tirelessly for student welfare, enhanced facilities, and meaningful extracurricular activities. My experience in organizing successful events and my commitment to transparency make me the right choice for leading our society towards excellence.",
            
            "My mission is to bridge the gap between students and faculty, ensure fair representation of all departments, and create opportunities for skill development and networking. With your support, we can transform our society into a platform for growth and success."
        ];
        
        $experiences = [
            "Event Coordinator for Annual Tech Fest 2024, President of Computer Science Society, Volunteer at Community Service Projects, Academic Excellence Award recipient",
            
            "Vice President of Debate Society, Organizer of Inter-University Sports Competition, Member of Student Advisory Committee, Dean's List for 3 consecutive semesters",
            
            "Cultural Secretary of Arts Society, Lead organizer of Charity Drive 2024, Student Representative in Academic Council, Winner of Leadership Excellence Award"
        ];
        
        $vp_names = [
            "Sarah Ahmed Khan",
            "Muhammad Ali Hassan", 
            "Fatima Noor Sheikh"
        ];
        
        $photo_urls = [
            "https://via.placeholder.com/300x300/7C3AED/FFFFFF?text=Candidate+1",
            "https://via.placeholder.com/300x300/059669/FFFFFF?text=Candidate+2",
            "https://via.placeholder.com/300x300/DC2626/FFFFFF?text=Candidate+3"
        ];
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO candidate_profiles (student_id, manifesto, photo_url, experience, vp_name, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending_patron')");
        
        for ($i = 0; $i < min(3, count($students)); $i++) {
            $stmt->execute([
                $students[$i]['id'],
                $manifestos[$i],
                $photo_urls[$i],
                $experiences[$i],
                $vp_names[$i]
            ]);
            echo "✅ Created candidate profile for {$students[$i]['name']} ({$students[$i]['reg_id']})<br>";
        }
    }
    
    // Create election settings for current term
    echo "<h3>Setting up Election Settings...</h3>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO election_settings (term_id, voting_enabled, voting_start_date, voting_end_date) 
                           VALUES (1, 0, DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY))");
    $stmt->execute();
    echo "✅ Election settings created for current term<br>";
    
    echo "<br>🎉 <strong>Elections & Analytics Setup Completed Successfully!</strong><br>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='candidate_setup.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Candidate Setup</a>";
echo "<a href='patron_dashboard.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Patron Dashboard</a>";
echo "<a href='hod_analytics.php' style='background: #dc2626; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>HOD Analytics</a>";
echo "</div>";
?>