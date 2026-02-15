<?php
// GD Upload Design - Epic E7
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: gd_dashboard.php");
    exit();
}

// Handle form submission - Upload new design
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_category = $_POST['design_category'] ?? '';
    $image_link = trim($_POST['image_link'] ?? '');
    
    if (empty($design_category) || empty($image_link)) {
        $_SESSION['error'] = "Please fill all required fields!";
    } else {
        try {
            // Design upload karo
            $stmt = $pdo->prepare("INSERT INTO event_graphics (event_id, gd_id, design_category, image_link, status, created_at) 
                                   VALUES (:event_id, :gd_id, :category, :link, 'pending_patron', NOW())");
            $stmt->execute([
                'event_id' => $event_id,
                'gd_id' => $_SESSION['user_id'],
                'category' => $design_category,
                'link' => $image_link
            ]);
            
            $_SESSION['success'] = "Design uploaded successfully! Sent to Patron for approval.";
            header("Location: gd_dashboard.php");
            exit();
            
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error uploading design!";
        }
    }
}

$page_title = "Upload Event Design";
require_once 'includes/gd_header.php';

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
        header("Location: gd_dashboard.php");
        exit();
    }
    
    // Existing graphics fetch karo
    $stmt = $pdo->prepare("SELECT * FROM event_graphics WHERE event_id = :event_id ORDER BY created_at DESC");
    $stmt->execute(['event_id' => $event_id]);
    $existing_graphics = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching event details!";
    header("Location: gd_dashboard.php");
    exit();
}
?>

<div class="mb-4">
    <a href="gd_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
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
            <p class="text-sm text-gray-600 mb-1">Budget</p>
            <p class="text-lg font-bold text-cause-purple">PKR <?php echo number_format($event['grand_total'], 2); ?></p>
        </div>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Description</p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
    </div>
</div>

<!-- Existing Graphics -->
<?php if (!empty($existing_graphics)): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Existing Graphics</h3>
        <p class="text-gray-600 text-sm mt-1">Previously uploaded designs for this event</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Design Link</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feedback</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($existing_graphics as $graphic): ?>
                <tr>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                            <?php echo ucfirst(str_replace('_', ' ', $graphic['design_category'])); ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="<?php echo htmlspecialchars($graphic['image_link']); ?>" target="_blank" 
                           class="text-cause-purple hover:text-cause-purple-dark underline">
                            View Design
                        </a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($graphic['status'] === 'pending_patron'): ?>
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">Pending Approval</span>
                        <?php elseif ($graphic['status'] === 'approved'): ?>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Approved</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?php echo htmlspecialchars($graphic['patron_feedback'] ?? 'No feedback'); ?>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500">
                        <?php echo date('M d, Y', strtotime($graphic['created_at'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Upload New Design Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Upload New Design</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Design Category *</label>
            <select name="design_category" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                <option value="">Select Category</option>
                <option value="poster">Event Poster</option>
                <option value="banner">Event Banner</option>
                <option value="social_media">Social Media Post</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Design Link/URL *</label>
            <input type="url" name="image_link" required 
                   placeholder="https://example.com/design-image.jpg"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Upload your design to Google Drive, Dropbox, or any image hosting service and paste the public link here</p>
        </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-blue-800 font-medium text-sm">Design Guidelines</p>
                <ul class="text-blue-700 text-sm mt-1 space-y-1">
                    <li>• Ensure high resolution (minimum 1080px width)</li>
                    <li>• Include event title, date, venue, and CAUSE branding</li>
                    <li>• Use CAUSE purple theme colors (#7C3AED)</li>
                    <li>• Make sure the link is publicly accessible</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" 
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Upload Design for Approval
        </button>
    </div>
</form>

<?php require_once 'includes/gd_footer.php'; ?>