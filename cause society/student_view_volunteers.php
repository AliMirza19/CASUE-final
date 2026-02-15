<?php
// Student View Volunteers
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: my_events.php");
    exit();
}

$page_title = "Event Volunteers";
require_once 'includes/student_header.php';

// Event aur volunteers fetch karo
try {
    // Check karo ke event student ka hai aur approved hai
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND student_id = :student_id AND status = 'approved'");
    $stmt->execute(['id' => $event_id, 'student_id' => $_SESSION['user_id']]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not accessible!";
        header("Location: my_events.php");
        exit();
    }
    
    // Volunteers fetch karo
    $stmt = $pdo->prepare("SELECT ev.*, vc.name as vc_name, vc.reg_id as vc_reg_id
                           FROM event_volunteers ev
                           JOIN users vc ON ev.vc_id = vc.id
                           WHERE ev.event_id = :event_id
                           ORDER BY ev.role_description, ev.assigned_at");
    $stmt->execute(['event_id' => $event_id]);
    $volunteers = $stmt->fetchAll();
    
    // Group volunteers by role
    $volunteers_by_role = [];
    foreach ($volunteers as $volunteer) {
        $role = $volunteer['role_description'];
        if (!isset($volunteers_by_role[$role])) {
            $volunteers_by_role[$role] = [];
        }
        $volunteers_by_role[$role][] = $volunteer;
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching volunteers!";
    header("Location: my_events.php");
    exit();
}
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
            <p class="text-gray-600 mt-1">Volunteer Team & Assignments</p>
        </div>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
            Approved Event
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Event Date</p>
            <p class="font-semibold text-gray-800"><?php echo date('F d, Y', strtotime($event['expected_date'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Venue</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($event['venue']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Total Volunteers</p>
            <p class="text-lg font-bold text-cause-purple"><?php echo count($volunteers); ?> Volunteers</p>
        </div>
    </div>
</div>

<!-- Volunteers Overview -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Volunteer Team Overview</h3>
        <p class="text-gray-600 text-sm mt-1">Volunteers assigned to help with your event</p>
    </div>
    
    <?php if (empty($volunteers)): ?>
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 mb-2">No volunteers assigned yet</p>
                <p class="text-gray-400 text-sm">Volunteers will be assigned by the Volunteer Coordinator and appear here</p>
            </div>
        </div>
    <?php else: ?>
        <div class="p-6">
            <!-- Volunteers by Role -->
            <?php foreach ($volunteers_by_role as $role => $role_volunteers): ?>
                <div class="mb-8 last:mb-0">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-cause-purple rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($role); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo count($role_volunteers); ?> volunteer(s) assigned</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($role_volunteers as $volunteer): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-800"><?php echo htmlspecialchars($volunteer['volunteer_name']); ?></h5>
                                        <?php if ($volunteer['volunteer_contact']): ?>
                                            <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($volunteer['volunteer_contact']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="text-xs text-gray-500">
                                    Assigned by <?php echo htmlspecialchars($volunteer['vc_name']); ?> on 
                                    <?php echo date('M d, Y', strtotime($volunteer['assigned_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Volunteer Contact Information -->
<?php if (!empty($volunteers)): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Complete Volunteer List</h3>
        <p class="text-gray-600 text-sm mt-1">All volunteers with contact information</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteer Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role/Duty</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assigned Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($volunteers as $index => $volunteer): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600"><?php echo $index + 1; ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800"><?php echo htmlspecialchars($volunteer['volunteer_name']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($volunteer['volunteer_contact'] ?? 'Not provided'); ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                            <?php echo htmlspecialchars($volunteer['role_description']); ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500">
                        <?php echo date('M d, Y', strtotime($volunteer['assigned_at'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/student_footer.php'; ?>