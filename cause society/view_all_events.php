<?php
// View All Events - Admin page to see events from all terms
session_start();
require_once 'config/db.php';

$page_title = "All Events Management";
require_once 'includes/admin_header.php';

// Get selected term (default to active term)
$selected_term_id = isset($_GET['term_id']) ? (int)$_GET['term_id'] : null;

// If no term selected, get active term
if (!$selected_term_id) {
    try {
        $stmt = $pdo->query("SELECT id FROM academic_terms WHERE status = 'active' LIMIT 1");
        $active_term = $stmt->fetch();
        $selected_term_id = $active_term ? $active_term['id'] : null;
    } catch(PDOException $e) {
        $selected_term_id = null;
    }
}

// Fetch all terms for dropdown
try {
    $stmt = $pdo->query("SELECT id, term_name, status FROM academic_terms ORDER BY created_at DESC");
    $all_terms = $stmt->fetchAll();
} catch(PDOException $e) {
    $all_terms = [];
}

// Fetch events for selected term
$events = [];
$term_info = null;
$stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'total_budget' => 0];

if ($selected_term_id) {
    try {
        // Get term info
        $stmt = $pdo->prepare("SELECT * FROM academic_terms WHERE id = ?");
        $stmt->execute([$selected_term_id]);
        $term_info = $stmt->fetch();
        
        // Get events with student details
        $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                               t.term_name,
                               (SELECT COUNT(*) FROM event_items WHERE event_id = e.id) as item_count
                               FROM events e
                               JOIN users u ON e.student_id = u.id
                               JOIN academic_terms t ON e.term_id = t.id
                               WHERE e.term_id = ?
                               ORDER BY e.created_at DESC");
        $stmt->execute([$selected_term_id]);
        $events = $stmt->fetchAll();
        
        // Calculate statistics
        foreach ($events as $event) {
            $stats['total']++;
            if ($event['status'] === 'approved') {
                $stats['approved']++;
                $stats['total_budget'] += $event['grand_total'];
            } elseif (strpos($event['status'], 'pending') !== false) {
                $stats['pending']++;
            } elseif ($event['status'] === 'rejected') {
                $stats['rejected']++;
            }
        }
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error fetching events data!";
    }
}

// Status badge helper function
function getStatusBadge($status) {
    $badges = [
        'pending_president' => ['bg-yellow-100 text-yellow-800', 'Pending President'],
        'revision_needed' => ['bg-orange-100 text-orange-800', 'Revision Needed'],
        'president_approved' => ['bg-blue-100 text-blue-800', 'President OK'],
        'pending_patron' => ['bg-purple-100 text-purple-800', 'Pending Patron'],
        'pending_hod' => ['bg-indigo-100 text-indigo-800', 'Pending HOD'],
        'pending_sa' => ['bg-pink-100 text-pink-800', 'Pending SA'],
        'approved' => ['bg-green-100 text-green-800', 'Approved'],
        'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
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

<!-- Term Selection and Info -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
            <h3 class="text-lg font-semibold text-gray-800">Events Filter by Term</h3>
            <p class="text-gray-600 text-sm">Select academic term to view its events</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <label class="text-gray-700 font-medium">Select Term:</label>
            <select onchange="window.location.href='view_all_events.php?term_id=' + this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
                <option value="">-- Select Term --</option>
                <?php foreach ($all_terms as $term): ?>
                    <option value="<?php echo $term['id']; ?>" 
                            <?php echo $selected_term_id == $term['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($term['term_name']); ?>
                        <?php if ($term['status'] === 'active'): ?>
                            (Active)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<?php if ($term_info): ?>
<!-- Term Information -->
<div class="bg-gradient-to-r from-cause-purple to-purple-600 rounded-lg shadow-md p-6 mb-8 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($term_info['term_name']); ?></h3>
            <p class="text-purple-200 mt-1">
                <?php echo date('F d, Y', strtotime($term_info['start_date'])); ?> - 
                <?php echo date('F d, Y', strtotime($term_info['end_date'])); ?>
            </p>
        </div>
        <div class="text-right">
            <?php
            $status_class = $term_info['status'] === 'active' ? 'bg-green-500' : 
                           ($term_info['status'] === 'completed' ? 'bg-blue-500' : 'bg-gray-500');
            ?>
            <span class="px-3 py-1 <?php echo $status_class; ?> text-white rounded-full text-sm font-medium">
                <?php echo ucfirst($term_info['status']); ?>
            </span>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Events</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['total']; ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['approved']; ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending Events</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['pending']; ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Budget</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">PKR <?php echo number_format($stats['total_budget'], 0); ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Events in <?php echo htmlspecialchars($term_info['term_name']); ?></h3>
                <p class="text-gray-600 text-sm mt-1">Complete list of events for this academic term</p>
            </div>
            <button onclick="window.print()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
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
                                <p class="text-gray-500">No events found for this term</p>
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
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['venue']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badge[0]; ?>">
                                    <?php echo $badge[1]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <?php echo date('M d, Y', strtotime($event['expected_date'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 text-sm">
                                <?php echo date('M d, Y', strtotime($event['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="view_event.php?id=<?php echo $event['id']; ?>" 
                                   class="text-cause-purple hover:text-cause-purple-dark font-medium text-sm">
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

<?php else: ?>
<!-- No Term Selected -->
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Term Selected</h3>
    <p class="text-gray-600">Please select an academic term to view its events</p>
</div>
<?php endif; ?>

<!-- Print Styles -->
<style>
@media print {
    .print\\:hidden { display: none !important; }
    body { font-size: 12px; }
    .bg-white { background: white !important; }
    .shadow-md { box-shadow: none !important; }
    .rounded-lg { border-radius: 0 !important; }
    .text-cause-purple { color: #7C3AED !important; }
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>