<?php
// Student View Graphics
session_start();
require_once 'config/db.php';

// Event ID check karo
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    $_SESSION['error'] = "Invalid event ID!";
    header("Location: my_events.php");
    exit();
}

$page_title = "Event Graphics";
require_once 'includes/student_header.php';

// Event aur graphics fetch karo
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
    
    // Graphics fetch karo
    $stmt = $pdo->prepare("SELECT eg.*, gd.name as gd_name, gd.reg_id as gd_reg_id
                           FROM event_graphics eg
                           JOIN users gd ON eg.gd_id = gd.id
                           WHERE eg.event_id = :event_id
                           ORDER BY eg.created_at DESC");
    $stmt->execute(['event_id' => $event_id]);
    $graphics = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching graphics!";
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
            <p class="text-gray-600 mt-1">Event Graphics & Design Materials</p>
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
            <p class="text-sm text-gray-600 mb-1">Graphics Available</p>
            <p class="text-lg font-bold text-cause-purple"><?php echo count($graphics); ?> Designs</p>
        </div>
    </div>
</div>

<!-- Graphics Gallery -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Event Graphics & Designs</h3>
        <p class="text-gray-600 text-sm mt-1">Graphics created by our design team for your event</p>
    </div>
    
    <?php if (empty($graphics)): ?>
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 mb-2">No graphics available yet</p>
                <p class="text-gray-400 text-sm">Graphics will be created by our design team and appear here once approved</p>
            </div>
        </div>
    <?php else: ?>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($graphics as $graphic): ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Graphics Preview -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                            <a href="<?php echo htmlspecialchars($graphic['image_link']); ?>" target="_blank" class="block">
                                <img src="<?php echo htmlspecialchars($graphic['image_link']); ?>" 
                                     alt="<?php echo ucfirst(str_replace('_', ' ', $graphic['design_category'])); ?>" 
                                     class="w-full h-48 object-cover hover:opacity-90 transition"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none;" class="w-full h-48 flex items-center justify-center bg-gray-100 text-gray-500">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm">Click to view</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Graphics Info -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-800">
                                    <?php echo ucfirst(str_replace('_', ' ', $graphic['design_category'])); ?>
                                </h4>
                                <span class="px-2 py-1 text-xs rounded-full <?php 
                                    echo $graphic['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                        ($graphic['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'); 
                                ?>">
                                    <?php echo ucfirst($graphic['status']); ?>
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-2">
                                Created by <?php echo htmlspecialchars($graphic['gd_name']); ?>
                            </p>
                            
                            <p class="text-xs text-gray-500 mb-3">
                                <?php echo date('M d, Y', strtotime($graphic['created_at'])); ?>
                            </p>
                            
                            <?php if ($graphic['patron_feedback']): ?>
                                <div class="bg-gray-50 rounded p-2 mb-3">
                                    <p class="text-xs text-gray-600 font-medium mb-1">Feedback:</p>
                                    <p class="text-xs text-gray-700"><?php echo htmlspecialchars($graphic['patron_feedback']); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex space-x-2">
                                <a href="<?php echo htmlspecialchars($graphic['image_link']); ?>" target="_blank" 
                                   class="flex-1 text-center px-3 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 transition">
                                    View Full Size
                                </a>
                                <?php if ($graphic['status'] === 'approved'): ?>
                                    <a href="<?php echo htmlspecialchars($graphic['image_link']); ?>" download 
                                       class="flex-1 text-center px-3 py-2 bg-cause-purple text-white rounded text-sm hover:bg-cause-purple-dark transition">
                                        Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/student_footer.php'; ?>