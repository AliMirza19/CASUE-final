<?php
// President Approved Events page
session_start();
require_once 'config/db.php';

$page_title = "Approved Events";
require_once 'includes/president_header.php';

// Approved events fetch karo (jo president ne forward kiye hain)
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id) as item_count
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status IN ('pending_patron', 'pending_hod', 'approved', 'completed') 
                           AND e.term_id = :term_id
                           ORDER BY e.updated_at DESC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $approved_events = $stmt->fetchAll();
} catch(PDOException $e) {
    $approved_events = [];
    $_SESSION['error'] = "Error fetching approved events!";
}

// Status badge helper
function getStatusBadge($status) {
    $badges = [
        'pending_patron' => ['bg-blue-100 text-blue-800', 'With Patron'],
        'pending_hod' => ['bg-purple-100 text-purple-800', 'With HOD'],
        'approved' => ['bg-green-100 text-green-800', 'Approved'],
        'completed' => ['bg-gray-100 text-gray-800', 'Completed']
    ];
    return $badges[$status] ?? ['bg-gray-100 text-gray-800', ucfirst($status)];
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Approved Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Events Forwarded by President</h3>
        <p class="text-gray-600 text-sm mt-1">Track the progress of events you have approved</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($approved_events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500">No events forwarded yet</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($approved_events as $event): ?>
                        <?php $badge = getStatusBadge($event['status']); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="text-sm text-gray-500 mt-1"><?php echo $event['item_count']; ?> budget items</div>
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
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge[0]; ?>">
                                    <?php echo $badge[1]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="view_event.php?id=<?php echo $event['id']; ?>" 
                                   class="text-cause-purple hover:text-cause-purple-dark text-sm font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/president_footer.php'; ?>