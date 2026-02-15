<?php
// UI Consistency Check - Final Polish
require_once 'config/db.php';

echo "<h2>🎨 UI Consistency Check - Purple Theme Verification</h2>";
echo "<p>Tamam dashboards ka UI consistency check kar rahe hain...</p>";

// Critical files to check for UI consistency
$dashboard_files = [
    'admin_dashboard.php' => 'Admin Dashboard',
    'hod_dashboard.php' => 'HOD Dashboard', 
    'student_dashboard.php' => 'Student Dashboard',
    'patron_dashboard.php' => 'Patron Dashboard',
    'president_dashboard.php' => 'President Dashboard',
    'sa_dashboard.php' => 'Student Affairs Dashboard',
    'gd_dashboard.php' => 'Graphic Designer Dashboard',
    'vc_dashboard.php' => 'Volunteer Coordinator Dashboard'
];

$header_files = [
    'includes/admin_header.php' => 'Admin Header',
    'includes/hod_header.php' => 'HOD Header',
    'includes/student_header.php' => 'Student Header',
    'includes/patron_header.php' => 'Patron Header',
    'includes/president_header.php' => 'President Header',
    'includes/sa_header.php' => 'SA Header',
    'includes/gd_header.php' => 'GD Header',
    'includes/vc_header.php' => 'VC Header'
];

echo "<h3>Step 1: Dashboard Files Check</h3>";

$missing_files = [];
$existing_files = [];

foreach ($dashboard_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ {$description} - File exists<br>";
        $existing_files[] = $file;
    } else {
        echo "❌ {$description} - Missing file: {$file}<br>";
        $missing_files[] = $file;
    }
}

echo "<h3>Step 2: Header Files Check</h3>";

foreach ($header_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ {$description} - File exists<br>";
    } else {
        echo "❌ {$description} - Missing file: {$file}<br>";
        $missing_files[] = $file;
    }
}

echo "<h3>Step 3: Purple Theme Consistency Check</h3>";

// Check for purple theme usage in existing files
$purple_theme_elements = [
    'cause-purple' => 'Primary Purple Color',
    'cause-purple-dark' => 'Dark Purple Variant',
    'bg-purple-' => 'Purple Backgrounds',
    'text-purple-' => 'Purple Text',
    'border-purple-' => 'Purple Borders',
    '#7C3AED' => 'Hex Purple Color'
];

$theme_consistency_score = 0;
$total_checks = 0;

foreach ($existing_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $file_score = 0;
        
        echo "<h4>Checking: " . basename($file) . "</h4>";
        
        foreach ($purple_theme_elements as $element => $description) {
            $total_checks++;
            if (strpos($content, $element) !== false) {
                echo "✅ {$description} found<br>";
                $file_score++;
                $theme_consistency_score++;
            } else {
                echo "⚠️ {$description} not found<br>";
            }
        }
        
        echo "<p><strong>File Score: {$file_score}/" . count($purple_theme_elements) . "</strong></p><br>";
    }
}

echo "<h3>Step 4: Responsive Design Check</h3>";

$responsive_classes = [
    'grid-cols-1 md:grid-cols-' => 'Responsive Grid',
    'flex-col md:flex-row' => 'Responsive Flex',
    'hidden md:block' => 'Mobile Hidden Elements',
    'text-sm md:text-base' => 'Responsive Typography',
    'p-4 md:p-6' => 'Responsive Padding'
];

$responsive_score = 0;
$responsive_total = 0;

foreach ($existing_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        foreach ($responsive_classes as $class => $description) {
            $responsive_total++;
            if (strpos($content, $class) !== false) {
                $responsive_score++;
            }
        }
    }
}

echo "✅ Responsive Design Score: {$responsive_score}/{$responsive_total}<br>";

echo "<h3>Step 5: Accessibility Check</h3>";

$accessibility_elements = [
    'alt=' => 'Image Alt Text',
    'aria-label=' => 'ARIA Labels',
    'role=' => 'ARIA Roles',
    'tabindex=' => 'Tab Navigation',
    'title=' => 'Tooltips'
];

$accessibility_score = 0;
$accessibility_total = 0;

foreach ($existing_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        foreach ($accessibility_elements as $element => $description) {
            $accessibility_total++;
            if (strpos($content, $element) !== false) {
                $accessibility_score++;
            }
        }
    }
}

echo "✅ Accessibility Score: {$accessibility_score}/{$accessibility_total}<br>";

// Overall UI Consistency Score
$overall_score = 0;
$max_score = 100;

// Theme consistency (40%)
$theme_percentage = $total_checks > 0 ? ($theme_consistency_score / $total_checks) * 40 : 0;
$overall_score += $theme_percentage;

// File completeness (30%)
$file_percentage = count($dashboard_files) > 0 ? (count($existing_files) / count($dashboard_files)) * 30 : 0;
$overall_score += $file_percentage;

// Responsive design (20%)
$responsive_percentage = $responsive_total > 0 ? ($responsive_score / $responsive_total) * 20 : 0;
$overall_score += $responsive_percentage;

// Accessibility (10%)
$accessibility_percentage = $accessibility_total > 0 ? ($accessibility_score / $accessibility_total) * 10 : 0;
$overall_score += $accessibility_percentage;

echo "<h3>Step 6: Overall UI Consistency Score</h3>";

echo "<div style='background: " . ($overall_score >= 85 ? '#10b981' : ($overall_score >= 70 ? '#f59e0b' : '#ef4444')) . "; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3 style='color: white; margin: 0 0 15px 0;'>UI Consistency Score: " . round($overall_score, 1) . "%</h3>";

if ($overall_score >= 85) {
    echo "🎉 <strong>EXCELLENT!</strong> UI is highly consistent across all dashboards<br>";
    echo "✅ Purple theme properly implemented<br>";
    echo "✅ Responsive design elements present<br>";
    echo "✅ Good accessibility practices<br>";
    echo "<br><strong>UI is PRESENTATION READY!</strong>";
} elseif ($overall_score >= 70) {
    echo "⚠️ <strong>GOOD</strong> - UI is mostly consistent with minor improvements needed<br>";
    echo "Most elements follow the purple theme<br>";
    echo "Consider adding more responsive and accessibility features";
} else {
    echo "❌ <strong>NEEDS IMPROVEMENT</strong> - UI consistency issues found<br>";
    echo "Please address missing theme elements and responsive design";
}

echo "</div>";

echo "<h3>Step 7: Recommendations</h3>";

echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px;'>";
echo "<strong>UI Improvement Recommendations:</strong><br>";
echo "1. Ensure all buttons use 'bg-cause-purple hover:bg-cause-purple-dark' classes<br>";
echo "2. Use consistent card styling with 'bg-white rounded-lg shadow-md p-6'<br>";
echo "3. Apply consistent spacing with Tailwind margin/padding classes<br>";
echo "4. Add hover effects to all interactive elements<br>";
echo "5. Ensure proper contrast ratios for accessibility<br>";
echo "6. Use consistent icon sizing (w-5 h-5 for small, w-8 h-8 for large)<br>";
echo "7. Apply consistent form styling across all input fields<br>";
echo "8. Use consistent table styling with alternating row colors<br>";
echo "</div>";

if (!empty($missing_files)) {
    echo "<h3>Missing Files to Create:</h3>";
    echo "<div style='background: #fef2f2; border: 1px solid #f87171; padding: 15px; border-radius: 8px;'>";
    foreach ($missing_files as $file) {
        echo "❌ {$file}<br>";
    }
    echo "</div>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='index.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>🚀 Test UI</a>";
echo "<a href='presentation_ready_test.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Full System Test</a>";
echo "<a href='final_setup.php' style='background: #dc2626; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Run Final Setup</a>";
echo "</div>";
?>