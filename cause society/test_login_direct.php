<?php
// Direct login test for GD and VC
session_start();
require_once 'config/db.php';

echo "<h2>Direct Login Test</h2>";

$test_users = [
    ['GD-001', '123456', 'Graphic Designer'],
    ['VC-001', '123456', 'Volunteer Coordinator']
];

foreach ($test_users as $test_user) {
    $reg_id = $test_user[0];
    $password = $test_user[1];
    $role_name = $test_user[2];
    
    echo "<h3>Testing {$role_name} ({$reg_id})</h3>";
    
    try {
        // Simulate login process
        $stmt = $pdo->prepare("SELECT id, reg_id, name, email, password, role, password_changed, current_term_id 
                               FROM users 
                               WHERE reg_id = :reg_id");
        $stmt->execute(['reg_id' => $reg_id]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            echo "✅ Login successful!<br>";
            echo "- User ID: {$user['id']}<br>";
            echo "- Name: {$user['name']}<br>";
            echo "- Role: {$user['role']}<br>";
            echo "- Password Changed: " . ($user['password_changed'] ? 'Yes' : 'No') . "<br>";
            echo "- Term ID: {$user['current_term_id']}<br>";
            
            // Check redirect logic
            switch ($user['role']) {
                case 'gd':
                    $redirect = 'gd_dashboard.php';
                    break;
                case 'vc':
                    $redirect = 'vc_dashboard.php';
                    break;
                default:
                    $redirect = 'index.php';
            }
            
            echo "- Should redirect to: <strong>{$redirect}</strong><br>";
            
            // Test if dashboard file exists
            if (file_exists($redirect)) {
                echo "✅ Dashboard file exists<br>";
            } else {
                echo "❌ Dashboard file missing!<br>";
            }
            
        } else {
            echo "❌ Login failed!<br>";
            if (!$user) {
                echo "- User not found<br>";
            } else {
                echo "- Password verification failed<br>";
            }
        }
        
    } catch(PDOException $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
}

// Test manual login form
echo "<h3>Manual Login Test</h3>";
echo "<div style='background: #f9fafb; border: 1px solid #d1d5db; padding: 20px; border-radius: 8px;'>";
echo "<form method='POST' action='auth/login_process.php'>";
echo "<p><strong>Test GD Login:</strong></p>";
echo "<input type='hidden' name='reg_id' value='GD-001'>";
echo "<input type='hidden' name='password' value='123456'>";
echo "<button type='submit' style='background: #7C3AED; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Login as GD-001</button>";
echo "</form>";
echo "<br>";
echo "<form method='POST' action='auth/login_process.php'>";
echo "<p><strong>Test VC Login:</strong></p>";
echo "<input type='hidden' name='reg_id' value='VC-001'>";
echo "<input type='hidden' name='password' value='123456'>";
echo "<button type='submit' style='background: #059669; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Login as VC-001</button>";
echo "</form>";
echo "</div>";

echo "<br><p><a href='index.php' style='background: #6b7280; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Normal Login Page</a></p>";
?>