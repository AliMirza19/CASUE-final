<?php
// Password change processing
session_start();

// Check karo ke user logged in hai
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: change_password.php");
    exit();
}

// Form data get karo
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: change_password.php");
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "New password and confirm password do not match!";
    header("Location: change_password.php");
    exit();
}

if (strlen($new_password) < 6 || strlen($new_password) > 30) {
    $_SESSION['error'] = "Password must be between 6 to 30 characters!";
    header("Location: change_password.php");
    exit();
}

try {
    // Current password verify karo
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Current password is incorrect!";
        header("Location: change_password.php");
        exit();
    }
    
    // New password hash karo aur update karo
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password = :password, password_changed = 1 WHERE id = :id");
    $stmt->execute([
        'password' => $hashed_password,
        'id' => $_SESSION['user_id']
    ]);
    
    // Session update karo
    $_SESSION['password_changed'] = 1;
    $_SESSION['success'] = "Password changed successfully!";
    
    // Dashboard par redirect karo
    header("Location: ../" . $_SESSION['dashboard']);
    exit();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "An error occurred while changing password!";
    error_log("Password Change Error: " . $e->getMessage());
    header("Location: change_password.php");
    exit();
}
?>
