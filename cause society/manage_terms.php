<?php
// Enhanced Manage Terms - Advanced Term Management
session_start();
require_once 'config/db.php';

// Handle new term creation BEFORE header include
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_term'])) {
    try {
        $term_name = trim($_POST['term_name']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        
        // Validation
        if (empty($term_name) || empty($start_date) || empty($end_date)) {
            throw new Exception("Tamam fields required hain!");
        }
        
        if (strtotime($end_date) <= strtotime($start_date)) {
            throw new Exception("End date start date se baad honi chahiye!");
        }
        
        // Check for duplicate term name
        $stmt = $pdo->prepare("SELECT id FROM academic_terms WHERE term_name = ?");
        $stmt->execute([$term_name]);
        if ($stmt->fetch()) {
            throw new Exception("Is naam ka term pehle se mojood hai!");
        }
        
        // Create new term (inactive by default)
        $stmt = $pdo->prepare("INSERT INTO academic_terms (term_name, status, start_date, end_date) 
                               VALUES (?, 'inactive', ?, ?)");
        $stmt->execute([$term_name, $start_date, $end_date]);
        
        // Log activity
        $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text) VALUES (?, 'admin', ?)");
        $log_stmt->execute([$_SESSION['user_id'], "Created new academic term: {$term_name}"]);
        
        $_SESSION['success'] = "Naya term '{$term_name}' successfully create ho gaya!";
        
    } catch(Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: manage_terms.php");
    exit();
}

$page_title = "Manage Academic Terms";
require_once 'includes/admin_header.php';

// Fetch all terms with statistics
try {
    $stmt = $pdo->query("SELECT t.*, 
                         COUNT(e.id) as total_events,
                         SUM(CASE WHEN e.status = 'approved' THEN e.grand_total ELSE 0 END) as total_spent,
                         CASE 
                             WHEN t.end_date < CURDATE() THEN 'expired'
                             WHEN t.start_date > CURDATE() THEN 'future'
                             ELSE 'current'
                         END as date_status
                         FROM academic_terms t
                         LEFT JOIN events e ON t.id = e.term_id
                         GROUP BY t.id
                         ORDER BY t.created_at DESC");
    $terms = $stmt->fetchAll();
    
    // Get current active term
    $stmt = $pdo->query("SELECT * FROM academic_terms WHERE status = 'active' LIMIT 1");
    $active_term = $stmt->fetch();
    
} catch(PDOException $e) {
    $terms = [];
    $active_term = null;
    $_SESSION['error'] = "Error fetching terms data!";
}

// Check if active term is expired
$show_expired_alert = false;
if ($active_term && strtotime($active_term['end_date']) < time()) {
    $show_expired_alert = true;
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

<!-- Expired Term Alert -->
<?php if ($show_expired_alert): ?>
<div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-6 mb-8 rounded-lg">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-orange-800">⚠️ Active Term Expired!</h3>
            <p class="text-orange-700 mt-1">
                <strong><?php echo htmlspecialchars($active_term['term_name']); ?></strong> ki date 
                <strong><?php echo date('F d, Y', strtotime($active_term['end_date'])); ?></strong> ko khatam ho chuki hai.
                <br>Agli term activate karein ya current term ko extend karein.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Current Active Term Info -->
<?php if ($active_term): ?>
<div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-green-800">Currently Active Term</h3>
            <p class="text-green-700 mt-1">
                <strong><?php echo htmlspecialchars($active_term['term_name']); ?></strong>
                (<?php echo date('M d, Y', strtotime($active_term['start_date'])); ?> - 
                <?php echo date('M d, Y', strtotime($active_term['end_date'])); ?>)
            </p>
        </div>
        <div class="text-right">
            <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm font-medium">Active</span>
            <?php if ($show_expired_alert): ?>
                <p class="text-red-600 text-sm mt-1 font-medium">Expired</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Create New Term -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Create New Academic Term</h3>
    
    <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Term Name</label>
            <input type="text" name="term_name" required
                   placeholder="e.g., Spring 2025"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
        </div>
        
        <div>
            <label class="block text-gray-700 font-medium mb-2">Start Date</label>
            <input type="date" name="start_date" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
        </div>
        
        <div>
            <label class="block text-gray-700 font-medium mb-2">End Date</label>
            <input type="date" name="end_date" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
        </div>
        
        <div class="flex items-end">
            <button type="submit" name="create_term"
                    class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white px-4 py-2 rounded-lg font-medium transition">
                Create Term
            </button>
        </div>
    </form>
</div>

<!-- Terms Management Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Academic Terms</h3>
        <p class="text-gray-600 text-sm mt-1">Manage term status and view statistics</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term Details</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Duration</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statistics</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($terms)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">No academic terms found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($terms as $term): ?>
                        <tr class="hover:bg-gray-50">
                            <!-- Term Details -->
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($term['term_name']); ?></div>
                                <div class="text-sm text-gray-500">Created: <?php echo date('M d, Y', strtotime($term['created_at'])); ?></div>
                                <?php if ($term['date_status'] === 'expired'): ?>
                                    <div class="text-xs text-red-600 font-medium mt-1">⚠️ Date Expired</div>
                                <?php elseif ($term['date_status'] === 'future'): ?>
                                    <div class="text-xs text-blue-600 font-medium mt-1">🔮 Future Term</div>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Duration -->
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-800"><?php echo date('M d, Y', strtotime($term['start_date'])); ?></div>
                                <div class="text-xs text-gray-500">to</div>
                                <div class="text-sm text-gray-800"><?php echo date('M d, Y', strtotime($term['end_date'])); ?></div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <?php
                                $status_classes = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-800', 
                                    'completed' => 'bg-blue-100 text-blue-800'
                                ];
                                $status_class = $status_classes[$term['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                    <?php echo ucfirst($term['status']); ?>
                                </span>
                            </td>
                            
                            <!-- Statistics -->
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-800"><?php echo $term['total_events']; ?> Events</div>
                                <div class="text-xs text-gray-500">PKR <?php echo number_format($term['total_spent'] ?? 0, 0); ?> Spent</div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <?php if ($term['status'] === 'active'): ?>
                                        <!-- Active term actions -->
                                        <form method="POST" action="toggle_term.php" class="inline">
                                            <input type="hidden" name="term_id" value="<?php echo $term['id']; ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <button type="submit" 
                                                    onclick="return confirm('Is term ko deactivate karna chahte hain?');"
                                                    class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded transition">
                                                Deactivate
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="toggle_term.php" class="inline">
                                            <input type="hidden" name="term_id" value="<?php echo $term['id']; ?>">
                                            <input type="hidden" name="action" value="complete">
                                            <button type="submit" 
                                                    onclick="return confirm('Is term ko complete mark karna chahte hain?');"
                                                    class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition">
                                                Complete
                                            </button>
                                        </form>
                                        
                                    <?php elseif ($term['status'] === 'inactive'): ?>
                                        <!-- Inactive term actions -->
                                        <form method="POST" action="toggle_term.php" class="inline">
                                            <input type="hidden" name="term_id" value="<?php echo $term['id']; ?>">
                                            <input type="hidden" name="action" value="activate">
                                            <button type="submit" 
                                                    onclick="return confirm('Is term ko activate karna chahte hain? Current active term deactivate ho jayega.');"
                                                    class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition">
                                                Activate
                                            </button>
                                        </form>
                                        
                                    <?php else: ?>
                                        <!-- Completed term -->
                                        <span class="text-xs text-gray-500">Completed</span>
                                    <?php endif; ?>
                                    
                                    <!-- View events link -->
                                    <a href="view_all_events.php?term_id=<?php echo $term['id']; ?>" 
                                       class="text-xs bg-cause-purple hover:bg-cause-purple-dark text-white px-3 py-1 rounded transition">
                                        View Events
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Terms Management Guide -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-lg font-semibold text-blue-800 mb-2">Terms Management Guide</h4>
            <div class="text-blue-700 text-sm space-y-1">
                <p><strong>Active:</strong> Current term jismein events submit ho sakte hain</p>
                <p><strong>Inactive:</strong> Term jo abhi active nahi hai lekin activate ho sakta hai</p>
                <p><strong>Completed:</strong> Term jo complete ho chuka hai aur dobara activate nahi ho sakta</p>
                <p><strong>Note:</strong> Sirf ek term ek waqt mein active ho sakta hai</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>