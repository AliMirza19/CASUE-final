<?php
// HOD Dashboard - Main page
session_start();
require_once 'config/db.php';

$page_title = "Dashboard";
require_once 'includes/hod_header.php';

// Dashboard stats fetch karo
try {
    // Budget info fetch karo
    $stmt = $pdo->prepare("SELECT total_amount, remaining_amount, is_locked FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $budget_info = $stmt->fetch();
    
    $total_budget = $budget_info ? number_format($budget_info['total_amount'], 2) : '0.00';
    $remaining_balance = $budget_info ? number_format($budget_info['remaining_amount'], 2) : '0.00';
    $is_budget_locked = $budget_info && $budget_info['is_locked'] == 1;
    
    // Pending event requests count karo (future implementation ke liye)
    $pending_events = 0;
    
    // Current Patron fetch karo
    $stmt = $pdo->prepare("SELECT name FROM users WHERE role = 'patron' AND current_term_id = :term_id LIMIT 1");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $patron = $stmt->fetch();
    $patron_name = $patron ? $patron['name'] : 'Not Assigned';
    
} catch(PDOException $e) {
    $total_budget = '0.00';
    $remaining_balance = '0.00';
    $pending_events = 0;
    $patron_name = 'Error';
    $is_budget_locked = false;
    error_log("HOD Dashboard Error: " . $e->getMessage());
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

<!-- Budget Warning agar lock nahi hai -->
<?php if (!$is_budget_locked): ?>
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Please <a href="manage_budget.php" class="font-semibold underline">set and lock the term budget</a> to enable all features.</span>
    </div>
<?php endif; ?>


<!-- Welcome Section -->
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-800">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h3>
    <p class="text-gray-600 mt-1">Here's your department overview for the current term.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Term Total Budget Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Term Total Budget</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">Rs. <?php echo $total_budget; ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Remaining Balance Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Remaining Balance</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">Rs. <?php echo $remaining_balance; ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pending Event Requests Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending Events</p>
                <p class="text-2xl font-bold text-gray-800 mt-2"><?php echo $pending_events; ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Current Patron Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Current Patron</p>
                <p class="text-xl font-bold text-gray-800 mt-2"><?php echo htmlspecialchars($patron_name); ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="assign_patron.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Assign Patron</p>
                <p class="text-sm text-gray-600">Appoint society patron</p>
            </div>
        </a>
        
        <a href="manage_budget.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Set Term Budget</p>
                <p class="text-sm text-gray-600">Configure budget allocation</p>
            </div>
        </a>
        
        <?php if ($is_budget_locked): ?>
        <a href="hod_event_approvals.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
            <div class="bg-yellow-100 rounded-lg p-3 mr-4">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Event Approvals</p>
                <p class="text-sm text-gray-600">Review pending requests</p>
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
                <p class="font-medium text-gray-500">Event Approvals</p>
                <p class="text-sm text-gray-400">Set budget to unlock</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/hod_footer.php'; ?>
<!-- Pending HOD Approvals -->
<?php if ($is_budget_locked): ?>
<?php
// Pending HOD events fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id AND is_approved_by_patron = 1) as approved_items
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_hod' AND e.term_id = :term_id
                           ORDER BY e.updated_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_hod_events = $stmt->fetchAll();
} catch(PDOException $e) {
    $pending_hod_events = [];
}
?>

<?php if (!empty($pending_hod_events)): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Events Pending HOD Final Approval</h3>
        <p class="text-gray-600 text-sm mt-1">Review patron-approved events and finalize budget allocation</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved Items</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pending_hod_events as $event): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime($event['expected_date'])); ?> • <?php echo htmlspecialchars($event['venue']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-green-600 font-medium"><?php echo $event['approved_items']; ?> items</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="hod_finalize_event.php?id=<?php echo $event['id']; ?>" 
                               class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                Final Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>