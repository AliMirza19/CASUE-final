<?php
// Check if users exist in database
require_once 'config/db.php';

try {
    echo "<h2>User Database Check</h2>";
    
    $users_to_check = [
        'ADMIN-001' => 'Admin',
        'HOD-001' => 'HOD', 
        'PRES-001' => 'President',
        'PAT-001' => 'Patron',
        'STU-001' => 'Student'
    ];
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Role</th><th>Registration ID</th><th>Name</th><th>Status</th><th>Password Hash</th></tr>";
    
    foreach ($users_to_check as $reg_id => $role) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reg_id = :reg_id");
        $stmt->execute(['reg_id' => $reg_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<tr>";
            echo "<td>{$role}</td>";
            echo "<td><strong>{$reg_id}</strong></td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td style='color: green;'>✓ EXISTS</td>";
            echo "<td>" . substr($user['password'], 0, 20) . "...</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td>{$role}</td>";
            echo "<td><strong>{$reg_id}</strong></td>";
            echo "<td>-</td>";
            echo "<td style='color: red;'>✗ NOT FOUND</td>";
            echo "<td>-</td>";
            echo "</tr>";
        }
    }
    
    echo "</table>";
    
    echo "<br><h3>Password Test</h3>";
    echo "Testing password '123456' against stored hashes:<br><br>";
    
    foreach ($users_to_check as $reg_id => $role) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE reg_id = :reg_id");
        $stmt->execute(['reg_id' => $reg_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $password_valid = password_verify('123456', $user['password']);
            echo "<strong>{$reg_id}</strong>: " . ($password_valid ? "✓ Password Valid" : "✗ Password Invalid") . "<br>";
        }
    }
    
    echo "<br><a href='index.php'>Go to Login Page</a> | <a href='setup.php'>Run Setup Again</a>";
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>