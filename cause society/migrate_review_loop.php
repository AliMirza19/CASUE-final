<?php
// Database Migration - Add President Review Loop
// This script updates the events table for the new workflow
require_once 'config/db.php';

echo "<h2>Database Migration - President Review Loop</h2>";

try {
    // Step 1: Add president_comments column if not exists
    echo "<h3>Step 1: Adding president_comments column...</h3>";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM events LIKE 'president_comments'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE events ADD COLUMN president_comments TEXT AFTER rejection_reason");
        echo "✅ president_comments column added<br>";
    } else {
        echo "✅ president_comments column already exists<br>";
    }
    
    // Step 2: Update ENUM to include new statuses
    echo "<h3>Step 2: Updating status ENUM values...</h3>";
    
    $pdo->exec("ALTER TABLE events MODIFY COLUMN status 
                ENUM('pending_president', 'revision_needed', 'president_approved', 'pending_patron', 
                     'pending_hod', 'pending_sa', 'approved', 'rejected', 'completed') 
                DEFAULT 'pending_president'");
    echo "✅ Status ENUM updated with new values:<br>";
    echo "   - pending_president (initial submission)<br>";
    echo "   - revision_needed (president requests changes)<br>";
    echo "   - president_approved (president marks as OK)<br>";
    echo "   - pending_patron (forwarded to patron)<br>";
    echo "   - pending_hod, pending_sa, approved, rejected, completed<br>";
    
    // Step 3: Verify the changes
    echo "<h3>Step 3: Verifying changes...</h3>";
    
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        $highlight = in_array($col['Field'], ['status', 'president_comments']) ? 'background: #d4edda;' : '';
        echo "<tr style='{$highlight}'>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><div style='background: #10b981; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: white; margin: 0;'>✅ Migration Complete!</h3>";
    echo "<p>Database is now ready for the President Review Loop workflow.</p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #ef4444; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: white; margin: 0;'>❌ Migration Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<br><a href='president_dashboard.php' style='background: #7C3AED; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to President Dashboard</a>";
?>
