<?php
// Patron Review Candidate - Epic E8
session_start();
require_once 'config/db.php';

// Candidate ID check karo
$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$candidate_id) {
    $_SESSION['error'] = "Invalid candidate ID!";
    header("Location: patron_dashboard.php");
    exit();
}

// Handle form submission - Approve or reject candidate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');
    
    if ($action === 'approve') {
        try {
            $stmt = $pdo->prepare("UPDATE candidate_profiles SET status = 'approved', patron_feedback = :feedback WHERE id = :id AND status = 'pending_patron'");
            $stmt->execute(['feedback' => $feedback, 'id' => $candidate_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Candidate approved for election!";
                header("Location: patron_dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Candidate not found or already processed!";
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error approving candidate!";
        }
    } elseif ($action === 'reject') {
        if (empty($feedback)) {
            $_SESSION['error'] = "Please provide feedback for rejection!";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE candidate_profiles SET status = 'rejected', patron_feedback = :feedback WHERE id = :id AND status = 'pending_patron'");
                $stmt->execute(['feedback' => $feedback, 'id' => $candidate_id]);
                
                $_SESSION['success'] = "Candidate rejected with feedback!";
                header("Location: patron_dashboard.php");
                exit();
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error rejecting candidate!";
            }
        }
    }
}

$page_title = "Review Election Candidate";
require_once 'includes/patron_header.php';

// Candidate details fetch karo
try {
    $stmt = $pdo->prepare("SELECT cp.*, u.name as student_name, u.reg_id as student_reg_id, u.email as student_email
                           FROM candidate_profiles cp
                           JOIN users u ON cp.student_id = u.id
                           WHERE cp.id = :id AND cp.status = 'pending_patron'");
    $stmt->execute(['id' => $candidate_id]);
    $candidate = $stmt->fetch();
    
    if (!$candidate) {
        $_SESSION['error'] = "Candidate not found or not available for review!";
        header("Location: patron_dashboard.php");
        exit();
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching candidate details!";
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

<!-- Candidate Profile Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-center">
            <?php if ($candidate['photo_url']): ?>
                <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" 
                     alt="Candidate Photo" 
                     class="w-20 h-20 rounded-full object-cover mr-6"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none;" class="w-20 h-20 bg-gray-200 rounded-full mr-6 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            <?php else: ?>
                <div class="w-20 h-20 bg-gray-200 rounded-full mr-6 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            <?php endif; ?>
            
            <div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($candidate['student_name']); ?></h3>
                <p class="text-gray-600"><?php echo htmlspecialchars($candidate['student_reg_id']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($candidate['student_email']); ?></p>
                <p class="text-sm text-gray-500 mt-1">Candidate for Society President</p>
            </div>
        </div>
        
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
            Pending Approval
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-1">Proposed Vice President</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($candidate['vp_name']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-1">Application Submitted</p>
            <p class="font-semibold text-gray-800"><?php echo date('F d, Y g:i A', strtotime($candidate['created_at'])); ?></p>
        </div>
    </div>
</div>

<!-- Manifesto Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Manifesto & Vision Statement</h3>
    <div class="prose max-w-none">
        <p class="text-gray-700 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($candidate['manifesto']); ?></p>
    </div>
</div>

<!-- Experience Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Experience & Achievements</h3>
    <div class="prose max-w-none">
        <p class="text-gray-700 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($candidate['experience']); ?></p>
    </div>
</div>

<!-- Review Guidelines -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-blue-800 mb-3">Candidate Review Guidelines</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-700 text-sm">
        <div>
            <h4 class="font-medium mb-2">Eligibility Criteria:</h4>
            <ul class="space-y-1">
                <li>• Student must be in good academic standing</li>
                <li>• No disciplinary actions in the past year</li>
                <li>• Demonstrated leadership experience</li>
                <li>• Clear and achievable manifesto</li>
            </ul>
        </div>
        <div>
            <h4 class="font-medium mb-2">Assessment Points:</h4>
            <ul class="space-y-1">
                <li>• Quality and feasibility of proposed plans</li>
                <li>• Relevant experience and achievements</li>
                <li>• Communication skills and presentation</li>
                <li>• Commitment to student welfare</li>
            </ul>
        </div>
    </div>
</div>

<!-- Patron Decision Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Patron Review Decision</h3>
    
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback/Comments</label>
        <textarea name="feedback" rows="4" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                  placeholder="Provide feedback about the candidate's qualifications, manifesto, or any concerns..."></textarea>
        <p class="text-xs text-gray-500 mt-1">Optional for approval, Required for rejection</p>
    </div>
    
    <div class="flex justify-end space-x-4">
        <button type="submit" name="action" value="reject"
                onclick="return confirm('Reject this candidate? They will not be eligible for the election.');"
                class="px-6 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition">
            Reject Candidate
        </button>
        <button type="submit" name="action" value="approve"
                onclick="return confirm('Approve this candidate for the election? They will be eligible for voting.');"
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Approve for Election
        </button>
    </div>
</form>

<?php require_once 'includes/patron_footer.php'; ?>