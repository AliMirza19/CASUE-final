<?php
// President Review - Detailed event review with approve/revision actions
session_start();
require_once 'config/db.php';

// Check if president is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'president') {
    header("Location: unauthorized.php");
    exit();
}

// Get event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: president_dashboard.php");
    exit();
}

$event_id = (int)$_GET['id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'request_revision') {
            // Request revision - set status to revision_needed
            $comments = trim($_POST['comments'] ?? '');
            
            if (empty($comments)) {
                $_SESSION['error'] = "Please provide comments explaining what needs to be revised.";
                header("Location: president_review.php?id=" . $event_id);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE events SET status = 'revision_needed', president_comments = ?, updated_at = NOW() 
                                   WHERE id = ? AND status = 'pending_president'");
            $stmt->execute([$comments, $event_id]);
            
            if ($stmt->rowCount() > 0) {
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, related_event_id) VALUES (?, 'president', ?, ?)");
                $log_stmt->execute([$_SESSION['user_id'], "Requested revision for event", $event_id]);
                
                $_SESSION['success'] = "Event sent back to student for revision.";
            } else {
                $_SESSION['error'] = "Event not found or already processed!";
            }
            
        } elseif ($action === 'approve') {
            // Approve - set status to president_approved
            $stmt = $pdo->prepare("UPDATE events SET status = 'president_approved', president_comments = NULL, updated_at = NOW() 
                                   WHERE id = ? AND status = 'pending_president'");
            $stmt->execute([$event_id]);
            
            if ($stmt->rowCount() > 0) {
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, related_event_id) VALUES (?, 'president', ?, ?)");
                $log_stmt->execute([$_SESSION['user_id'], "Approved event details - marked as OK", $event_id]);
                
                $_SESSION['success'] = "Event approved! Student can now forward it to Patron.";
            } else {
                $_SESSION['error'] = "Event not found or already processed!";
            }
        }
        
        header("Location: president_dashboard.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: president_review.php?id=" . $event_id);
        exit();
    }
}

// Fetch event details
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id, u.email as student_email
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = ? AND e.term_id = ?");
    $stmt->execute([$event_id, $_SESSION['term_id']]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found!";
        header("Location: president_dashboard.php");
        exit();
    }
    
    // Check if event is in correct status for review
    if ($event['status'] !== 'pending_president') {
        $_SESSION['error'] = "This event is not pending your review.";
        header("Location: president_dashboard.php");
        exit();
    }
    
    // Fetch event items
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = ? ORDER BY id");
    $stmt->execute([$event_id]);
    $event_items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: president_dashboard.php");
    exit();
}

$page_title = "Review Event";
require_once 'includes/president_header.php';
?>

<!-- Back Button -->
<div class="mb-6">
    <a href="president_dashboard.php" class="inline-flex items-center text-gray-600 hover:text-gray-800">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Dashboard
    </a>
</div>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Event Details Card -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-cause-purple">
        <h3 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($event['title']); ?></h3>
        <p class="text-purple-200 text-sm mt-1">Submitted by <?php echo htmlspecialchars($event['student_name']); ?></p>
    </div>
    
    <div class="p-6">
        <!-- Event Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Student Information</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($event['student_reg_id']); ?></p>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($event['student_email']); ?></p>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Event Details</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-800"><strong>Date:</strong> <?php echo date('F d, Y', strtotime($event['expected_date'])); ?></p>
                    <p class="text-gray-800"><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                    <p class="text-gray-800"><strong>Submitted:</strong> <?php echo date('M d, Y g:i A', strtotime($event['created_at'])); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Description -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Event Description</h4>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($event['description']); ?></p>
            </div>
        </div>
        
        <!-- Team Members -->
        <?php if ($event['team_member_1'] || $event['team_member_2'] || $event['team_member_3']): ?>
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Team Members</h4>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex flex-wrap gap-2">
                    <?php if ($event['team_member_1']): ?>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"><?php echo htmlspecialchars($event['team_member_1']); ?></span>
                    <?php endif; ?>
                    <?php if ($event['team_member_2']): ?>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"><?php echo htmlspecialchars($event['team_member_2']); ?></span>
                    <?php endif; ?>
                    <?php if ($event['team_member_3']): ?>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"><?php echo htmlspecialchars($event['team_member_3']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Budget Items Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Proposed Budget</h3>
        <p class="text-gray-600 text-sm mt-1">Review the itemized budget for this event</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Description</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Rate</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($event_items as $index => $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-600"><?php echo $index + 1; ?></td>
                        <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td class="px-6 py-4 text-center text-gray-600"><?php echo $item['quantity']; ?></td>
                        <td class="px-6 py-4 text-right text-gray-600">PKR <?php echo number_format($item['unit_rate'], 2); ?></td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">PKR <?php echo number_format($item['total_amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-100">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-800">Grand Total:</td>
                    <td class="px-6 py-4 text-right font-bold text-cause-purple text-lg">PKR <?php echo number_format($event['grand_total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Review Actions -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Your Decision</h3>
        <p class="text-gray-600 text-sm mt-1">Review the event and take appropriate action</p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Request Revision -->
            <div class="border border-orange-200 rounded-lg p-6 bg-orange-50">
                <h4 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Request Revision
                </h4>
                <p class="text-orange-700 text-sm mb-4">
                    If the event details need changes, provide your feedback and send it back to the student.
                </p>
                
                <form action="" method="POST">
                    <input type="hidden" name="action" value="request_revision">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Your Comments <span class="text-red-500">*</span></label>
                        <textarea name="comments" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                  placeholder="Explain what needs to be revised..."></textarea>
                    </div>
                    
                    <button type="submit" 
                            onclick="return confirm('Send this event back to the student for revision?');"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Send for Revision
                    </button>
                </form>
            </div>
            
            <!-- Approve -->
            <div class="border border-green-200 rounded-lg p-6 bg-green-50">
                <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approve Event Details
                </h4>
                <p class="text-green-700 text-sm mb-4">
                    If the event details are satisfactory, mark it as OK. The student will then be able to forward it to the Patron for budget review.
                </p>
                
                <div class="bg-white rounded-lg p-4 mb-4 border border-green-200">
                    <h5 class="font-medium text-gray-800 mb-2">What happens next:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Event status changes to "President OK"
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Student can forward to Patron
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Patron reviews detailed budget
                        </li>
                    </ul>
                </div>
                
                <form action="" method="POST">
                    <input type="hidden" name="action" value="approve">
                    
                    <button type="submit" 
                            onclick="return confirm('Approve this event? The student will be able to forward it to Patron.');"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mark as OK
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/president_footer.php'; ?>
