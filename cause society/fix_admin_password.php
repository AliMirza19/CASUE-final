<?php
// Fix Admin Password - Quick Fix
require_once 'config/db.php';

echo "<h2>🔧 Fixing Admin Password</h2>";

try {
    // Update admin password to proper hash for 123456
    $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'admin'");
    $stmt->execute([$hashedPassword]);
    
    echo "✅ Admin password updated successfully<br>";
    
    // Test the password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin && password_verify('123456', $admin['password'])) {
        echo "✅ Password verification test passed<br>";
        echo "<strong>Admin login: ADMIN-001 / 123456</strong><br>";
    } else {
        echo "❌ Password verification test failed<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='index.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Test Login</a>";
?>