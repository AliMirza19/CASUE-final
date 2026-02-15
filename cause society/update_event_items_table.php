<?php
// Update event_items table to add patron columns
require_once 'config/db.php';

try {
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM event_items LIKE 'patron_comment'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE event_items ADD COLUMN patron_comment TEXT NULL");
        echo "✓ Added patron_comment column.<br>";
    } else {
        echo "✓ patron_comment column already exists.<br>";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM event_items LIKE 'is_approved_by_patron'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE event_items ADD COLUMN is_approved_by_patron TINYINT(1) DEFAULT 1");
        echo "✓ Added is_approved_by_patron column.<br>";
    } else {
        echo "✓ is_approved_by_patron column already exists.<br>";
    }
    
    echo "<br><strong>Event items table updated successfully!</strong><br>";
    echo "<a href='patron_dashboard.php'>Go to Patron Dashboard</a>";
    
} catch(PDOException $e) {
    die("Error updating table: " . $e->getMessage());
}
?>