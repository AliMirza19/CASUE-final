<?php
// Manage Budget - HOD page (Epic E9 - Mandatory Budget Lock)
session_start();
require_once 'config/db.php';

// Check karo ke user logged in hai aur HOD hai
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hod') {
    header("Location: index.php");
    exit();
}

// Handle budget submission - Header include se PEHLE redirect karo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_budget'])) {
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    
    // Current budget check karo
    $stmt = $pdo->prepare("SELECT * FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $current_budget = $stmt->fetch();
    
    // Validation
    if ($total_amount <= 0) {
        $_SESSION['error'] = "Please enter a valid budget amount!";
    } elseif ($current_budget && $current_budget['is_locked'] == 1) {
        $_SESSION['error'] = "Budget is already locked and cannot be modified!";
    } else {
        try {
            if ($current_budget) {
                // Update existing budget aur lock karo
                $stmt = $pdo->prepare("UPDATE budgets SET total_amount = :total, remaining_amount = :remaining, is_locked = 1 WHERE term_id = :term_id");
                $stmt->execute([
                    'total' => $total_amount,
                    'remaining' => $total_amount,
                    'term_id' => $_SESSION['term_id']
                ]);
            } else {
                // Naya budget insert karo aur lock karo
                $stmt = $pdo->prepare("INSERT INTO budgets (term_id, total_amount, remaining_amount, is_locked) VALUES (:term_id, :total, :remaining, 1)");
                $stmt->execute([
                    'term_id' => $_SESSION['term_id'],
                    'total' => $total_amount,
                    'remaining' => $total_amount
                ]);
            }
            
            $_SESSION['success'] = "Budget has been successfully locked!";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error saving budget: " . $e->getMessage();
        }
    }
    // Redirect to avoid form resubmission
    header("Location: manage_budget.php");
    exit();
}

// Ab header include karo
$page_title = "Set Term Budget";
require_once 'includes/hod_header.php';

// Current term ka budget fetch karo
try {
    $stmt = $pdo->prepare("SELECT * FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $current_budget = $stmt->fetch();
} catch(PDOException $e) {
    $current_budget = null;
    $_SESSION['error'] = "Error fetching budget information!";
}

// Current term info fetch karo
try {
    $stmt = $pdo->prepare("SELECT term_name FROM academic_terms WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['term_id']]);
    $term_info = $stmt->fetch();
    $term_name = $term_info ? $term_info['term_name'] : 'Unknown Term';
} catch(PDOException $e) {
    $term_name = 'Unknown Term';
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Current Term Info -->
<div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <svg class="w-6 h-6 text-cause-purple mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <div>
            <p class="text-sm text-purple-600">Current Active Term</p>
            <p class="font-semibold text-purple-800"><?php echo htmlspecialchars($term_name); ?></p>
        </div>
    </div>
</div>

<?php if ($current_budget && $current_budget['is_locked'] == 1): ?>
    <!-- Budget is Locked - Display Only -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-6">
            <div class="bg-green-100 rounded-full p-3 mr-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Budget is Locked</h3>
                <p class="text-gray-600">The budget for this term has been set and cannot be modified.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-cause-purple">
                <p class="text-sm text-gray-600 mb-1">Total Budget Amount</p>
                <p class="text-3xl font-bold text-gray-800">Rs. <?php echo number_format($current_budget['total_amount'], 2); ?></p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-600 mb-1">Remaining Balance</p>
                <p class="text-3xl font-bold text-gray-800">Rs. <?php echo number_format($current_budget['remaining_amount'], 2); ?></p>
            </div>
        </div>
        
        <?php 
        $used_amount = $current_budget['total_amount'] - $current_budget['remaining_amount'];
        $usage_percent = $current_budget['total_amount'] > 0 ? ($used_amount / $current_budget['total_amount']) * 100 : 0;
        ?>
        <div class="mt-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Budget Usage</span>
                <span><?php echo number_format($usage_percent, 1); ?>% used</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-cause-purple h-4 rounded-full transition-all duration-300" style="width: <?php echo $usage_percent; ?>%"></div>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-800 text-sm">
                <strong>Note:</strong> Once the budget is locked, it cannot be changed. The remaining balance will be updated automatically as events are approved.
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- Budget Form - Not Yet Locked -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-6">
            <div class="bg-yellow-100 rounded-full p-3 mr-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Set Term Budget</h3>
                <p class="text-gray-600">Enter the total budget amount for this term. Once set, it will be locked.</p>
            </div>
        </div>
        
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Budget Amount (Rs.)</label>
                <input type="number" name="total_amount" required min="1" step="0.01" placeholder="Enter total budget amount"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent text-lg">
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800 text-sm">
                    <strong>Warning:</strong> Once you set and lock the budget, it cannot be changed. Please make sure the amount is correct.
                </p>
            </div>
            
            <button type="submit" name="set_budget" onclick="return confirm('Are you sure you want to lock this budget? This action cannot be undone.');"
                class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-4 rounded-lg transition">
                Set & Lock Budget
            </button>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/hod_footer.php'; ?>
