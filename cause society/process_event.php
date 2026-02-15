<?php
// Process Event Submission - Form data save karo
session_start();
require_once 'config/db.php';

// Check karo ke user logged in hai aur student hai
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Check karo ke POST request hai
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: request_event.php");
    exit();
}

// Budget lock check karo
try {
    $stmt = $pdo->prepare("SELECT is_locked FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $budget = $stmt->fetch();
    if (!$budget || $budget['is_locked'] != 1) {
        $_SESSION['error'] = "System is currently inactive!";
        header("Location: student_dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "System error occurred!";
    header("Location: student_dashboard.php");
    exit();
}

// Form data sanitize karo
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$expected_date = $_POST['expected_date'] ?? '';
$venue = trim($_POST['venue'] ?? '');
$grand_total = floatval($_POST['grand_total'] ?? 0);
$items = $_POST['items'] ?? [];

// Team members
$team_member_1 = trim($_POST['team_member_1'] ?? '');
$team_member_2 = trim($_POST['team_member_2'] ?? '');
$team_member_3 = trim($_POST['team_member_3'] ?? '');

// Validation
if (empty($title) || empty($description) || empty($expected_date) || empty($venue)) {
    $_SESSION['error'] = "All required fields must be filled!";
    header("Location: request_event.php");
    exit();
}

if (empty($items)) {
    $_SESSION['error'] = "At least one budget item is required!";
    header("Location: request_event.php");
    exit();
}

// Items validate karo
foreach ($items as $item) {
    if (empty($item['name']) || empty($item['quantity']) || !isset($item['rate'])) {
        $_SESSION['error'] = "All budget items must have name, quantity and rate!";
        header("Location: request_event.php");
        exit();
    }
}

try {
    // Transaction start karo
    $pdo->beginTransaction();
    
    // Event insert karo
    $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, team_member_1, team_member_2, team_member_3, status, created_at) 
                           VALUES (:title, :description, :student_id, :term_id, :expected_date, :venue, :grand_total, :tm1, :tm2, :tm3, 'pending_president', NOW())");
    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'student_id' => $_SESSION['user_id'],
        'term_id' => $_SESSION['term_id'],
        'expected_date' => $expected_date,
        'venue' => $venue,
        'grand_total' => $grand_total,
        'tm1' => $team_member_1 ?: null,
        'tm2' => $team_member_2 ?: null,
        'tm3' => $team_member_3 ?: null
    ]);
    
    // Event ID get karo
    $event_id = $pdo->lastInsertId();
    
    // Budget items insert karo
    $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount) 
                           VALUES (:event_id, :item_name, :quantity, :unit_rate, :total_amount)");
    
    foreach ($items as $item) {
        $quantity = intval($item['quantity']);
        $rate = floatval($item['rate']);
        $total = $quantity * $rate;
        
        $stmt->execute([
            'event_id' => $event_id,
            'item_name' => trim($item['name']),
            'quantity' => $quantity,
            'unit_rate' => $rate,
            'total_amount' => $total
        ]);
    }
    
    // Transaction commit karo
    $pdo->commit();
    
    $_SESSION['success'] = "Event request submitted successfully! It is now pending President approval.";
    header("Location: my_events.php");
    exit();
    
} catch(PDOException $e) {
    // Rollback on error
    $pdo->rollBack();
    $_SESSION['error'] = "Error submitting event: " . $e->getMessage();
    error_log("Event Submission Error: " . $e->getMessage());
    header("Location: request_event.php");
    exit();
}
?>
