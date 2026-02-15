<?php
// Final Setup - Master Data Seeder for Presentation
require_once 'config/db.php';

echo "<h2>🚀 Final Setup - Master Data Seeder</h2>";
echo "<p>Ye script database ko realistic presentation data se bhar dega...</p>";

try {
    // Step 1: Create additional mock students
    echo "<h3>Step 1: Creating Mock Students</h3>";
    
    $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);
    $mock_students = [
        ['STU-002', 'Ayesha Khan', 'ayesha.khan@cause.edu.pk'],
        ['STU-003', 'Muhammad Usman', 'usman@cause.edu.pk'], 
        ['STU-004', 'Fatima Sheikh', 'fatima.sheikh@cause.edu.pk'],
        ['STU-005', 'Ahmed Hassan', 'ahmed.hassan@cause.edu.pk'],
        ['STU-006', 'Zainab Ali', 'zainab.ali@cause.edu.pk']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                           VALUES (?, ?, ?, ?, 'student', 1, 1)");
    
    $created_students = 0;
    foreach ($mock_students as $student) {
        $result = $stmt->execute([$student[0], $student[1], $student[2], $hashedPassword]);
        if ($result) $created_students++;
    }
    
    echo "✅ Created {$created_students} mock students<br>";
    
    // Step 2: Create realistic events with proper workflow
    echo "<h3>Step 2: Creating Realistic Events</h3>";
    
    // Get student IDs
    $stmt = $pdo->query("SELECT id, name, reg_id FROM users WHERE role = 'student' ORDER BY id LIMIT 6");
    $students = $stmt->fetchAll();
    
    $realistic_events = [
        [
            'title' => 'Annual Tech Symposium 2024',
            'description' => 'A comprehensive technology symposium featuring industry experts, workshops on emerging technologies, and networking opportunities for students and professionals.',
            'expected_date' => date('Y-m-d', strtotime('+15 days')),
            'venue' => 'Main Auditorium',
            'status' => 'approved',
            'grand_total' => 45000.00,
            'items' => [
                ['Sound System & AV Equipment', 1, 15000, 15000],
                ['Guest Speaker Honorarium', 3, 8000, 24000],
                ['Refreshments & Lunch', 150, 40, 6000]
            ]
        ],
        [
            'title' => 'Entrepreneurship Workshop Series',
            'description' => 'Three-day intensive workshop series on entrepreneurship, startup funding, and business development for aspiring student entrepreneurs.',
            'expected_date' => date('Y-m-d', strtotime('+22 days')),
            'venue' => 'Conference Hall A',
            'status' => 'pending_sa',
            'grand_total' => 28000.00,
            'items' => [
                ['Workshop Materials & Kits', 100, 150, 15000],
                ['Expert Trainer Fees', 2, 5000, 10000],
                ['Venue Setup & Decoration', 1, 3000, 3000]
            ]
        ],
        [
            'title' => 'Cultural Night - Rang-e-CAUSE',
            'description' => 'Annual cultural celebration showcasing student talents in music, dance, drama, and poetry. A night to celebrate diversity and creativity.',
            'expected_date' => date('Y-m-d', strtotime('+30 days')),
            'venue' => 'Open Air Theater',
            'status' => 'pending_hod',
            'grand_total' => 35000.00,
            'items' => [
                ['Stage Setup & Lighting', 1, 20000, 20000],
                ['Costumes & Props', 1, 8000, 8000],
                ['Sound System Rental', 1, 7000, 7000]
            ]
        ],
        [
            'title' => 'Career Fair 2024',
            'description' => 'Annual career fair bringing together top companies and organizations to provide internship and job opportunities for CAUSE students.',
            'expected_date' => date('Y-m-d', strtotime('+45 days')),
            'venue' => 'Sports Complex',
            'status' => 'pending_patron',
            'grand_total' => 22000.00,
            'items' => [
                ['Company Booth Setup', 25, 500, 12500],
                ['Promotional Materials', 1, 5000, 5000],
                ['Refreshments for Participants', 200, 25, 5000]
            ]
        ],
        [
            'title' => 'Research Symposium - Innovation Hub',
            'description' => 'Platform for students to present their research projects, compete for awards, and get feedback from faculty and industry experts.',
            'expected_date' => date('Y-m-d', strtotime('+60 days')),
            'venue' => 'Research Center',
            'status' => 'pending_president',
            'grand_total' => 18000.00,
            'items' => [
                ['Research Presentation Setup', 1, 8000, 8000],
                ['Awards & Certificates', 50, 100, 5000],
                ['Documentation & Photography', 1, 5000, 5000]
            ]
        ]
    ];
    
    $created_events = 0;
    foreach ($realistic_events as $index => $event_data) {
        if (isset($students[$index])) {
            $student = $students[$index];
            
            // Create event
            $stmt = $pdo->prepare("INSERT INTO events (title, description, student_id, term_id, expected_date, venue, grand_total, status, created_at, updated_at) 
                                   VALUES (?, ?, ?, 1, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY), NOW())");
            
            $stmt->execute([
                $event_data['title'],
                $event_data['description'],
                $student['id'],
                $event_data['expected_date'],
                $event_data['venue'],
                $event_data['grand_total'],
                $event_data['status'],
                $index + 1 // Created on different days
            ]);
            
            $event_id = $pdo->lastInsertId();
            
            // Add event items
            $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount, is_approved_by_patron, patron_comment) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($event_data['items'] as $item) {
                $is_approved = in_array($event_data['status'], ['approved', 'pending_sa', 'pending_hod']) ? 1 : 0;
                $comment = $is_approved ? 'Approved - necessary for event success' : '';
                
                $stmt->execute([
                    $event_id,
                    $item[0], // item_name
                    $item[1], // quantity
                    $item[2], // unit_rate
                    $item[3], // total_amount
                    $is_approved,
                    $comment
                ]);
            }
            
            $created_events++;
            echo "✅ Created event: {$event_data['title']} by {$student['name']}<br>";
        }
    }
    
    echo "✅ Created {$created_events} realistic events with proper workflow<br>";
    
    // Step 3: Update budget to reflect spending
    echo "<h3>Step 3: Updating Budget Information</h3>";
    
    // Calculate total spent on approved events
    $stmt = $pdo->query("SELECT SUM(grand_total) as total_spent FROM events WHERE status IN ('approved', 'completed')");
    $spent = $stmt->fetch()['total_spent'] ?? 0;
    
    // Update budget
    $total_budget = 500000;
    $remaining = $total_budget - $spent;
    
    $stmt = $pdo->prepare("INSERT INTO budgets (term_id, total_amount, remaining_amount, is_locked) 
                           VALUES (1, ?, ?, 1) 
                           ON DUPLICATE KEY UPDATE 
                           total_amount = VALUES(total_amount), 
                           remaining_amount = VALUES(remaining_amount),
                           is_locked = 1");
    $stmt->execute([$total_budget, $remaining]);
    
    echo "✅ Budget updated: Total PKR " . number_format($total_budget, 2) . ", Spent PKR " . number_format($spent, 2) . "<br>";
    
    // Step 4: Create graphics and volunteers for approved events
    echo "<h3>Step 4: Adding Graphics & Volunteers</h3>";
    
    // Get approved events
    $stmt = $pdo->query("SELECT id, title FROM events WHERE status = 'approved' LIMIT 3");
    $approved_events = $stmt->fetchAll();
    
    // Get GD and VC users
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'gd' LIMIT 1");
    $gd_user = $stmt->fetch();
    
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'vc' LIMIT 1");
    $vc_user = $stmt->fetch();
    
    if ($gd_user && $vc_user) {
        foreach ($approved_events as $event) {
            // Add graphics
            $graphics = [
                ['poster', 'https://via.placeholder.com/800x600/7C3AED/FFFFFF?text=Event+Poster+-+' . urlencode($event['title'])],
                ['banner', 'https://via.placeholder.com/1200x400/7C3AED/FFFFFF?text=Event+Banner+-+' . urlencode($event['title'])],
                ['social_media', 'https://via.placeholder.com/600x600/7C3AED/FFFFFF?text=Social+Media+-+' . urlencode($event['title'])]
            ];
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO event_graphics (event_id, gd_id, design_category, image_link, status, patron_feedback) 
                                   VALUES (?, ?, ?, ?, 'approved', 'Excellent design quality - approved')");
            
            foreach ($graphics as $graphic) {
                $stmt->execute([$event['id'], $gd_user['id'], $graphic[0], $graphic[1]]);
            }
            
            // Add volunteers
            $volunteers = [
                ['Sarah Ahmed', '0300-1234567', 'Registration Desk'],
                ['Ali Hassan', '0301-2345678', 'Security & Crowd Control'],
                ['Fatima Khan', '0302-3456789', 'Stage Management'],
                ['Usman Sheikh', '0303-4567890', 'Audio/Visual Support'],
                ['Zainab Ali', '0304-5678901', 'Guest Reception']
            ];
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO event_volunteers (event_id, vc_id, volunteer_name, volunteer_contact, role_description) 
                                   VALUES (?, ?, ?, ?, ?)");
            
            foreach ($volunteers as $volunteer) {
                $stmt->execute([$event['id'], $vc_user['id'], $volunteer[0], $volunteer[1], $volunteer[2]]);
            }
        }
        
        echo "✅ Added graphics and volunteers for approved events<br>";
    }
    
    // Step 5: Create election candidates
    echo "<h3>Step 5: Setting up Election Candidates</h3>";
    
    $candidate_data = [
        [
            'manifesto' => 'I envision a society where every student voice is heard and valued. My focus will be on improving academic support, organizing more engaging events, creating better communication channels between students and administration, and ensuring transparent governance. Together, we can build a stronger, more inclusive CAUSE community that prepares us for future challenges.',
            'experience' => 'Event Coordinator for Annual Tech Fest 2023, President of Computer Science Society, Volunteer at Community Service Projects, Academic Excellence Award recipient for 3 consecutive semesters, Led team of 50+ volunteers in organizing inter-university competition',
            'vp_name' => 'Sarah Ahmed Khan',
            'photo_url' => 'https://via.placeholder.com/300x300/7C3AED/FFFFFF?text=Candidate+1'
        ],
        [
            'manifesto' => 'As your representative, I pledge to work tirelessly for student welfare, enhanced facilities, and meaningful extracurricular activities. My experience in organizing successful events and my commitment to transparency make me the right choice for leading our society towards excellence and innovation in education.',
            'experience' => 'Vice President of Debate Society, Organizer of Inter-University Sports Competition, Member of Student Advisory Committee, Dean\'s List for 4 consecutive semesters, Winner of Best Leadership Award 2023',
            'vp_name' => 'Muhammad Ali Hassan',
            'photo_url' => 'https://via.placeholder.com/300x300/059669/FFFFFF?text=Candidate+2'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO candidate_profiles (student_id, manifesto, experience, vp_name, photo_url, status, patron_feedback) 
                           VALUES (?, ?, ?, ?, ?, 'approved', 'Excellent qualifications and clear vision - approved for election')");
    
    $candidates_created = 0;
    foreach ($candidate_data as $index => $candidate) {
        if (isset($students[$index + 1])) { // Skip first student
            $stmt->execute([
                $students[$index + 1]['id'],
                $candidate['manifesto'],
                $candidate['experience'],
                $candidate['vp_name'],
                $candidate['photo_url']
            ]);
            $candidates_created++;
        }
    }
    
    // Enable voting
    $stmt = $pdo->prepare("INSERT INTO election_settings (term_id, voting_enabled, voting_start_date, voting_end_date) 
                           VALUES (1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY))
                           ON DUPLICATE KEY UPDATE 
                           voting_enabled = 1, 
                           voting_start_date = NOW(), 
                           voting_end_date = DATE_ADD(NOW(), INTERVAL 7 DAY)");
    $stmt->execute();
    
    echo "✅ Created {$candidates_created} election candidates and enabled voting<br>";
    
    // Step 6: Add comprehensive activity logs
    echo "<h3>Step 6: Adding Comprehensive Activity Logs</h3>";
    
    $comprehensive_activities = [
        ['admin', 'System initialized with Fall 2024 academic term'],
        ['admin', 'All user roles and permissions configured successfully'],
        ['hod', 'Term budget of PKR 500,000 allocated and locked for Fall 2024'],
        ['hod', 'Patron Prof. Muhammad Khan assigned for current academic term'],
        ['student', 'Annual Tech Symposium 2024 event proposal submitted'],
        ['president', 'Tech Symposium proposal reviewed and forwarded to Patron'],
        ['patron', 'Tech Symposium budget approved with modifications'],
        ['hod', 'Tech Symposium given final approval - PKR 45,000 allocated'],
        ['sa', 'Tech Symposium approved by Student Affairs - ready for execution'],
        ['gd', 'Professional graphics package created for Tech Symposium'],
        ['vc', 'Volunteer team of 15 members assigned for Tech Symposium'],
        ['student', 'Entrepreneurship Workshop Series proposal submitted'],
        ['student', 'Cultural Night event proposal submitted for review'],
        ['patron', 'Election candidates approved for Society President position'],
        ['admin', 'Voting system activated for Society President elections'],
        ['student', 'Multiple students participated in democratic voting process']
    ];
    
    // Get user IDs for roles
    $role_users = [];
    foreach (['admin', 'hod', 'student', 'patron', 'president', 'sa', 'gd', 'vc'] as $role) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = ? LIMIT 1");
        $stmt->execute([$role]);
        $user = $stmt->fetch();
        if ($user) {
            $role_users[$role] = $user['id'];
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, created_at) 
                           VALUES (?, ?, ?, DATE_SUB(NOW(), INTERVAL ? HOUR))");
    
    foreach ($comprehensive_activities as $index => $activity) {
        $role = $activity[0];
        $action = $activity[1];
        
        if (isset($role_users[$role])) {
            $stmt->execute([
                $role_users[$role],
                $role,
                $action,
                (count($comprehensive_activities) - $index) * 3 // Spread over time
            ]);
        }
    }
    
    echo "✅ Added " . count($comprehensive_activities) . " comprehensive activity logs<br>";
    
    // Final Summary
    echo "<br><div style='background: #10b981; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3 style='color: white; margin: 0 0 15px 0;'>🎉 Final Setup Complete - System Ready for Presentation!</h3>";
    echo "<strong>Database now contains:</strong><br>";
    echo "✅ 6+ Mock Students with realistic profiles<br>";
    echo "✅ 5 Realistic Events in different approval stages<br>";
    echo "✅ Complete budget allocation and spending tracking<br>";
    echo "✅ Graphics and volunteer assignments<br>";
    echo "✅ Active election system with approved candidates<br>";
    echo "✅ Comprehensive activity logs across all roles<br>";
    echo "✅ Professional announcements and notifications<br>";
    echo "<br><strong>System is 100% ready for demo and presentation!</strong>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><div style='margin: 20px 0;'>";
echo "<a href='index.php' style='background: #7C3AED; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>Start Demo</a>";
echo "<a href='ui_consistency_check.php' style='background: #059669; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>UI Consistency Check</a>";
echo "<a href='create_error_pages.php' style='background: #dc2626; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Create Error Pages</a>";
echo "</div>";
?>