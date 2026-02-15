<?php
// Login processing logic
session_start();
require_once '../config/db.php';

// Check karo ke form POST method se submit hua hai
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

// Form data ko sanitize aur validate karo
$reg_id = trim($_POST['reg_id'] ?? '');
$password = $_POST['password'] ?? '';

// Validation checks
if (empty($reg_id) || empty($password)) {
    $_SESSION['error'] = "Both Registration ID and Password are required!";
    header("Location: ../index.php");
    exit();
}

// Registration ID length check
if (strlen($reg_id) < 6 || strlen($reg_id) > 12) {
    $_SESSION['error'] = "Registration ID must be between 6 to 12 characters!";
    header("Location: ../index.php");
    exit();
}

// Password length check
if (strlen($password) < 6 || strlen($password) > 30) {
    $_SESSION['error'] = "Password must be between 6 to 30 characters!";
    header("Location: ../index.php");
    exit();
}

try {
    // Database se user ko find karo
    $stmt = $pdo->prepare("SELECT id, reg_id, name, email, password, role, password_changed, current_term_id 
                           FROM users 
                           WHERE reg_id = :reg_id");
    $stmt->execute(['reg_id' => $reg_id]);
    $user = $stmt->fetch();
    
    // Check karo ke user exist karta hai aur password sahi hai
    if ($user && password_verify($password, $user['password'])) {
        // Session mein user details store karo
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['reg_id'] = $user['reg_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['term_id'] = $user['current_term_id'];
        $_SESSION['password_changed'] = $user['password_changed'];
        
        // Agar password change nahi hua to change password page par redirect karo
        if ($user['password_changed'] == 0) {
            header("Location: change_password.php");
            exit();
        }
        
        // Role ke basis par dashboard set karo aur redirect karo
        switch ($user['role']) {
            case 'admin':
                $_SESSION['dashboard'] = 'admin_dashboard.php';
                header("Location: ../admin_dashboard.php");
                break;
            case 'student':
                $_SESSION['dashboard'] = 'student_dashboard.php';
                header("Location: ../student_dashboard.php");
                break;
            case 'hod':
                $_SESSION['dashboard'] = 'hod_dashboard.php';
                header("Location: ../hod_dashboard.php");
                break;
            case 'patron':
                $_SESSION['dashboard'] = 'patron_dashboard.php';
                header("Location: ../patron_dashboard.php");
                break;
            case 'president':
                $_SESSION['dashboard'] = 'president_dashboard.php';
                header("Location: ../president_dashboard.php");
                break;
            case 'sa':
                $_SESSION['dashboard'] = 'sa_dashboard.php';
                header("Location: ../sa_dashboard.php");
                break;
            case 'vc':
                $_SESSION['dashboard'] = 'vc_dashboard.php';
                header("Location: ../vc_dashboard.php");
                break;
            case 'gd':
                $_SESSION['dashboard'] = 'gd_dashboard.php';
                header("Location: ../gd_dashboard.php");
                break;
            default:
                $_SESSION['error'] = "Invalid user role!";
                header("Location: ../index.php");
                exit();
        }
        
    } else {
        // Login failed - galat credentials
        $_SESSION['error'] = "Invalid Registration ID or Password!";
        header("Location: ../index.php");
        exit();
    }
    
} catch(PDOException $e) {
    // Database error handle karo
    $_SESSION['error'] = "A technical error occurred. Please try again!";
    error_log("Login Error: " . $e->getMessage());
    header("Location: ../index.php");
    exit();
}
?>
