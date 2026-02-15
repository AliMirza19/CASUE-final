<?php
// Create missing President and Patron users
require_once 'config/db.php';

try {
    echo "<h2>Creating Missing Users</h2>";
    
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
        echo "✓ President user (PRES-001) created successfully!<br>";
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
        echo "✓ Patron user (PAT-001) created successfully!<br>";
    } else {
        echo "✓ Patron user already exists.<br>";
    }
    
    echo "<br><h3>Login Credentials:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Role</th><th>Registration ID</th><th>Password</th></tr>";
    echo "<tr><td>President</td><td><strong>PRES-001</strong></td><td>123456</td></tr>";
    echo "<tr><td>Patron</td><td><strong>PAT-001</strong></td><td>123456</td></tr>";
    echo "</table>";
    
    echo "<br><a href='check_users.php'>Check All Users</a> | <a href='index.php'>Go to Login Page</a>";
    
} catch(PDOException $e) {
    die("Error creating users: " . $e->getMessage());
}
?>