<?php
// My Events - Student page to view submitted events with review loop
session_start();
require_once 'config/db.php';

$page_title = "My Events";

// Handle forward to patron action
if (isset($_GET['forward']) && is_numeric($_GET['forward'])) {
    $event_id = (int)$_GET['forward'];
    
    try {
        // Only allow forwarding if status is president_approved
        $stmt = $pdo->prepare("UPDATE events SET status = 'pending_patron', updated_at = NOW() 
                               WHERE id = ? AND student_id = ? AND status = 'president_approved'");
        $stmt->execute([$event_id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            // Log activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, related_event_id) VALUES (?, 'student', ?, ?)");
            $log_stmt->execute([$_SESSION['user_id'], "Forwarded approved event to Patron for budget review", $event_id]);
            
            $_SESSION['success'] = "Event forwarded to Patron for detailed budget review!";
        } else {
            $_SESSION['error'] = "Event not found or not approved by President yet!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error forwarding event!";
    }
    
    header("Location: my_events.php");
    exit();
}

require_once 'includes/student_header.php';

// Student ke events fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, 
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id) as item_count
                           FROM events e 
                           WHERE e.student_id = :student_id AND e.term_id = :term_id 
                           ORDER BY e.created_at DESC");
    $stmt->execute([
        'student_id' => $_SESSION['user_id'],
        'term_id' => $_SESSION['term_id']
    ]);
    $events = $stmt->fetchAll();
} catch(PDOException $e) {
    $events = [];
    $_SESSION['error'] = "Error fetching events!";
}

// Status badge helper function
function getStatusBadge($status) {
    $badges = [
        'pending_president' => ['bg-yellow-100 text-yellow-800', 'Pending President Review'],
        'revision_needed' => ['bg-orange-100 text-orange-800', 'Revision Needed'],
        'president_approved' => ['bg-blue-100 text-blue-800', 'President OK - Ready to Forward'],
        'pending_patron' => ['bg-purple-100 text-purple-800', 'Pending Patron Review'],
        'pending_hod' => ['bg-indigo-100 text-indigo-800', 'Pending HOD Approval'],
        'pending_sa' => ['bg-pink-100 text-pink-800', 'Pending Student Affairs'],
        'approved' => ['bg-green-100 text-green-800', 'Approved'],
        'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
        'completed' => ['bg-gray-100 text-gray-800', 'Completed']
    ];
    return $badges[$status] ?? ['bg-gray-100 text-gray-800', ucfirst($status)];
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

<!-- Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">My Submitted Events</h3>
        <?php if ($system_active): ?>
        <a href="request_event.php" class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Event
        </a>
        <?php endif; ?>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 mb-2">No events submitted yet</p>
                                <?php if ($system_active): ?>
                                <a href="request_event.php" class="text-cause-purple hover:text-cause-purple-dark font-medium">Submit your first event →</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <?php $badge = getStatusBadge($event['status']); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo $event['item_count']; ?> budget items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                <?php echo date('M d, Y', strtotime($event['expected_date'])); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php echo htmlspecialchars($event['venue']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge[0]; ?>">
                                    <?php echo $badge[1]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 text-sm">
                                <?php echo date('M d, Y', strtotime($event['updated_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col space-y-1">
                                    <!-- View Details (always available) -->
                                    <a href="view_event.php?id=<?php echo $event['id']; ?>" class="text-cause-purple hover:text-cause-purple-dark font-medium text-sm">
                                        View Details
                                    </a>
                                    
                                    <!-- Status-specific actions -->
                                    <?php if ($event['status'] === 'revision_needed'): ?>
                                        <!-- Show president comments and edit button -->
                                        <div class="mt-2 p-2 bg-orange-50 rounded border border-orange-200">
                                            <p class="text-xs text-orange-800 font-medium mb-1">President's Feedback:</p>
                                            <p class="text-xs text-orange-700 mb-2"><?php echo htmlspecialchars($event['president_comments'] ?? 'No comments provided'); ?></p>
                                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" 
                                               class="inline-flex items-center text-xs bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit & Resubmit
                                            </a>
                                        </div>
                                    <?php elseif ($event['status'] === 'president_approved'): ?>
                                        <!-- Forward to Patron button -->
                                        <div class="mt-2">
                                            <a href="?forward=<?php echo $event['id']; ?>" 
                                               onclick="return confirm('Forward this event to Patron for budget review?');"
                                               class="inline-flex items-center text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                                Forward to Patron
                                            </a>
                                        </div>
                                    <?php elseif ($event['status'] === 'approved'): ?>
                                        <!-- View graphics and volunteers -->
                                        <a href="student_view_graphics.php?id=<?php echo $event['id']; ?>" class="text-green-600 hover:text-green-700 font-medium text-xs">
                                            View Graphics
                                        </a>
                                        <a href="student_view_volunteers.php?id=<?php echo $event['id']; ?>" class="text-blue-600 hover:text-blue-700 font-medium text-xs">
                                            View Volunteers
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Workflow Information -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-lg font-semibold text-blue-800 mb-2">Event Approval Workflow</h4>
            <div class="text-blue-700 text-sm space-y-1">
                <p><strong>Step 1:</strong> Submit event → <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending President Review</span></p>
                <p><strong>Step 2:</strong> President reviews → Either <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Revision Needed</span> or <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">President OK</span></p>
                <p><strong>Step 3:</strong> If approved, you forward → <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Pending Patron Review</span></p>
                <p><strong>Step 4:</strong> Patron → HOD → Student Affairs → <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Approved</span></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/student_footer.php'; ?>