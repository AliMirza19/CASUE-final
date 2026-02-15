<?php
// HOD Event Approvals - Show pending HOD events
session_start();
require_once 'config/db.php';

$page_title = "Event Approvals";
require_once 'includes/hod_header.php';

// Check karo ke budget locked hai ya nahi
if (!$budget_locked) {
    $_SESSION['error'] = "Please set and lock the budget first!";
    header("Location: manage_budget.php");
    exit();
}

// Pending HOD events fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id AND is_approved_by_patron = 1) as approved_items
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_hod' AND e.term_id = :term_id
                           ORDER BY e.updated_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetchAll();
} catch(PDOException $e) {
    $pending_events = [];
    $_SESSION['error'] = "Error fetching pending events!";
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

<!-- Debug Info -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <p class="text-blue-800 text-sm">
        <strong>Debug Info:</strong> 
        Current Term ID: <?php echo $_SESSION['term_id']; ?> | 
        Budget Locked: <?php echo $budget_locked ? 'Yes' : 'No'; ?> | 
        Found Events: <?php echo count($pending_events); ?>
    </p>
</div>

<?php if (empty($pending_events)): ?>
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center py-12">
        <div class="bg-purple-100 rounded-full p-4 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
            <svg class="w-10 h-10 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Event Approvals</h3>
        <p class="text-gray-600 mb-4">No pending event requests at the moment.</p>
        <p class="text-sm text-gray-500">Events will appear here after Patron review and approval.</p>
        <div class="mt-4">
            <a href="debug_events.php" class="text-cause-purple hover:text-cause-purple-dark text-sm">Debug: View All Events</a>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Pending Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                <?php foreach ($pending_events as $event): ?>
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

<?php require_once 'includes/hod_footer.php'; ?>
