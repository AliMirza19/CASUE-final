<?php
// View Event Details - Student page
session_start();
require_once 'config/db.php';

$page_title = "Event Details";
require_once 'includes/student_header.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: my_events.php");
    exit();
}

// Event fetch karo (sirf apna event dekh sakta hai)
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id 
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = :id AND e.student_id = :student_id");
    $stmt->execute(['id' => $event_id, 'student_id' => $_SESSION['user_id']]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or access denied!";
        header("Location: my_events.php");
        exit();
    }
    
    // Event items fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_items WHERE event_id = :event_id");
    $stmt->execute(['event_id' => $event_id]);
    $items = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: my_events.php");
    exit();
}

// Status badge helper
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
$badge = getStatusBadge($event['status']);
?>

<div class="mb-4">
    <a href="my_events.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to My Events
    </a>
</div>

<!-- Event Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
            <p class="text-gray-600 mt-1">Submitted on <?php echo date('M d, Y', strtotime($event['created_at'])); ?></p>
        </div>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo $badge[0]; ?>">
            <?php echo $badge[1]; ?>
        </span>
    </div>
    
    <?php if ($event['status'] === 'rejected' && !empty($event['rejection_reason'])): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <p class="text-red-800 font-medium">Rejection Reason:</p>
        <p class="text-red-700"><?php echo htmlspecialchars($event['rejection_reason']); ?></p>
    </div>
    <?php endif; ?>
    
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

<!-- Budget Items -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Budget Breakdown</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Rate</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $i = 1; foreach ($items as $item): ?>
                <tr>
                    <td class="px-6 py-4 text-gray-600"><?php echo $i++; ?></td>
                    <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td class="px-6 py-4 text-right text-gray-600"><?php echo $item['quantity']; ?></td>
                    <td class="px-6 py-4 text-right text-gray-600">PKR <?php echo number_format($item['unit_rate'], 2); ?></td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-800">PKR <?php echo number_format($item['total_amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-800">Grand Total:</td>
                    <td class="px-6 py-4 text-right text-xl font-bold text-cause-purple">PKR <?php echo number_format($event['grand_total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once 'includes/student_footer.php'; ?>
