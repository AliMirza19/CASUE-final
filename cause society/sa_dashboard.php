<?php
// SA Dashboard - Main page
session_start();
require_once 'config/db.php';

$page_title = "Pending Final Approvals";
require_once 'includes/sa_header.php';

// Pending SA events fetch karo (jo HOD se aaye hain)
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id AND is_approved_by_patron = 1) as approved_items
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_sa' AND e.term_id = :term_id
                           ORDER BY e.updated_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetchAll();
    
    // Debug: Check all pending_sa events regardless of term
    $stmt_debug = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                                 FROM events e 
                                 JOIN users u ON e.student_id = u.id 
                                 WHERE e.status = 'pending_sa'
                                 ORDER BY e.updated_at ASC");
    $stmt_debug->execute();
    $all_pending_sa = $stmt_debug->fetchAll();
    
} catch(PDOException $e) {
    $pending_events = [];
    $all_pending_sa = [];
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
        SA Term ID: <?php echo $_SESSION['term_id']; ?> | 
        Found Events (matching term): <?php echo count($pending_events); ?> |
        All pending_sa events: <?php echo count($all_pending_sa); ?>
    </p>
    <?php if (!empty($all_pending_sa)): ?>
        <div class="mt-2 text-xs text-blue-700">
            <strong>All pending_sa events:</strong>
            <?php foreach ($all_pending_sa as $event): ?>
                <span class="inline-block bg-blue-100 px-2 py-1 rounded mr-2 mb-1">
                    ID:<?php echo $event['id']; ?> | Term:<?php echo $event['term_id']; ?> | <?php echo htmlspecialchars($event['title']); ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Pending Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Events Pending Student Affairs Final Approval</h3>
        <p class="text-gray-600 text-sm mt-1">Final review and approval of HOD-approved events</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Final Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pending_events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500">No events pending final approval</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_events as $event): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="text-sm text-gray-500 mt-1"><?php echo $event['approved_items']; ?> approved items</div>
                                <div class="text-sm text-gray-500"><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800"><?php echo date('M d, Y', strtotime($event['expected_date'])); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['venue']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                                <div class="text-xs text-gray-500">HOD Approved</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Pending SA
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="sa_review_event.php?id=<?php echo $event['id']; ?>" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    Final Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/sa_footer.php'; ?>
