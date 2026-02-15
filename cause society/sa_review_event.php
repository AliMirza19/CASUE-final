<?php
// SA Review Event - Final approval process
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: sa_dashboard.php");
    exit();
}

// Handle form submission - SA final decision
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');
    
    if ($action === 'final_approval') {
        try {
            // Event ko approved status kar do
            $stmt = $pdo->prepare("UPDATE events SET status = 'approved' WHERE id = :id AND status = 'pending_sa'");
            $stmt->execute(['id' => $event_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Event has been given final approval!";
                header("Location: sa_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Event not found or already processed!";
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error approving event!";
        }
    } elseif ($action === 'request_revision') {
        if (empty($feedback)) {
            $_SESSION['error'] = "Please provide feedback for revision request!";
        } else {
            try {
                // Event ko wapas HOD ke paas bhej do with feedback
                $stmt = $pdo->prepare("UPDATE events SET status = 'pending_hod', rejection_reason = :feedback WHERE id = :id AND status = 'pending_sa'");
                $stmt->execute(['feedback' => $feedback, 'id' => $event_id]);
                
                $_SESSION['success'] = "Event sent back to HOD for revision!";
                header("Location: sa_dashboard.php");
                exit();
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error requesting revision!";
            }
        }
    }
}

$page_title = "Final Event Review";
require_once 'includes/sa_header.php';

// Event details fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id 
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = :id AND e.status = 'pending_sa'");
    $stmt->execute(['id' => $event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not available for review!";
        header("Location: sa_dashboard.php");
        exit();
    }
    
    // Event items fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = :event_id ORDER BY id");
    $stmt->execute(['event_id' => $event_id]);
    $items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: sa_dashboard.php");
    exit();
}
?>
<div class="mb-4">
    <a href="sa_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Pending Approvals
    </a>
</div>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Event Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
            <p class="text-gray-600 mt-1">Submitted by <?php echo htmlspecialchars($event['student_name']); ?> (<?php echo htmlspecialchars($event['student_reg_id']); ?>)</p>
        </div>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
            Pending Student Affairs Approval
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Expected Date</p>
            <p class="font-semibold text-gray-800"><?php echo date('F d, Y', strtotime($event['expected_date'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Venue</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($event['venue']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Final Approved Budget</p>
            <p class="text-2xl font-bold text-cause-purple">PKR <?php echo number_format($event['grand_total'], 2); ?></p>
        </div>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Description</p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
    </div>
    
    <?php if ($event['team_member_1'] || $event['team_member_2'] || $event['team_member_3']): ?>
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-2">Team Members</p>
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
    <?php endif; ?>
</div>

<!-- Approval History -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Approval History</h3>
    <div class="space-y-3">
        <div class="flex items-center">
            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
            <span class="text-gray-800">✓ Student Submitted</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
            <span class="text-gray-800">✓ President Approved & Forwarded</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
            <span class="text-gray-800">✓ Patron Reviewed & Approved</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
            <span class="text-gray-800">✓ HOD Final Approval & Budget Allocated</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-4 bg-orange-500 rounded-full mr-3"></div>
            <span class="text-gray-800">⏳ Pending Student Affairs Final Approval</span>
        </div>
    </div>
</div>

<!-- Final Budget Breakdown -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Final Budget Breakdown</h3>
        <p class="text-gray-600 text-sm mt-1">Items approved through the complete review process</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit Rate</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $approved_total = 0;
                foreach ($items as $item): 
                    if ($item['is_approved_by_patron']) {
                        $approved_total += $item['total_amount'];
                    }
                ?>
                <tr class="border-b <?php echo $item['is_approved_by_patron'] ? 'bg-green-50' : 'bg-red-50'; ?>">
                    <td class="px-4 py-3 text-gray-600"><?php echo $item['is_approved_by_patron'] ? $i++ : '-'; ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td class="px-4 py-3 text-center text-gray-600"><?php echo $item['quantity']; ?></td>
                    <td class="px-4 py-3 text-center text-gray-600">PKR <?php echo number_format($item['unit_rate'], 2); ?></td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">PKR <?php echo number_format($item['total_amount'], 2); ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($item['is_approved_by_patron']): ?>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?php echo htmlspecialchars($item['patron_comment'] ?? 'No comment'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-100">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-800">Final Approved Total:</td>
                    <td class="px-4 py-3 text-center text-xl font-bold text-cause-purple">PKR <?php echo number_format($event['grand_total'], 2); ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- SA Decision Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Student Affairs Final Decision</h3>
    
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback/Comments (Optional for approval, Required for revision)</label>
        <textarea name="feedback" rows="4" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                  placeholder="Enter any feedback or comments..."></textarea>
    </div>
    
    <div class="flex justify-end space-x-4">
        <button type="submit" name="action" value="request_revision"
                onclick="return confirm('Send this event back to HOD for revision?');"
                class="px-6 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition">
            Request Revision
        </button>
        <button type="submit" name="action" value="final_approval"
                onclick="return confirm('Give final approval to this event? This will make it visible to the student as approved.');"
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Final Approval
        </button>
    </div>
</form>

<?php require_once 'includes/sa_footer.php'; ?>