<?php
// Patron Review Event - Epic E5 Budget Control
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: patron_dashboard.php");
    exit();
}

// Handle form submission - Patron review process karo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $items = $_POST['items'] ?? [];
    
    if ($action === 'request_revision') {
        // Status ko revision_requested kar do
        try {
            $stmt = $pdo->prepare("UPDATE events SET status = 'revision_requested' WHERE id = :id AND status = 'pending_patron'");
            $stmt->execute(['id' => $event_id]);
            
            $_SESSION['success'] = "Event sent back to student for revision!";
            header("Location: patron_dashboard.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error requesting revision!";
        }
    } elseif ($action === 'forward_to_hod') {
        // Items update karo aur HOD ko forward karo
        try {
            $pdo->beginTransaction();
            
            $new_grand_total = 0;
            
            // Har item ko update karo
            foreach ($items as $item_id => $item_data) {
                $quantity = intval($item_data['quantity']);
                $rate = floatval($item_data['rate']);
                $total = $quantity * $rate;
                $comment = trim($item_data['comment'] ?? '');
                $approved = isset($item_data['approved']) ? 1 : 0;
                
                $stmt = $pdo->prepare("UPDATE event_items SET 
                                       quantity = :quantity, 
                                       unit_rate = :rate, 
                                       total_amount = :total,
                                       patron_comment = :comment,
                                       is_approved_by_patron = :approved
                                       WHERE id = :id");
                $stmt->execute([
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                    'comment' => $comment,
                    'approved' => $approved,
                    'id' => $item_id
                ]);
                
                // Sirf approved items ka total add karo
                if ($approved) {
                    $new_grand_total += $total;
                }
            }
            
            // Event ka grand total update karo aur status change karo
            $stmt = $pdo->prepare("UPDATE events SET grand_total = :total, status = 'pending_hod' WHERE id = :id");
            $stmt->execute(['total' => $new_grand_total, 'id' => $event_id]);
            
            $pdo->commit();
            
            $_SESSION['success'] = "Event reviewed and forwarded to HOD!";
            header("Location: patron_dashboard.php");
            exit();
        } catch(PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error processing review!";
        }
    }
}

$page_title = "Review Event";
require_once 'includes/patron_header.php';

// Event aur items fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id 
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = :id AND e.status = 'pending_patron'");
    $stmt->execute(['id' => $event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not available for review!";
        header("Location: patron_dashboard.php");
        exit();
    }
    
    // Event items fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = :event_id ORDER BY id");
    $stmt->execute(['event_id' => $event_id]);
    $items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: patron_dashboard.php");
    exit();
}
?>
<div class="mb-4">
    <a href="patron_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Pending Reviews
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
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
            Pending Patron Review
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

<!-- Budget Review Form -->
<form method="POST" id="reviewForm">
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Budget Items Review</h3>
            <p class="text-gray-600 text-sm mt-1">Review and modify quantities, rates, and approve/reject individual items</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="budgetTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Unit Rate (PKR)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Total (PKR)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Approve</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                    <tr class="border-b item-row">
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" 
                                   name="items[<?php echo $item['id']; ?>][quantity]" 
                                   value="<?php echo $item['quantity']; ?>"
                                   min="1" required
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-center qty-input"
                                   onchange="calculateRow(this)">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" 
                                   name="items[<?php echo $item['id']; ?>][rate]" 
                                   value="<?php echo $item['unit_rate']; ?>"
                                   min="0" step="0.01" required
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-center rate-input"
                                   onchange="calculateRow(this)">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-gray-800 total-display">PKR <?php echo number_format($item['total_amount'], 2); ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" 
                                   name="items[<?php echo $item['id']; ?>][approved]" 
                                   value="1"
                                   <?php echo $item['is_approved_by_patron'] ? 'checked' : ''; ?>
                                   class="w-5 h-5 text-cause-purple approve-checkbox"
                                   onchange="calculateGrandTotal()">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" 
                                   name="items[<?php echo $item['id']; ?>][comment]" 
                                   value="<?php echo htmlspecialchars($item['patron_comment'] ?? ''); ?>"
                                   placeholder="Optional comment"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Grand Total Display -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 font-medium">Approved Items Total:</span>
                <span class="text-2xl font-bold text-cause-purple" id="grandTotal">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Only approved items will be included in the final budget</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <button type="submit" name="action" value="request_revision"
                onclick="return confirm('Send this event back to student for revision?');"
                class="px-6 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition">
            Request Revision
        </button>
        <button type="submit" name="action" value="forward_to_hod"
                onclick="return confirm('Forward this event to HOD with your review?');"
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Forward to HOD
        </button>
    </div>
</form>

<script>
// Row ka total calculate karo
function calculateRow(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const total = qty * rate;
    
    row.querySelector('.total-display').textContent = 'PKR ' + total.toFixed(2);
    calculateGrandTotal();
}

// Grand total calculate karo (sirf approved items ka)
function calculateGrandTotal() {
    const rows = document.querySelectorAll('.item-row');
    let grandTotal = 0;
    
    rows.forEach(row => {
        const isApproved = row.querySelector('.approve-checkbox').checked;
        if (isApproved) {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
            grandTotal += qty * rate;
        }
    });
    
    document.getElementById('grandTotal').textContent = 'PKR ' + grandTotal.toFixed(2);
}

// Page load par grand total calculate karo
document.addEventListener('DOMContentLoaded', function() {
    calculateGrandTotal();
});
</script>

<?php require_once 'includes/patron_footer.php'; ?>