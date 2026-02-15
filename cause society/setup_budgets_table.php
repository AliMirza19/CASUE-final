<?php
// Budgets table create karne ka script
require_once 'config/db.php';

try {
    // Budgets table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS budgets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        term_id INT NOT NULL,
        total_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
        remaining_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
        is_locked TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (term_id) REFERENCES academic_terms(id) ON DELETE CASCADE,
        UNIQUE KEY unique_term_budget (term_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "✓ Table 'budgets' successfully created!<br>";
    echo "<a href='hod_dashboard.php'>Go to HOD Dashboard</a>";
    
} catch(PDOException $e) {
    die("Error creating budgets table: " . $e->getMessage());
}
?>
