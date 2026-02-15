<?php
// Volunteer Coordinator Dashboard
session_start();
require_once 'config/db.php';

$page_title = "Volunteer Coordination Portal";
require_once 'includes/vc_header.php';

// Approved events fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_volunteers WHERE event_id = e.id) as volunteers_count
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'approved' AND e.term_id = :term_id
                           ORDER BY e.expected_date ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $approved_events = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $approved_events = [];
    $_SESSION['error'] = "Error fetching approved events!";
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

<!-- Dashboard Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo count($approved_events); ?></p>
                <p class="text-gray-600">Approved Events</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <?php 
                $total_volunteers = 0;
                foreach ($approved_events as $event) {
                    $total_volunteers += $event['volunteers_count'];
                }
                ?>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_volunteers; ?></p>
                <p class="text-gray-600">Total Volunteers</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <?php 
                $pending_assignments = 0;
                foreach ($approved_events as $event) {
                    if ($event['volunteers_count'] == 0) {
                        $pending_assignments++;
                    }
                }
                ?>
                <p class="text-2xl font-bold text-gray-800"><?php echo $pending_assignments; ?></p>
                <p class="text-gray-600">Pending Assignments</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm0 0v4a2 2 0 002 2h4a2 2 0 002-2v-4"></path>
                </svg>
            </div>
            <div>
                <?php 
                $upcoming_events = 0;
                $today = date('Y-m-d');
                foreach ($approved_events as $event) {
                    if ($event['expected_date'] >= $today) {
                        $upcoming_events++;
                    }
                }
                ?>
                <p class="text-2xl font-bold text-gray-800"><?php echo $upcoming_events; ?></p>
                <p class="text-gray-600">Upcoming Events</p>
            </div>
        </div>
    </div>
</div>

<!-- Approved Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Approved Events - Volunteer Assignment</h3>
        <p class="text-gray-600 text-sm mt-1">Assign volunteers to approved events</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Volunteers Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($approved_events)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500">No approved events found</p>
                                <p class="text-gray-400 text-sm">Events will appear here once they are approved by Student Affairs</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($approved_events as $event): ?>
                        <?php 
                        $is_past = strtotime($event['expected_date']) < strtotime(date('Y-m-d'));
                        $is_upcoming = strtotime($event['expected_date']) >= strtotime(date('Y-m-d'));
                        ?>
                        <tr class="hover:bg-gray-50 <?php echo $is_past ? 'opacity-60' : ''; ?>">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="text-sm text-gray-500 mt-1"><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</div>
                                <div class="text-sm text-gray-500">Budget: PKR <?php echo number_format($event['grand_total'], 2); ?></div>
                                <?php if ($is_past): ?>
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full mt-1">Past Event</span>
                                <?php elseif ($is_upcoming): ?>
                                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded-full mt-1">Upcoming</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800"><?php echo date('M d, Y', strtotime($event['expected_date'])); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['venue']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($event['volunteers_count'] > 0): ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?php echo $event['volunteers_count']; ?> Volunteers Assigned
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        No Volunteers Assigned
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="vc_assign_volunteers.php?id=<?php echo $event['id']; ?>" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    <?php echo $event['volunteers_count'] > 0 ? 'Manage Volunteers' : 'Assign Volunteers'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/vc_footer.php'; ?>