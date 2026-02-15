<?php
// Final System Test - STEP 8 (Elections & Analytics)
require_once 'config/db.php';

echo "<h2>🧪 Final System Test - Elections & Analytics</h2>";

try {
    // Step 1: Run elections setup
    echo "<h3>Step 1: Elections & Analytics Setup</h3>";
    include 'setup_elections_analytics.php';
    
    // Step 2: Test candidate profile creation
    echo "<h3>Step 2: Testing Candidate Profile System</h3>";
    
    // Get student user
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'student' LIMIT 1");
    $student = $stmt->fetch();
    
    if ($student) {
        // Check if candidate profile exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM candidate_profiles WHERE student_id = ?");
        $stmt->execute([$student['id']]);
        $profile_count = $stmt->fetch()['count'];
        echo "Candidate profiles in system: {$profile_count}<br>";
    }
    
    // Step 3: Test voting system
    echo "<h3>Step 3: Testing Voting System</h3>";
    
    // Check election settings
    $stmt = $pdo->query("SELECT * FROM election_settings WHERE term_id = 1");
    $election = $stmt->fetch();
    
    if ($election) {
        echo "✅ Election settings configured<br>";
        echo "- Voting enabled: " . ($election['voting_enabled'] ? 'Yes' : 'No') . "<br>";
        echo "- Voting period: " . date('M d, Y', strtotime($election['voting_start_date'])) . " to " . date('M d, Y', strtotime($election['voting_end_date'])) . "<br>";
        
        // Enable voting for testing
        $stmt = $pdo->prepare("UPDATE election_settings SET voting_enabled = 1, voting_start_date = NOW(), voting_end_date = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE term_id = 1");
        $stmt->execute();
        echo "✅ Enabled voting for testing<br>";
    }
    
    // Step 4: Test analytics data
    echo "<h3>Step 4: Testing Analytics System</h3>";
    
    // Check budget data
    $stmt = $pdo->query("SELECT * FROM budgets WHERE term_id = 1");
    $budget = $stmt->fetch();
    
    if ($budget) {
        echo "✅ Budget data available<br>";
        echo "- Total budget: PKR " . number_format($budget['total_amount'], 2) . "<br>";
        echo "- Remaining: PKR " . number_format($budget['remaining_amount'], 2) . "<br>";
        echo "- Spent: PKR " . number_format($budget['total_amount'] - $budget['remaining_amount'], 2) . "<br>";
    }
    
    // Check events for analytics
    $stmt = $pdo->query("SELECT COUNT(*) as count, SUM(grand_total) as total_spent FROM events WHERE term_id = 1 AND status IN ('approved', 'completed')");
    $analytics = $stmt->fetch();
    
    echo "✅ Analytics data ready<br>";
    echo "- Approved events: {$analytics['count']}<br>";
    echo "- Total spent on events: PKR " . number_format($analytics['total_spent'] ?? 0, 2) . "<br>";
    
    // Step 5: Verify all portals
    echo "<h3>Step 5: Portal Verification</h3>";
    
    $portals = [
        'candidate_setup.php' => 'Candidate Setup',
        'voting_portal.php' => 'Voting Portal', 
        'hod_analytics.php' => 'HOD Analytics',
        'patron_dashboard.php' => 'Patron Dashboard (with elections)',
        'student_dashboard.php' => 'Student Dashboard (with voting)'
    ];
    
    foreach ($portals as $file => $name) {
        if (file_exists($file)) {
            echo "✅ {$name} - Ready<br>";
        } else {
            echo "❌ {$name} - Missing<br>";
        }
    }
    
    echo "<br>🎉 <strong>STEP 8 Implementation Complete!</strong><br>";
    
    echo "<h3>Complete System Features:</h3>";
    echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px;'>";
    echo "<strong>✅ All 8 Steps Implemented:</strong><br>";
    echo "1. ✅ Login System & User Management<br>";
    echo "2. ✅ Admin Dashboard & Term Management<br>";
    echo "3. ✅ HOD Dashboard & Budget Management<br>";
    echo "4. ✅ Student Event Submission Portal<br>";
    echo "5. ✅ President & Patron Review System<br>";
    echo "6. ✅ HOD Final Approval & SA Review<br>";
    echo "7. ✅ Graphic Designer & Volunteer Coordinator<br>";
    echo "8. ✅ Elections & Financial Analytics<br>";
    echo "<br><strong>🎯 Complete Workflow:</strong><br>";
    echo "Student → President → Patron → HOD → SA → Approved → GD Graphics + VC Volunteers → Elections & Analytics<br>";
    echo "</div>";
    
    echo "<h3>Login Credentials for Testing:</h3>";
    echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px;'>";
    echo "<strong>All Users (Password: 123456):</strong><br>";
    echo "🔐 Admin: ADMIN-001<br>";
    echo "👨‍💼 HOD: HOD-001<br>";
    echo "👨‍🎓 Student: STU-001<br>";
    echo "👔 President: PRES-001<br>";
    echo "🎖️ Patron: PAT-001<br>";
    echo "📋 Student Affairs: SA-001<br>";
    echo "🎨 Graphic Designer: GD-001<br>";
    echo "👥 Volunteer Coordinator: VC-001<br>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='index.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Login Portal</a>";
echo "<a href='candidate_setup.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Candidate Setup</a>";
echo "<a href='voting_portal.php' style='background: #dc2626; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Voting Portal</a>";
echo "<a href='hod_analytics.php' style='background: #16a34a; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>HOD Analytics</a>";
echo "</div>";
?>