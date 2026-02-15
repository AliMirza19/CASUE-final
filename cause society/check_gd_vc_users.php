<?php
// Check GD and VC users
require_once 'config/db.php';

echo "<h2>Checking GD & VC Users</h2>";

try {
    // Check all users with GD and VC roles
    $stmt = $pdo->query("SELECT reg_id, name, email, role, password_changed, current_term_id FROM users WHERE role IN ('gd', 'vc') ORDER BY role");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "❌ No GD or VC users found!<br>";
    } else {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr style='background: #f3f4f6;'>";
        echo "<th>Registration ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password Changed</th><th>Term ID</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td><strong>{$user['reg_id']}</strong></td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>" . strtoupper($user['role']) . "</td>";
            echo "<td>" . ($user['password_changed'] ? 'Yes' : 'No') . "</td>";
            echo "<td>{$user['current_term_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test login credentials
    echo "<h3>Testing Login Credentials</h3>";
    
    $test_credentials = [
        ['GD-001', '123456'],
        ['VC-001', '123456']
    ];
    
    foreach ($test_credentials as $cred) {
        $reg_id = $cred[0];
        $password = $cred[1];
        
        echo "<h4>Testing {$reg_id}:</h4>";
        
        $stmt = $pdo->prepare("SELECT reg_id, name, password, role, password_changed FROM users WHERE reg_id = ?");
        $stmt->execute([$reg_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ User found: {$user['name']} ({$user['role']})<br>";
            
            if (password_verify($password, $user['password'])) {
                echo "✅ Password verification: SUCCESS<br>";
                echo "✅ Password changed status: " . ($user['password_changed'] ? 'Yes' : 'No') . "<br>";
            } else {
                echo "❌ Password verification: FAILED<br>";
                echo "Stored hash: " . substr($user['password'], 0, 20) . "...<br>";
                
                // Try to fix password
                echo "Fixing password...<br>";
                $new_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, password_changed = 1 WHERE reg_id = ?");
                $stmt->execute([$new_hash, $reg_id]);
                echo "✅ Password updated<br>";
            }
        } else {
            echo "❌ User not found!<br>";
        }
        echo "<br>";
    }
    
    echo "<h3>Login Test Links</h3>";
    echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px;'>";
    echo "<p><strong>Try logging in with these credentials:</strong></p>";
    echo "<p>🎨 <strong>Graphic Designer:</strong> GD-001 / 123456</p>";
    echo "<p>👥 <strong>Volunteer Coordinator:</strong> VC-001 / 123456</p>";
    echo "<br>";
    echo "<a href='index.php' style='background: #7C3AED; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Go to Login Page</a>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
}
?>