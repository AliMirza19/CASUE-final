<?php
// Patron Review Graphics - Epic E7
session_start();
require_once 'config/db.php';

// Graphics ID check karo
$graphics_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$graphics_id) {
    $_SESSION['error'] = "Invalid graphics ID!";
    header("Location: patron_dashboard.php");
    exit();
}

// Handle form submission - Approve or reject graphics
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');
    
    if ($action === 'approve') {
        try {
            $stmt = $pdo->prepare("UPDATE event_graphics SET status = 'approved', patron_feedback = :feedback WHERE id = :id AND status = 'pending_patron'");
            $stmt->execute(['feedback' => $feedback, 'id' => $graphics_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Graphics approved successfully!";
                header("Location: patron_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Graphics not found or already processed!";
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error approving graphics!";
        }
    } elseif ($action === 'reject') {
        if (empty($feedback)) {
            $_SESSION['error'] = "Please provide feedback for rejection!";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE event_graphics SET status = 'rejected', patron_feedback = :feedback WHERE id = :id AND status = 'pending_patron'");
                $stmt->execute(['feedback' => $feedback, 'id' => $graphics_id]);
                
                $_SESSION['success'] = "Graphics rejected with feedback!";
                header("Location: patron_dashboard.php");
                exit();
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error rejecting graphics!";
            }
        }
    }
}

$page_title = "Review Event Graphics";
require_once 'includes/patron_header.php';

// Graphics details fetch karo
try {
    $stmt = $pdo->prepare("SELECT eg.*, e.title as event_title, e.description as event_description, 
                           e.expected_date, e.venue, u.name as student_name, u.reg_id as student_reg_id,
                           gd.name as gd_name, gd.reg_id as gd_reg_id
                           FROM event_graphics eg
                           JOIN events e ON eg.event_id = e.id
                           JOIN users u ON e.student_id = u.id
                           JOIN users gd ON eg.gd_id = gd.id
                           WHERE eg.id = :id AND eg.status = 'pending_patron'");
    $stmt->execute(['id' => $graphics_id]);
    $graphics = $stmt->fetch();
    
    if (!$graphics) {
        $_SESSION['error'] = "Graphics not found or not available for review!";
        header("Location: patron_dashboard.php");
        exit();
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching graphics details!";
    header("Location: patron_dashboard.php");
    exit();
}
?>

<div class="mb-4">
    <a href="patron_dashboard.php" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
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

<!-- Event & Graphics Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($graphics['event_title']); ?></h3>
            <p class="text-gray-600 mt-1">Graphics by <?php echo htmlspecialchars($graphics['gd_name']); ?> (<?php echo htmlspecialchars($graphics['gd_reg_id']); ?>)</p>
        </div>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
            Pending Graphics Approval
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Event Date</p>
            <p class="font-semibold text-gray-800"><?php echo date('F d, Y', strtotime($graphics['expected_date'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Venue</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($graphics['venue']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Design Category</p>
            <p class="font-semibold text-cause-purple"><?php echo ucfirst(str_replace('_', ' ', $graphics['design_category'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Submitted</p>
            <p class="font-semibold text-gray-800"><?php echo date('M d, Y', strtotime($graphics['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Event Description</p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($graphics['event_description'])); ?></p>
    </div>
    
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-1">Organized by</p>
        <p class="text-gray-800"><?php echo htmlspecialchars($graphics['student_name']); ?> (<?php echo htmlspecialchars($graphics['student_reg_id']); ?>)</p>
    </div>
</div>

<!-- Graphics Preview -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Graphics Preview</h3>
    
    <div class="text-center">
        <div class="inline-block border-2 border-gray-200 rounded-lg p-4 bg-gray-50">
            <a href="<?php echo htmlspecialchars($graphics['image_link']); ?>" target="_blank" class="block">
                <img src="<?php echo htmlspecialchars($graphics['image_link']); ?>" 
                     alt="Event Graphics Preview" 
                     class="max-w-full max-h-96 mx-auto rounded-lg shadow-sm"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display: none;" class="text-gray-500 py-8">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p>Image could not be loaded</p>
                    <p class="text-sm">Click to view original link</p>
                </div>
            </a>
        </div>
        
        <div class="mt-4">
            <a href="<?php echo htmlspecialchars($graphics['image_link']); ?>" target="_blank" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Open in New Tab
            </a>
        </div>
    </div>
</div>

<!-- Review Guidelines -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-blue-800 mb-3">Graphics Review Guidelines</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-700 text-sm">
        <div>
            <h4 class="font-medium mb-2">Design Quality Check:</h4>
            <ul class="space-y-1">
                <li>• High resolution and clear imagery</li>
                <li>• Proper CAUSE branding and colors</li>
                <li>• Event details are clearly visible</li>
                <li>• Professional and attractive design</li>
            </ul>
        </div>
        <div>
            <h4 class="font-medium mb-2">Content Verification:</h4>
            <ul class="space-y-1">
                <li>• Event title, date, and venue are correct</li>
                <li>• No spelling or grammatical errors</li>
                <li>• Appropriate imagery for the event type</li>
                <li>• Consistent with CAUSE guidelines</li>
            </ul>
        </div>
    </div>
</div>

<!-- Patron Decision Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Patron Graphics Review Decision</h3>
    
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback/Comments</label>
        <textarea name="feedback" rows="4" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                  placeholder="Provide feedback about the graphics quality, design, or any required changes..."></textarea>
        <p class="text-xs text-gray-500 mt-1">Optional for approval, Required for rejection</p>
    </div>
    
    <div class="flex justify-end space-x-4">
        <button type="submit" name="action" value="reject"
                onclick="return confirm('Reject this graphics design? The designer will need to create a new version.');"
                class="px-6 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition">
            Reject Graphics
        </button>
        <button type="submit" name="action" value="approve"
                onclick="return confirm('Approve this graphics design? It will be available for the event.');"
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Approve Graphics
        </button>
    </div>
</form>

<?php require_once 'includes/patron_footer.php'; ?>