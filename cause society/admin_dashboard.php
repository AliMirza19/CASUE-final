<?php
// Enhanced Admin Dashboard - Main page with term selection
session_start();
require_once 'config/db.php';

$page_title = "Dashboard";

// Admin header include karo (session check bhi usme hai)
require_once 'includes/admin_header.php';

// Get selected term for stats (default to active term)
$selected_term_id = isset($_GET['term_id']) ? (int)$_GET['term_id'] : null;

// Dashboard stats fetch karo
try {
    // Total terms count karo
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM academic_terms");
    $total_terms = $stmt->fetch()['total'];
    
    // Current active term fetch karo
    $stmt = $pdo->query("SELECT * FROM academic_terms WHERE status = 'active' LIMIT 1");
    $active_term = $stmt->fetch();
    $active_term_name = $active_term ? $active_term['term_name'] : 'No Active Term';
    
    // If no term selected, use active term
    if (!$selected_term_id && $active_term) {
        $selected_term_id = $active_term['id'];
    }
    
    // Get all terms for dropdown
    $stmt = $pdo->query("SELECT id, term_name, status FROM academic_terms ORDER BY created_at DESC");
    $all_terms = $stmt->fetchAll();
    
    // Get selected term info
    $selected_term_info = null;
    if ($selected_term_id) {
        $stmt = $pdo->prepare("SELECT * FROM academic_terms WHERE id = ?");
        $stmt->execute([$selected_term_id]);
        $selected_term_info = $stmt->fetch();
    }
    
    // Term-specific stats
    $term_events_count = 0;
    $term_budget_spent = 0;
    $term_pending_events = 0;
    $term_approved_events = 0;
    
    if ($selected_term_id) {
        // Events count for selected term
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE term_id = ?");
        $stmt->execute([$selected_term_id]);
        $term_events_count = $stmt->fetch()['count'];
        
        // Budget spent in selected term
        $stmt = $pdo->prepare("SELECT SUM(grand_total) as total FROM events WHERE term_id = ? AND status = 'approved'");
        $stmt->execute([$selected_term_id]);
        $term_budget_spent = $stmt->fetch()['total'] ?? 0;
        
        // Pending events in selected term
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE term_id = ? AND status LIKE 'pending%'");
        $stmt->execute([$selected_term_id]);
        $term_pending_events = $stmt->fetch()['count'];
        
        // Approved events in selected term
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE term_id = ? AND status = 'approved'");
        $stmt->execute([$selected_term_id]);
        $term_approved_events = $stmt->fetch()['count'];
    }
    
    // Current HOD fetch karo
    $stmt = $pdo->query("SELECT name FROM users WHERE role = 'hod' LIMIT 1");
    $hod = $stmt->fetch();
    $hod_name = $hod ? $hod['name'] : 'Not Assigned';
    
    // Check if active term is expired
    $active_term_expired = false;
    if ($active_term && strtotime($active_term['end_date']) < time()) {
        $active_term_expired = true;
    }
    
} catch(PDOException $e) {
    $total_terms = 0;
    $active_term_name = 'Error';
    $hod_name = 'Error';
    $all_terms = [];
    $selected_term_info = null;
    $term_events_count = 0;
    $term_budget_spent = 0;
    $term_pending_events = 0;
    $term_approved_events = 0;
    $active_term_expired = false;
    error_log("Dashboard Error: " . $e->getMessage());
}
?>

<!-- Success/Error Messages -->
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

<!-- Active Term Expired Alert -->
<?php if ($active_term_expired): ?>
<div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-6 mb-8 rounded-lg">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-orange-800">⚠️ Active Term Expired!</h3>
            <p class="text-orange-700 mt-1">
                Current active term <strong><?php echo htmlspecialchars($active_term_name); ?></strong> 
                expired on <?php echo date('F d, Y', strtotime($active_term['end_date'])); ?>. 
                <a href="manage_terms.php" class="font-medium underline">Manage terms</a> to activate a new one.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Welcome Section with Term Selection -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-xl font-semibold text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h3>
            <p class="text-gray-600 mt-1">Manage system and view term-specific statistics</p>
        </div>
        
        <!-- Term Selection Dropdown -->
        <div class="mt-4 md:mt-0">
            <label class="block text-gray-700 font-medium mb-2">View Stats for Term:</label>
            <select onchange="window.location.href='admin_dashboard.php?term_id=' + this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
                <option value="">-- Select Term --</option>
                <?php foreach ($all_terms as $term): ?>
                    <option value="<?php echo $term['id']; ?>" 
                            <?php echo $selected_term_id == $term['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($term['term_name']); ?>
                        <?php if ($term['status'] === 'active'): ?>
                            (Active)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Selected Term Info -->
<?php if ($selected_term_info): ?>
<div class="bg-gradient-to-r from-cause-purple to-purple-600 rounded-lg shadow-md p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($selected_term_info['term_name']); ?></h3>
            <p class="text-purple-200 mt-1">
                <?php echo date('F d, Y', strtotime($selected_term_info['start_date'])); ?> - 
                <?php echo date('F d, Y', strtotime($selected_term_info['end_date'])); ?>
            </p>
        </div>
        <div class="text-right">
            <?php
            $status_class = $selected_term_info['status'] === 'active' ? 'bg-green-500' : 
                           ($selected_term_info['status'] === 'completed' ? 'bg-blue-500' : 'bg-gray-500');
            ?>
            <span class="px-3 py-1 <?php echo $status_class; ?> text-white rounded-full text-sm font-medium">
                <?php echo ucfirst($selected_term_info['status']); ?>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Enhanced Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Terms Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Terms</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_terms; ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Term Events Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Events in Selected Term</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $term_events_count; ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo $term_approved_events; ?> approved, <?php echo $term_pending_events; ?> pending</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Term Budget Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Budget Spent in Term</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">PKR <?php echo number_format($term_budget_spent, 0); ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Current HOD Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Current HOD</p>
                <p class="text-xl font-bold text-gray-800 mt-2"><?php echo htmlspecialchars($hod_name); ?></p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="manage_terms.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Manage Academic Terms</p>
                <p class="text-sm text-gray-600">Create, activate, or complete terms</p>
            </div>
        </a>
        
        <a href="view_all_events.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">View All Events</p>
                <p class="text-sm text-gray-600">Filter events by term</p>
            </div>
        </a>
        
        <a href="admin_bulk_upload.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-green-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Bulk Upload Users</p>
                <p class="text-sm text-gray-600">Upload multiple users via CSV</p>
            </div>
        </a>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
