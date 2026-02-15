<?php
// Admin password reset script - Run this if you need to reset admin password
require_once 'config/db.php';

try {
    // Delete existing admin
    $pdo->exec("DELETE FROM users WHERE reg_id = 'ADMIN-001'");
    
    // Create new admin with fresh password
    $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                VALUES (:reg_id, :name, :email, :password, :role, :password_changed, :term_id)");
    $stmt->execute([
        'reg_id' => 'ADMIN-001',
        'name' => 'System Administrator',
        'email' => 'admin@cause.edu.pk',
        'password' => $hashedPassword,
        'role' => 'admin',
        'password_changed' => 0,
        'term_id' => 1
    ]);
    
    echo "<h2>Admin Reset Successful!</h2>";
    echo "<p>Login Credentials:</p>";
    echo "<ul>";
    echo "<li><strong>Registration ID:</strong> ADMIN-001</li>";
    echo "<li><strong>Password:</strong> 123456</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>Go to Login Page</a></p>";
    
} catch(PDOException $e) {
    die("Reset failed: " . $e->getMessage());
}
?>
