<?php
// VC Assign Volunteers
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: vc_dashboard.php");
    exit();
}

// Handle form submission - Add new volunteer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_volunteer') {
        $volunteer_name = trim($_POST['volunteer_name'] ?? '');
        $volunteer_contact = trim($_POST['volunteer_contact'] ?? '');
        $role_description = trim($_POST['role_description'] ?? '');
        
        if (empty($volunteer_name) || empty($role_description)) {
            $_SESSION['error'] = "Please fill all required fields!";
        } else {
            try {
                // Volunteer assign karo
                $stmt = $pdo->prepare("INSERT INTO event_volunteers (event_id, vc_id, volunteer_name, volunteer_contact, role_description, assigned_at) 
                                       VALUES (:event_id, :vc_id, :name, :contact, :role, NOW())");
                $stmt->execute([
                    'event_id' => $event_id,
                    'vc_id' => $_SESSION['user_id'],
                    'name' => $volunteer_name,
                    'contact' => $volunteer_contact,
                    'role' => $role_description
                ]);
                
                $_SESSION['success'] = "Volunteer assigned successfully!";
                
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error assigning volunteer!";
            }
        }
    } elseif ($action === 'remove_volunteer') {
        $volunteer_id = intval($_POST['volunteer_id'] ?? 0);
        
        if ($volunteer_id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM event_volunteers WHERE id = :id AND event_id = :event_id");
                $stmt->execute(['id' => $volunteer_id, 'event_id' => $event_id]);
                
                $_SESSION['success'] = "Volunteer removed successfully!";
                
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error removing volunteer!";
            }
        }
    }
}

$page_title = "Assign Event Volunteers";
require_once 'includes/vc_header.php';

// Event details fetch karo
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id 
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.id = :id AND e.status = 'approved'");
    $stmt->execute(['id' => $event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $_SESSION['error'] = "Event not found or not approved!";
        header("Location: vc_dashboard.php");
        exit();
    }
    
    // Assigned volunteers fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_volunteers WHERE event_id = :event_id ORDER BY assigned_at DESC");
    $stmt->execute(['event_id' => $event_id]);
    $assigned_volunteers = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: vc_dashboard.php");
    exit();
}
?>

<div class="mb-4">
    <a href="vc_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Dashboard
    </a>
</div>

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

<!-- Event Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
            <p class="text-gray-600 mt-1">Organized by <?php echo htmlspecialchars($event['student_name']); ?> (<?php echo htmlspecialchars($event['student_reg_id']); ?>)</p>
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
            <p class="text-sm text-gray-600 mb-1">Volunteers Assigned</p>
            <p class="text-lg font-bold text-cause-purple"><?php echo count($assigned_volunteers); ?> Volunteers</p>
        </div>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Description</p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
    </div>
</div>

<!-- Add New Volunteer Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Assign New Volunteer</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Volunteer Name *</label>
            <input type="text" name="volunteer_name" required 
                   placeholder="Enter volunteer's full name"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact (Optional)</label>
            <input type="text" name="volunteer_contact" 
                   placeholder="Phone number or email"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role/Duty *</label>
            <select name="role_description" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                <option value="">Select Role</option>
                <option value="Registration Desk">Registration Desk</option>
                <option value="Security & Crowd Control">Security & Crowd Control</option>
                <option value="Stage Management">Stage Management</option>
                <option value="Audio/Visual Support">Audio/Visual Support</option>
                <option value="Guest Reception">Guest Reception</option>
                <option value="Photography/Videography">Photography/Videography</option>
                <option value="Refreshment Management">Refreshment Management</option>
                <option value="Decoration Setup">Decoration Setup</option>
                <option value="Cleanup Team">Cleanup Team</option>
                <option value="General Support">General Support</option>
            </select>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" name="action" value="add_volunteer"
                class="px-6 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Assign Volunteer
        </button>
    </div>
</form>

<!-- Assigned Volunteers List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Assigned Volunteers (<?php echo count($assigned_volunteers); ?>)</h3>
        <p class="text-gray-600 text-sm mt-1">Volunteers assigned to this event</p>
    </div>
    
    <?php if (empty($assigned_volunteers)): ?>
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500">No volunteers assigned yet</p>
                <p class="text-gray-400 text-sm">Use the form above to assign volunteers to this event</p>
            </div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volunteer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role/Duty</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assigned Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($assigned_volunteers as $index => $volunteer): ?>
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
                        <td class="px-4 py-3 text-center">
                            <form method="POST" class="inline" onsubmit="return confirm('Remove this volunteer from the event?');">
                                <input type="hidden" name="volunteer_id" value="<?php echo $volunteer['id']; ?>">
                                <button type="submit" name="action" value="remove_volunteer"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/vc_footer.php'; ?>