<?php
// Toggle Term Status - Activate/Deactivate/Complete terms
session_start();
require_once 'config/db.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied!";
    header("Location: index.php");
    exit();
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_terms.php");
    exit();
}

$term_id = isset($_POST['term_id']) ? (int)$_POST['term_id'] : 0;
$action = $_POST['action'] ?? '';

if (!$term_id || !in_array($action, ['activate', 'deactivate', 'complete'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_terms.php");
    exit();
}

try {
    // Get term info
    $stmt = $pdo->prepare("SELECT * FROM academic_terms WHERE id = ?");
    $stmt->execute([$term_id]);
    $term = $stmt->fetch();
    
    if (!$term) {
        throw new Exception("Term not found!");
    }
    
    switch ($action) {
        case 'activate':
            // Deactivate all other terms first
            $pdo->exec("UPDATE academic_terms SET status = 'inactive' WHERE status = 'active'");
            
            // Activate selected term
            $stmt = $pdo->prepare("UPDATE academic_terms SET status = 'active' WHERE id = ?");
            $stmt->execute([$term_id]);
            
            // Update all users' current_term_id
            $stmt = $pdo->prepare("UPDATE users SET current_term_id = ?");
            $stmt->execute([$term_id]);
            
            // Log activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text) VALUES (?, 'admin', ?)");
            $log_stmt->execute([$_SESSION['user_id'], "Activated academic term: {$term['term_name']}"]);
            
            $_SESSION['success'] = "Term '{$term['term_name']}' successfully activated!";
            break;
            
        case 'deactivate':
            $stmt = $pdo->prepare("UPDATE academic_terms SET status = 'inactive' WHERE id = ?");
            $stmt->execute([$term_id]);
            
            // Log activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text) VALUES (?, 'admin', ?)");
            $log_stmt->execute([$_SESSION['user_id'], "Deactivated academic term: {$term['term_name']}"]);
            
            $_SESSION['success'] = "Term '{$term['term_name']}' deactivated!";
            break;
            
        case 'complete':
            $stmt = $pdo->prepare("UPDATE academic_terms SET status = 'completed' WHERE id = ?");
            $stmt->execute([$term_id]);
            
            // Log activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text) VALUES (?, 'admin', ?)");
            $log_stmt->execute([$_SESSION['user_id'], "Marked academic term as completed: {$term['term_name']}"]);
            
            $_SESSION['success'] = "Term '{$term['term_name']}' marked as completed!";
            break;
    }
    
} catch(Exception $e) {
    $_SESSION['error'] = $e->getMessage();
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: manage_terms.php");
exit();
?>