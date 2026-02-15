<?php
// Student Dashboard - Main page
session_start();
require_once 'config/db.php';

$page_title = "Dashboard";
require_once 'includes/student_header.php';

// Dashboard stats fetch karo
try {
    // Total events submitted by this student
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM events WHERE student_id = :student_id AND term_id = :term_id");
    $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
    $total_events = $stmt->fetch()['total'];
    
    // Approved events count
    $stmt = $pdo->prepare("SELECT COUNT(*) as approved FROM events WHERE student_id = :student_id AND term_id = :term_id AND status = 'approved'");
    $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
    $approved_events = $stmt->fetch()['approved'];
    
    // Pending approvals count
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM events WHERE student_id = :student_id AND term_id = :term_id AND status LIKE 'pending%'");
    $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetch()['pending'];
    
    // Check voting status (Epic E8 - Elections)
    $stmt = $pdo->prepare("SELECT * FROM election_settings WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $election_settings = $stmt->fetch();
    
    $voting_enabled = $election_settings && $election_settings['voting_enabled'];
    $current_time = date('Y-m-d H:i:s');
    $voting_period_active = false;
    
    if ($election_settings) {
        $voting_period_active = ($current_time >= $election_settings['voting_start_date'] && 
                                $current_time <= $election_settings['voting_end_date']);
    }
    
    // Check if student has voted
    $stmt = $pdo->prepare("SELECT id FROM votes WHERE student_id = :student_id AND term_id = :term_id");
    $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
    $has_voted = $stmt->fetch() ? true : false;
    
} catch(PDOException $e) {
    $total_events = 0;
    $approved_events = 0;
    $pending_events = 0;
    $voting_enabled = false;
    $voting_period_active = false;
    $has_voted = false;
    error_log("Student Dashboard Error: " . $e->getMessage());
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- System Inactive Warning -->
<?php if (!$system_active): ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span><strong>System is currently inactive.</strong> Event submissions are disabled until the HOD sets the term budget.</span>
    </div>
<?php endif; ?>

<!-- Welcome Section -->
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h3>
    <p class="text-gray-600 mt-1">Here's your event submission overview for the current term.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Events Submitted -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Events Submitted</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_events; ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Approved Events -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $approved_events; ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending Approvals</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $pending_events; ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Election Voting Section (Epic E8) -->
<?php if ($voting_enabled && $voting_period_active): ?>
<div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold mb-2">🗳️ Society Elections - Vote Now!</h3>
            <?php if ($has_voted): ?>
                <p class="text-purple-100">Thank you for voting! Your vote has been recorded.</p>
                <p class="text-purple-200 text-sm mt-1">Voting ends: <?php echo date('F d, Y g:i A', strtotime($election_settings['voting_end_date'])); ?></p>
            <?php else: ?>
                <p class="text-purple-100">Cast your vote for the Society President. Your voice matters!</p>
                <p class="text-purple-200 text-sm mt-1">Voting ends: <?php echo date('F d, Y g:i A', strtotime($election_settings['voting_end_date'])); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($has_voted): ?>
                <a href="voting_portal.php" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                    View Results
                </a>
            <?php else: ?>
                <a href="voting_portal.php" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition animate-pulse">
                    Vote Now
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php elseif ($voting_enabled && !$voting_period_active): ?>
<div class="bg-gray-100 border border-gray-300 rounded-lg p-6 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">🗳️ Society Elections</h3>
            <p class="text-gray-600">Voting period: <?php echo date('F d, Y', strtotime($election_settings['voting_start_date'])); ?> - <?php echo date('F d, Y', strtotime($election_settings['voting_end_date'])); ?></p>
        </div>
        <div>
            <span class="bg-gray-300 text-gray-600 px-6 py-3 rounded-lg font-semibold">
                Voting Closed
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Candidate Profile Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Election Candidacy</h3>
        <a href="candidate_setup.php" class="text-cause-purple hover:text-cause-purple-dark font-medium text-sm">
            Manage Profile →
        </a>
    </div>
    
    <?php
    // Check if student has candidate profile
    try {
        $stmt = $pdo->prepare("SELECT status FROM candidate_profiles WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $_SESSION['user_id']]);
        $candidate_profile = $stmt->fetch();
        
        // Recent activity logs fetch karo
        $stmt = $pdo->prepare("SELECT action_text, created_at FROM activity_logs 
                               WHERE user_id = :user_id OR user_role = 'admin' OR user_role = 'sa'
                               ORDER BY created_at DESC LIMIT 5");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $recent_activities = $stmt->fetchAll();
        
    } catch(PDOException $e) {
        $candidate_profile = null;
        $recent_activities = [];
    }
    ?>
    
    <?php if ($candidate_profile): ?>
        <div class="flex items-center">
            <div class="p-2 rounded-full <?php echo $candidate_profile['status'] === 'approved' ? 'bg-green-100' : ($candidate_profile['status'] === 'rejected' ? 'bg-red-100' : 'bg-orange-100'); ?> mr-3">
                <svg class="w-5 h-5 <?php echo $candidate_profile['status'] === 'approved' ? 'text-green-600' : ($candidate_profile['status'] === 'rejected' ? 'text-red-600' : 'text-orange-600'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Candidate Profile Status</p>
                <p class="text-sm <?php echo $candidate_profile['status'] === 'approved' ? 'text-green-600' : ($candidate_profile['status'] === 'rejected' ? 'text-red-600' : 'text-orange-600'); ?>">
                    <?php 
                    echo $candidate_profile['status'] === 'approved' ? 'Approved for Election' : 
                        ($candidate_profile['status'] === 'rejected' ? 'Rejected' : 'Pending Patron Approval');
                    ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-4">
            <p class="text-gray-600 mb-3">Interested in running for Society President?</p>
            <a href="candidate_setup.php" class="bg-cause-purple hover:bg-cause-purple-dark text-white px-6 py-2 rounded-lg font-medium transition">
                Submit Candidate Profile
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if ($system_active): ?>
        <a href="request_event.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Request New Event</p>
                <p class="text-sm text-gray-600">Submit a new event proposal</p>
            </div>
        </a>
        <?php else: ?>
        <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50 cursor-not-allowed opacity-60">
            <div class="bg-gray-200 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-500">Request New Event</p>
                <p class="text-sm text-gray-400">System inactive</p>
            </div>
        </div>
        <?php endif; ?>
        
        <a href="my_events.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">View My Events</p>
                <p class="text-sm text-gray-600">Check status of your submissions</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h4>
    
    <?php if (empty($recent_activities)): ?>
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500">Abhi tak koi activity nahi hai</p>
            <p class="text-gray-400 text-sm">Jab aap events submit karenge to yahan activity dikhegi</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($recent_activities as $activity): ?>
                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                    <div class="p-1 bg-cause-purple rounded-full mr-3 mt-1">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-800 text-sm"><?php echo htmlspecialchars($activity['action_text']); ?></p>
                        <p class="text-gray-500 text-xs mt-1"><?php echo date('M d, Y g:i A', strtotime($activity['created_at'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-4 text-center">
            <a href="student_activity_log.php" class="text-cause-purple hover:text-cause-purple-dark text-sm font-medium">
                View All Activity →
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/student_footer.php'; ?>
