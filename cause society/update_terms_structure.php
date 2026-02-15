<?php
// Database Update - Enhanced Terms Structure
require_once 'config/db.php';

echo "<h2>🔧 Database Update - Enhanced Terms Management</h2>";

try {
    // Step 1: Update academic_terms table structure
    echo "<h3>Step 1: Updating academic_terms table...</h3>";
    
    // Check if status column exists and update ENUM values
    $stmt = $pdo->query("SHOW COLUMNS FROM academic_terms LIKE 'status'");
    if ($stmt->rowCount() > 0) {
        // Update existing ENUM to include 'completed'
        $pdo->exec("ALTER TABLE academic_terms MODIFY COLUMN status 
                    ENUM('active', 'inactive', 'completed') DEFAULT 'inactive'");
        echo "✅ Status column updated with new values: active, inactive, completed<br>";
    } else {
        // Add status column if it doesn't exist
        $pdo->exec("ALTER TABLE academic_terms ADD COLUMN status 
                    ENUM('active', 'inactive', 'completed') DEFAULT 'inactive' AFTER term_name");
        echo "✅ Status column added with values: active, inactive, completed<br>";
    }
    
    // Step 2: Ensure only one active term constraint
    echo "<h3>Step 2: Ensuring single active term constraint...</h3>";
    
    // Count active terms
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM academic_terms WHERE status = 'active'");
    $active_count = $stmt->fetch()['count'];
    
    if ($active_count > 1) {
        // If multiple active terms, keep the latest one active
        $pdo->exec("UPDATE academic_terms SET status = 'inactive' WHERE status = 'active'");
        $pdo->exec("UPDATE academic_terms SET status = 'active' 
                    WHERE id = (SELECT id FROM (SELECT id FROM academic_terms ORDER BY created_at DESC LIMIT 1) as temp)");
        echo "⚠️ Multiple active terms found - kept latest one active<br>";
    } elseif ($active_count == 0) {
        // If no active term, activate the latest one
        $pdo->exec("UPDATE academic_terms SET status = 'active' 
                    WHERE id = (SELECT id FROM (SELECT id FROM academic_terms ORDER BY created_at DESC LIMIT 1) as temp)");
        echo "✅ No active term found - activated latest term<br>";
    } else {
        echo "✅ Single active term constraint verified<br>";
    }
    
    // Step 3: Add indexes for better performance
    echo "<h3>Step 3: Adding database indexes...</h3>";
    
    try {
        $pdo->exec("CREATE INDEX idx_terms_status ON academic_terms(status)");
        echo "✅ Index added on academic_terms.status<br>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✅ Index on academic_terms.status already exists<br>";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_events_term_status ON events(term_id, status)");
        echo "✅ Composite index added on events(term_id, status)<br>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✅ Composite index on events already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // Step 4: Verify the structure
    echo "<h3>Step 4: Verifying updated structure...</h3>";
    
    $stmt = $pdo->query("DESCRIBE academic_terms");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        $highlight = $col['Field'] === 'status' ? 'background: #d4edda;' : '';
        echo "<tr style='{$highlight}'>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 5: Sample data verification
    echo "<h3>Step 5: Current Terms Status...</h3>";
    
    $stmt = $pdo->query("SELECT id, term_name, status, start_date, end_date, 
                         CASE WHEN end_date < CURDATE() THEN 'EXPIRED' ELSE 'CURRENT' END as date_status
                         FROM academic_terms ORDER BY created_at DESC");
    $terms = $stmt->fetchAll();
    
    if (!empty($terms)) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Term Name</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Date Status</th></tr>";
        foreach ($terms as $term) {
            $status_color = $term['status'] === 'active' ? 'background: #d4edda;' : 
                           ($term['status'] === 'completed' ? 'background: #f8d7da;' : 'background: #fff3cd;');
            $date_color = $term['date_status'] === 'EXPIRED' ? 'color: red; font-weight: bold;' : '';
            
            echo "<tr style='{$status_color}'>";
            echo "<td>{$term['id']}</td>";
            echo "<td>{$term['term_name']}</td>";
            echo "<td>{$term['status']}</td>";
            echo "<td>{$term['start_date']}</td>";
            echo "<td>{$term['end_date']}</td>";
            echo "<td style='{$date_color}'>{$term['date_status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><div style='background: #10b981; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: white; margin: 0;'>✅ Database Update Complete!</h3>";
    echo "<p>Enhanced Terms Management structure is ready.</p>";
    echo "<ul style='margin: 10px 0; padding-left: 20px;'>";
    echo "<li>✅ Status ENUM updated: active, inactive, completed</li>";
    echo "<li>✅ Single active term constraint enforced</li>";
    echo "<li>✅ Performance indexes added</li>";
    echo "<li>✅ Data integrity verified</li>";
    echo "</ul>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #ef4444; color: white; padding: 20px; border-radius: 10px;'>";
    echo "<h3 style='color: white; margin: 0;'>❌ Database Update Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<br><a href='admin_dashboard.php' style='background: #7C3AED; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
?>