<?php
// Fix event_items table by adding missing columns
require_once 'config/db.php';

try {
    echo "<h2>Fixing Event Items Table</h2>";
    
    // Add patron_comment column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE event_items ADD COLUMN patron_comment TEXT NULL");
        echo "✓ Added patron_comment column.<br>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✓ patron_comment column already exists.<br>";
        } else {
            throw $e;
        }
    }
    
    // Add is_approved_by_patron column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE event_items ADD COLUMN is_approved_by_patron TINYINT(1) DEFAULT 1");
        echo "✓ Added is_approved_by_patron column.<br>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "✓ is_approved_by_patron column already exists.<br>";
        } else {
            throw $e;
        }
    }
    
    // Update existing records to have default values
    $pdo->exec("UPDATE event_items SET is_approved_by_patron = 1 WHERE is_approved_by_patron IS NULL");
    echo "✓ Updated existing records with default values.<br>";
    
    echo "<br><strong>Table fixed successfully!</strong><br>";
    echo "<a href='patron_dashboard.php'>Go to Patron Dashboard</a> | <a href='patron_review_event.php?id=1'>Test Review Page</a>";
    
} catch(PDOException $e) {
    die("Error fixing table: " . $e->getMessage());
}
?>