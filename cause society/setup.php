<?php
// Database setup script - Ye file ek baar run karo database setup ke liye

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Pehle bina database ke connect karo
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Database create karo
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cause_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'cause_db' successfully created ya already exist karta hai.<br>";
    
    // Ab database select karo
    $pdo->exec("USE cause_db");
    
    // Academic Terms table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS academic_terms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        term_name VARCHAR(100) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'inactive',
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table 'academic_terms' successfully created.<br>";
    
    // Users table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reg_id VARCHAR(50) UNIQUE NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd') NOT NULL,
        password_changed TINYINT(1) DEFAULT 0,
        current_term_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (current_term_id) REFERENCES academic_terms(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table 'users' successfully created.<br>";
    
    // Budgets table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS budgets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        term_id INT NOT NULL,
        total_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
        remaining_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
        is_locked TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
        UNIQUE KEY unique_term_budget (term_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table 'budgets' successfully created.<br>";
    
    // Events table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        student_id INT NOT NULL,
        term_id INT NOT NULL,
        expected_date DATE NOT NULL,
        venue VARCHAR(255) NOT NULL,
        grand_total DECIMAL(15, 2) NOT NULL DEFAULT 0,
        team_member_1 VARCHAR(50) NULL,
        team_member_2 VARCHAR(50) NULL,
        team_member_3 VARCHAR(50) NULL,
        status ENUM('pending_president', 'pending_patron', 'pending_hod', 'approved', 'rejected', 'completed') DEFAULT 'pending_president',
        rejection_reason TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table 'events' successfully created.<br>";
    
    // Event Items table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        item_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        unit_rate DECIMAL(15, 2) NOT NULL DEFAULT 0,
        total_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
        patron_comment TEXT NULL,
        is_approved_by_patron TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Table 'event_items' successfully created.<br>";
    
    // Check karo ke term already exist karta hai
    $stmt = $pdo->query("SELECT COUNT(*) FROM academic_terms WHERE term_name = 'Fall 2024'");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO academic_terms (term_name, status, start_date, end_date) 
                    VALUES ('Fall 2024', 'active', '2024-09-01', '2024-12-31')");
        echo "✓ Default term 'Fall 2024' successfully inserted.<br>";
    } else {
        echo "✓ Default term 'Fall 2024' already exists.<br>";
    }
    
    // Check karo ke admin user already exist karta hai
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'ADMIN-001'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'ADMIN-001',
            'name' => 'System Administrator',
            'email' => 'admin@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'admin',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✓ Admin user created.<br>";
    } else {
        echo "✓ Admin user already exists.<br>";
    }
    
    // HOD user create karo
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'HOD-001'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'HOD-001',
            'name' => 'Dr. Ahmed Khan',
            'email' => 'hod@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'hod',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✓ HOD user created.<br>";
    } else {
        echo "✓ HOD user already exists.<br>";
    }
    
    // Student user create karo
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'STU-001'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'STU-001',
            'name' => 'Ali Hassan',
            'email' => 'student@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'student',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✓ Student user created.<br>";
    } else {
        echo "✓ Student user already exists.<br>";
    }
    
    // President user create karo
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'PRES-001'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'PRES-001',
            'name' => 'Sarah Ahmed',
            'email' => 'president@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'president',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✓ President user created.<br>";
    } else {
        echo "✓ President user already exists.<br>";
    }
    
    // Patron user create karo
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'PAT-001'");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                    VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
        $stmt->execute([
            'reg_id' => 'PAT-001',
            'name' => 'Prof. Muhammad Khan',
            'email' => 'patron@cause.edu.pk',
            'password' => $hashedPassword,
            'role' => 'patron',
            'password_changed' => 1,
            'term_id' => 1
        ]);
        echo "✓ Patron user created.<br>";
    } else {
        echo "✓ Patron user already exists.<br>";
    }
    
    // Student Affairs user create karo
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE reg_id = 'SA-001'");
    if ($stmt->fetchColumn() == 0) {
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
        echo "✓ Student Affairs user created.<br>";
    } else {
        echo "✓ Student Affairs user already exists.<br>";
    }
    
    echo "<br><strong>Setup complete!</strong><br><br>";
    echo "<h3>Login Credentials:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Role</th><th>Registration ID</th><th>Password</th></tr>";
    echo "<tr><td>Admin</td><td><strong>ADMIN-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>HOD</td><td><strong>HOD-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>President</td><td><strong>PRES-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>Patron</td><td><strong>PAT-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>Student Affairs</td><td><strong>SA-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>Student</td><td><strong>STU-001</strong></td><td>123456</td></tr>";
    echo "</table>";
    echo "<br><a href='index.php'>Go to Login Page</a>";
    
} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
