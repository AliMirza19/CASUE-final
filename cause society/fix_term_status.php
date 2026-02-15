<?php
// Fix Term Status ENUM - Add 'completed' status
require_once 'config/db.php';

echo "<h2>Fixing Academic Terms Status ENUM</h2>";

try {
    // Update ENUM to include 'completed'
    $pdo->exec("ALTER TABLE academic_terms MODIFY COLUMN status ENUM('active', 'inactive', 'completed') DEFAULT 'inactive'");
    echo "✅ Status ENUM updated to include 'completed'<br>";
    
    // Verify
    $stmt = $pdo->query("DESCRIBE academic_terms");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'status') {
            echo "✅ Current status type: " . $col['Type'] . "<br>";
        }
    }
    
    echo "<br><strong>Done!</strong>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<br><br><a href='manage_terms.php'>Go to Manage Terms</a>";
?>