<?php
// Student Activity Log - Complete Activity History
session_start();
require_once 'config/db.php';

$page_title = "Activity Log";
require_once 'includes/student_header.php';

// Fetch comprehensive activity logs
try {
    // Student's own activities
    $stmt = $pdo->prepare("SELECT action_text, created_at, 'own' as activity_type 
                           FROM activity_logs 
                           WHERE user_id = :user_id 
                           
                           UNION ALL
                           
                           SELECT CONCAT('System: ', action_text) as action_text, created_at, 'system' as activity_type
                           FROM activity_logs 
                           WHERE user_role IN ('admin', 'sa', 'hod') 
                           AND (action_text LIKE '%budget%' OR action_text LIKE '%term%' OR action_text LIKE '%election%')
                           
                           ORDER BY created_at DESC 
                           LIMIT 50");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $activities = $stmt->fetchAll();
    
    // Get student's events for context
    $stmt = $pdo->prepare("SELECT id, title, status, created_at FROM events 
                           WHERE student_id = :student_id 
                           ORDER BY created_at DESC");
    $stmt->execute(['student_id' => $_SESSION['user_id']]);
    $student_events = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $activities = [];
    $student_events = [];
    $_SESSION['error'] = "Error loading activity logs!";
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Activity Log</h3>
            <p class="text-gray-600 mt-1">Complete history of your activities and system updates</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" 
                    class="bg-cause-purple hover:bg-cause-purple-dark text-white px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Log
            </button>
            <a href="student_dashboard.php" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Activity Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo count($student_events); ?></p>
                <p class="text-gray-600">Events Submitted</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">
                    <?php echo count(array_filter($student_events, function($e) { return $e['status'] === 'approved'; })); ?>
                </p>
                <p class="text-gray-600">Approved Events</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo count($activities); ?></p>
                <p class="text-gray-600">Total Activities</p>
            </div>
        </div>
    </div>
</div>

<!-- Activity Timeline -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Activity Timeline</h3>
        <p class="text-gray-600 text-sm mt-1">Chronological history of all activities</p>
    </div>
    
    <div class="p-6">
        <?php if (empty($activities)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No activities found</p>
                <p class="text-gray-400">Activities will appear here as you use the system</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php 
                $current_date = '';
                foreach ($activities as $activity): 
                    $activity_date = date('Y-m-d', strtotime($activity['created_at']));
                    $show_date_header = ($current_date !== $activity_date);
                    $current_date = $activity_date;
                ?>
                
                <?php if ($show_date_header): ?>
                    <div class="flex items-center my-6">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <div class="px-4 py-2 bg-gray-100 rounded-full">
                            <span class="text-sm font-medium text-gray-600">
                                <?php echo date('F d, Y', strtotime($activity['created_at'])); ?>
                            </span>
                        </div>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>
                <?php endif; ?>
                
                <div class="flex items-start space-x-4">
                    <!-- Timeline dot -->
                    <div class="flex-shrink-0 mt-1">
                        <?php if ($activity['activity_type'] === 'own'): ?>
                            <div class="w-3 h-3 bg-cause-purple rounded-full"></div>
                        <?php else: ?>
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Activity content -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-gray-800 text-sm">
                                        <?php echo htmlspecialchars($activity['action_text']); ?>
                                    </p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">
                                            <?php echo date('g:i A', strtotime($activity['created_at'])); ?>
                                        </span>
                                        <?php if ($activity['activity_type'] === 'own'): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Your Activity
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                System Update
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More (if needed) -->
            <div class="text-center mt-8">
                <p class="text-gray-500 text-sm">
                    Showing last 50 activities. 
                    <a href="#" class="text-cause-purple hover:text-cause-purple-dark font-medium">
                        Contact admin for complete history
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .print\\:hidden { display: none !important; }
    body { font-size: 12px; }
    .bg-white { background: white !important; }
    .shadow-md { box-shadow: none !important; }
    .rounded-lg { border-radius: 0 !important; }
    .text-cause-purple { color: #7C3AED !important; }
}
</style>

<?php require_once 'includes/student_footer.php'; ?>