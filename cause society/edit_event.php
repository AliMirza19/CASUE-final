<?php
// Edit Event - For students to edit events that need revision
session_start();
require_once 'config/db.php';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: unauthorized.php");
    exit();
}

// Get event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: my_events.php");
    exit();
}

$event_id = (int)$_GET['id'];

// Fetch event details
try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND student_id = ? AND status = 'revision_needed'");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not available for editing!";
        header("Location: my_events.php");
        exit();
    }
    
    // Fetch event items
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = ? ORDER BY id");
    $stmt->execute([$event_id]);
    $event_items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: my_events.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $expected_date = $_POST['expected_date'] ?? '';
        $venue = trim($_POST['venue'] ?? '');
        $team_member_1 = trim($_POST['team_member_1'] ?? '');
        $team_member_2 = trim($_POST['team_member_2'] ?? '');
        $team_member_3 = trim($_POST['team_member_3'] ?? '');
        
        // Basic validation
        if (empty($title) || empty($description) || empty($expected_date) || empty($venue)) {
            throw new Exception("Please fill in all required fields.");
        }
        
        // Validate date
        if (strtotime($expected_date) < strtotime(date('Y-m-d'))) {
            throw new Exception("Event date cannot be in the past.");
        }
        
        // Process budget items
        $items = $_POST['items'] ?? [];
        $grand_total = 0;
        
        if (empty($items)) {
            throw new Exception("Please add at least one budget item.");
        }
        
        // Calculate grand total
        foreach ($items as $item) {
            if (!empty($item['name']) && !empty($item['quantity']) && !empty($item['rate'])) {
                $quantity = (int)$item['quantity'];
                $rate = (float)$item['rate'];
                $grand_total += $quantity * $rate;
            }
        }
        
        if ($grand_total <= 0) {
            throw new Exception("Total budget must be greater than zero.");
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Update event
        $stmt = $pdo->prepare("UPDATE events SET 
                               title = ?, description = ?, expected_date = ?, venue = ?, 
                               team_member_1 = ?, team_member_2 = ?, team_member_3 = ?, 
                               grand_total = ?, status = 'pending_president', 
                               president_comments = NULL, updated_at = NOW()
                               WHERE id = ? AND student_id = ?");
        
        $stmt->execute([
            $title, $description, $expected_date, $venue,
            $team_member_1 ?: null, $team_member_2 ?: null, $team_member_3 ?: null,
            $grand_total, $event_id, $_SESSION['user_id']
        ]);
        
        // Delete old items
        $stmt = $pdo->prepare("DELETE FROM event_items WHERE event_id = ?");
        $stmt->execute([$event_id]);
        
        // Insert new items
        $stmt = $pdo->prepare("INSERT INTO event_items (event_id, item_name, quantity, unit_rate, total_amount) 
                               VALUES (?, ?, ?, ?, ?)");
        
        foreach ($items as $item) {
            if (!empty($item['name']) && !empty($item['quantity']) && !empty($item['rate'])) {
                $quantity = (int)$item['quantity'];
                $rate = (float)$item['rate'];
                $total = $quantity * $rate;
                
                $stmt->execute([$event_id, $item['name'], $quantity, $rate, $total]);
            }
        }
        
        // Log activity
        $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text, related_event_id) VALUES (?, 'student', ?, ?)");
        $log_stmt->execute([$_SESSION['user_id'], "Revised and resubmitted event for president review", $event_id]);
        
        $pdo->commit();
        
        $_SESSION['success'] = "Event updated and resubmitted for president review!";
        header("Location: my_events.php");
        exit();
        
    } catch(Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    } catch(PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
}

$page_title = "Edit Event";
require_once 'includes/student_header.php';
?>

<!-- Back Button -->
<div class="mb-6">
    <a href="my_events.php" class="inline-flex items-center text-gray-600 hover:text-gray-800">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to My Events
    </a>
</div>

<!-- Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- President's Feedback -->
<?php if ($event['president_comments']): ?>
<div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-orange-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div>
            <h4 class="text-lg font-semibold text-orange-800 mb-2">President's Feedback</h4>
            <p class="text-orange-700 whitespace-pre-line"><?php echo htmlspecialchars($event['president_comments']); ?></p>
            <p class="text-orange-600 text-sm mt-2">Please address the above concerns and resubmit your event.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Event Form -->
<form method="POST" id="eventForm" class="space-y-8">
    <!-- Basic Event Information -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Event Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Event Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Expected Date <span class="text-red-500">*</span></label>
                <input type="date" name="expected_date" value="<?php echo $event['expected_date']; ?>" required
                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-gray-700 font-medium mb-2">Venue <span class="text-red-500">*</span></label>
            <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
        </div>
        
        <div class="mt-6">
            <label class="block text-gray-700 font-medium mb-2">Event Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="4" required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
    </div>
    
    <!-- Team Members -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Team Members (Optional)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Team Member 1</label>
                <input type="text" name="team_member_1" value="<?php echo htmlspecialchars($event['team_member_1'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Team Member 2</label>
                <input type="text" name="team_member_2" value="<?php echo htmlspecialchars($event['team_member_2'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Team Member 3</label>
                <input type="text" name="team_member_3" value="<?php echo htmlspecialchars($event['team_member_3'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
        </div>
    </div>
    
    <!-- Budget Items -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Budget Items</h3>
            <button type="button" onclick="addBudgetItem()" 
                    class="bg-cause-purple hover:bg-cause-purple-dark text-white px-4 py-2 rounded-lg font-medium transition">
                Add Item
            </button>
        </div>
        
        <div id="budgetItems">
            <?php foreach ($event_items as $index => $item): ?>
            <div class="budget-item grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Item Description</label>
                    <input type="text" name="items[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($item['item_name']); ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Quantity</label>
                    <input type="number" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple quantity-input">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Unit Rate (PKR)</label>
                    <input type="number" name="items[<?php echo $index; ?>][rate]" value="<?php echo $item['unit_rate']; ?>" min="0" step="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple rate-input">
                </div>
                
                <div class="flex items-end">
                    <button type="button" onclick="removeBudgetItem(this)" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-medium transition">
                        Remove
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Total Display -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-800">Grand Total:</span>
                <span id="grandTotal" class="text-2xl font-bold text-cause-purple">PKR 0.00</span>
            </div>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="flex justify-between items-center">
        <a href="my_events.php" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
        
        <button type="submit" 
                class="bg-cause-purple hover:bg-cause-purple-dark text-white px-8 py-3 rounded-lg font-semibold transition">
            Update & Resubmit Event
        </button>
    </div>
</form>

<!-- JavaScript for budget calculations -->
<script>
let itemIndex = <?php echo count($event_items); ?>;

function addBudgetItem() {
    const container = document.getElementById('budgetItems');
    const itemHtml = `
        <div class="budget-item grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-2">Item Description</label>
                <input type="text" name="items[${itemIndex}][name]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Quantity</label>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple quantity-input">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Unit Rate (PKR)</label>
                <input type="number" name="items[${itemIndex}][rate]" min="0" step="0.01" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple rate-input">
            </div>
            
            <div class="flex items-end">
                <button type="button" onclick="removeBudgetItem(this)" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-medium transition">
                    Remove
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
    calculateTotal();
    
    // Add event listeners to new inputs
    const newItem = container.lastElementChild;
    newItem.querySelector('.quantity-input').addEventListener('input', calculateTotal);
    newItem.querySelector('.rate-input').addEventListener('input', calculateTotal);
}

function removeBudgetItem(button) {
    button.closest('.budget-item').remove();
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    const items = document.querySelectorAll('.budget-item');
    
    items.forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const rate = parseFloat(item.querySelector('.rate-input').value) || 0;
        total += quantity * rate;
    });
    
    document.getElementById('grandTotal').textContent = 'PKR ' + total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Add event listeners to existing inputs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity-input, .rate-input').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
    calculateTotal();
});
</script>

<?php require_once 'includes/student_footer.php'; ?>