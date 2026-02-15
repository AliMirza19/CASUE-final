<?php
require_once 'config/db.php';

$stmt = $pdo->prepare("SELECT * FROM budgets WHERE term_id = 1");
$stmt->execute();
$budget = $stmt->fetch();

if ($budget) {
    echo "Budget exists:\n";
    echo "- Total: PKR " . number_format($budget['total_amount'], 2) . "\n";
    echo "- Remaining: PKR " . number_format($budget['remaining_amount'], 2) . "\n";
    echo "- Locked: " . ($budget['is_locked'] ? 'Yes' : 'No') . "\n";
} else {
    echo "No budget found for term 1\n";
}
?>