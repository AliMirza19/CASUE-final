<?php
// HOD Finalize Event - Epic E6 & E10 (Budget Deduction)
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: hod_dashboard.php");
    exit();
}

// Handle form submission - HOD final approval with budget deduction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $final_budget = floatval($_POST['final_budget'] ?? 0);
    
    if ($action === 'approve_forward') {
        if ($final_budget <= 0) {
            $_SESSION['error'] = "Please enter a valid final budget amount!";
        } else {
            try {
                $pdo->beginTransaction();
                
                // Current budget check karo
                $stmt = $pdo->prepare("SELECT remaining_amount FROM budgets WHERE term_id = :term_id AND is_locked = 1");
                $stmt->execute(['term_id' => $_SESSION['term_id']]);
                $budget = $stmt->fetch();
                
                if (!$budget) {
                    throw new Exception("Budget not found or not locked!");
                }
                
                // Check karo ke balance kafi hai
                if ($budget['remaining_amount'] < $final_budget) {
                    $_SESSION['error'] = "Maazrat! Term budget mein itni raqam mojood nahi hai. Available: PKR " . number_format($budget['remaining_amount'], 2);
                } else {
                    // Budget deduct karo
                    $new_remaining = $budget['remaining_amount'] - $final_budget;
                    $stmt = $pdo->prepare("UPDATE budgets SET remaining_amount = :remaining WHERE term_id = :term_id");
                    $stmt->execute(['remaining' => $new_remaining, 'term_id' => $_SESSION['term_id']]);
                    
                    // Event status aur final budget update karo
                    $stmt = $pdo->prepare("UPDATE events SET grand_total = :budget, status = 'pending_sa' WHERE id = :id");
                    $stmt->execute(['budget' => $final_budget, 'id' => $event_id]);
                    
                    $pdo->commit();
                    
                    $_SESSION['success'] = "Event approved and forwarded to Student Affairs! Budget deducted: PKR " . number_format($final_budget, 2);
                    header("Location: hod_dashboard.php");
                    exit();
                }
            } catch(Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = "Error processing approval: " . $e->getMessage();
            }
        }
    }
}

$page_title = "Finalize Event";
require_once 'includes/hod_header.php';

// Event aur items fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id 
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = :id AND e.status = 'pending_hod'");
    $stmt->execute(['id' => $event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not available for review!";
        header("Location: hod_dashboard.php");
        exit();
    }
    
    // Patron approved items fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = :event_id ORDER BY id");
    $stmt->execute(['event_id' => $event_id]);
    $items = $stmt->fetchAll();
    
    // Current budget info fetch karo
    $stmt = $pdo->prepare("SELECT remaining_amount FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $budget_info = $stmt->fetch();
    $available_budget = $budget_info ? $budget_info['remaining_amount'] : 0;
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: hod_dashboard.php");
    exit();
}
?>
<div class="mb-4">
    <a href="hod_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
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

<!-- Budget Status Alert -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-blue-800 font-medium">Available Term Budget: PKR <?php echo number_format($available_budget, 2); ?></p>
            <p class="text-blue-600 text-sm">Ensure final budget does not exceed available balance</p>
        </div>
    </div>
</div>

<!-- Event Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
            <p class="text-gray-600 mt-1">Submitted by <?php echo htmlspecialchars($event['student_name']); ?> (<?php echo htmlspecialchars($event['student_reg_id']); ?>)</p>
        </div>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
            Pending HOD Final Approval
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Expected Date</p>
            <p class="font-semibold text-gray-800"><?php echo date('F d, Y', strtotime($event['expected_date'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Venue</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($event['venue']); ?></p>
        </div>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Description</p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
    </div>
</div>

<!-- Patron Reviewed Items (Read-only) -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Patron Reviewed Budget Items</h3>
        <p class="text-gray-600 text-sm mt-1">Items reviewed and approved by Patron (Read-only view)</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit Rate</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patron Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $patron_approved_total = 0;
                foreach ($items as $item): 
                    if ($item['is_approved_by_patron']) {
                        $patron_approved_total += $item['total_amount'];
                    }
                ?>
                <tr class="border-b <?php echo $item['is_approved_by_patron'] ? 'bg-green-50' : 'bg-red-50'; ?>">
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></span>
                    </td>
                    <td class="px-4 py-3 text-center"><?php echo $item['quantity']; ?></td>
                    <td class="px-4 py-3 text-center">PKR <?php echo number_format($item['unit_rate'], 2); ?></td>
                    <td class="px-4 py-3 text-center font-semibold">PKR <?php echo number_format($item['total_amount'], 2); ?></td>
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
                    <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-800">Patron Approved Total:</td>
                    <td class="px-4 py-3 text-center text-lg font-bold text-green-600">PKR <?php echo number_format($patron_approved_total, 2); ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- HOD Final Budget Decision -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">HOD Final Budget Decision</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Patron Approved Amount</p>
            <p class="text-2xl font-bold text-green-600">PKR <?php echo number_format($patron_approved_total, 2); ?></p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Final Budget Amount (PKR)</label>
            <input type="number" name="final_budget" required min="1" step="0.01" 
                   value="<?php echo $patron_approved_total; ?>"
                   max="<?php echo $available_budget; ?>"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent text-lg font-semibold">
            <p class="text-xs text-gray-500 mt-1">You can adjust the final amount (max: PKR <?php echo number_format($available_budget, 2); ?>)</p>
        </div>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-yellow-800 text-sm">
            <strong>Important:</strong> Once approved, this amount will be deducted from the term budget and cannot be reversed. 
            The event will be forwarded to Student Affairs for final processing.
        </p>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" name="action" value="approve_forward"
                onclick="return confirm('Are you sure you want to approve this event and deduct the budget? This action cannot be undone.');"
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Approve & Forward to SA
        </button>
    </div>
</form>

<?php require_once 'includes/hod_footer.php'; ?>