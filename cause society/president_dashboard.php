<?php
// President Dashboard - Event Review with Review Loop
session_start();
require_once 'config/db.php';

$page_title = "Event Reviews";
require_once 'includes/president_header.php';

// Fetch pending events for president review
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id) as item_count
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_president' AND e.term_id = :term_id
                           ORDER BY e.created_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetchAll();
    
    // Also fetch events that were approved by president (waiting for student to forward)
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'president_approved' AND e.term_id = :term_id
                           ORDER BY e.updated_at DESC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $approved_events = $stmt->fetchAll();
    
    // Fetch events sent for revision
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'revision_needed' AND e.term_id = :term_id
                           ORDER BY e.updated_at DESC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $revision_events = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $pending_events = [];
    $approved_events = [];
    $revision_events = [];
    $_SESSION['error'] = "Error fetching events!";
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

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending Review</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo count($pending_events); ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Awaiting Revision</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo count($revision_events); ?></p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Approved (Awaiting Forward)</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo count($approved_events); ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Pending Review Events -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
        <h3 class="text-lg font-semibold text-gray-800">Events Pending Your Review</h3>
        <p class="text-gray-600 text-sm mt-1">Review event details and either approve or request revisions</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
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
                                <p class="text-gray-500">No events pending review</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_events as $event): ?>
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
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                <?php echo date('M d, Y', strtotime($event['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="president_review.php?id=<?php echo $event['id']; ?>" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Events Sent for Revision -->
<?php if (!empty($revision_events)): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
        <h3 class="text-lg font-semibold text-gray-800">Events Awaiting Student Revision</h3>
        <p class="text-gray-600 text-sm mt-1">These events have been sent back to students for changes</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Your Comments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent On</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($revision_events as $event): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                <?php echo htmlspecialchars($event['president_comments'] ?? 'No comments'); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            <?php echo date('M d, Y', strtotime($event['updated_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Awaiting Revision
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Approved Events (Waiting for Student to Forward) -->
<?php if (!empty($approved_events)): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
        <h3 class="text-lg font-semibold text-gray-800">Approved Events (Awaiting Student Action)</h3>
        <p class="text-gray-600 text-sm mt-1">These events are approved and waiting for students to forward to Patron</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved On</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($approved_events as $event): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">
                            <?php echo date('M d, Y', strtotime($event['updated_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                President OK
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/president_footer.php'; ?>
