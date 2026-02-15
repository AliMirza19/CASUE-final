<?php
// Voting Portal - Election System
session_start();
require_once 'config/db.php';

$page_title = "Society Elections - Vote Now";
require_once 'includes/student_header.php';

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = intval($_POST['candidate_id'] ?? 0);
    
    if ($candidate_id <= 0) {
        $_SESSION['error'] = "Please select a candidate to vote for!";
    } else {
        try {
            // Check karo ke student ne pehle vote kiya hai ya nahi
            $stmt = $pdo->prepare("SELECT id FROM votes WHERE student_id = :student_id AND term_id = :term_id");
            $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
            $existing_vote = $stmt->fetch();
            
            if ($existing_vote) {
                $_SESSION['error'] = "You have already voted in this election!";
            } else {
                // Vote record karo
                $stmt = $pdo->prepare("INSERT INTO votes (student_id, candidate_id, term_id, voted_at) 
                                       VALUES (:student_id, :candidate_id, :term_id, NOW())");
                $stmt->execute([
                    'student_id' => $_SESSION['user_id'],
                    'candidate_id' => $candidate_id,
                    'term_id' => $_SESSION['term_id']
                ]);
                
                $_SESSION['success'] = "Your vote has been recorded successfully! Thank you for participating in the election.";
                header("Location: voting_portal.php");
                exit();
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error recording your vote!";
        }
    }
}

// Check voting status and fetch data
try {
    // Election settings check karo
    $stmt = $pdo->prepare("SELECT * FROM election_settings WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $election_settings = $stmt->fetch();
    
    $voting_enabled = $election_settings && $election_settings['voting_enabled'];
    $current_time = date('Y-m-d H:i:s');
    $voting_period_active = false;
    
    if ($election_settings) {
        $voting_period_active = ($current_time >= $election_settings['voting_start_date'] && 
                                $current_time <= $election_settings['voting_end_date']);
    }
    
    // Check karo ke student ne vote kiya hai
    $stmt = $pdo->prepare("SELECT v.*, cp.student_id as voted_candidate_student_id, u.name as voted_candidate_name
                           FROM votes v
                           JOIN candidate_profiles cp ON v.candidate_id = cp.id
                           JOIN users u ON cp.student_id = u.id
                           WHERE v.student_id = :student_id AND v.term_id = :term_id");
    $stmt->execute(['student_id' => $_SESSION['user_id'], 'term_id' => $_SESSION['term_id']]);
    $user_vote = $stmt->fetch();
    
    // Approved candidates fetch karo
    $stmt = $pdo->prepare("SELECT cp.*, u.name as student_name, u.reg_id as student_reg_id
                           FROM candidate_profiles cp
                           JOIN users u ON cp.student_id = u.id
                           WHERE cp.status = 'approved'
                           ORDER BY u.name");
    $stmt->execute();
    $candidates = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $voting_enabled = false;
    $voting_period_active = false;
    $user_vote = null;
    $candidates = [];
    $_SESSION['error'] = "Error loading election data!";
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

<!-- Election Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">CAUSE Society Elections</h3>
            <p class="text-gray-600 mt-1">Vote for your Society President</p>
        </div>
    </div>
</div>

<!-- Voting Status -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Election Status</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Voting Status</p>
            <p class="text-lg font-semibold <?php echo ($voting_enabled && $voting_period_active) ? 'text-green-600' : 'text-red-600'; ?>">
                <?php 
                if (!$voting_enabled) {
                    echo 'Not Started';
                } elseif (!$voting_period_active) {
                    echo 'Closed';
                } else {
                    echo 'Active';
                }
                ?>
            </p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Approved Candidates</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo count($candidates); ?></p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Your Vote Status</p>
            <p class="text-lg font-semibold <?php echo $user_vote ? 'text-green-600' : 'text-orange-600'; ?>">
                <?php echo $user_vote ? 'Voted' : 'Not Voted'; ?>
            </p>
        </div>
    </div>
    
    <?php if ($election_settings): ?>
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-blue-800 text-sm">
                <strong>Voting Period:</strong> 
                <?php echo date('F d, Y g:i A', strtotime($election_settings['voting_start_date'])); ?> - 
                <?php echo date('F d, Y g:i A', strtotime($election_settings['voting_end_date'])); ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<!-- User's Vote (if already voted) -->
<?php if ($user_vote): ?>
<div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
    <div class="flex items-center">
        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-green-800 font-medium">You have successfully voted!</p>
            <p class="text-green-700 text-sm">
                You voted for <strong><?php echo htmlspecialchars($user_vote['voted_candidate_name']); ?></strong> 
                on <?php echo date('F d, Y g:i A', strtotime($user_vote['voted_at'])); ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Voting Form or Candidates Display -->
<?php if (!$voting_enabled || !$voting_period_active): ?>
    <!-- Voting Not Active -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-2">Voting is currently not active</p>
            <p class="text-gray-400">Please check back during the voting period</p>
        </div>
    </div>
<?php elseif ($user_vote): ?>
    <!-- Already Voted - Show Candidates (Read-only) -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Election Candidates</h3>
            <p class="text-gray-600 text-sm mt-1">You have already voted in this election</p>
        </div>
        
        <?php if (empty($candidates)): ?>
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No approved candidates found</p>
            </div>
        <?php else: ?>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="border border-gray-200 rounded-lg p-6 <?php echo ($user_vote && $user_vote['candidate_id'] == $candidate['id']) ? 'bg-green-50 border-green-300' : ''; ?>">
                            <div class="flex items-start">
                                <?php if ($candidate['photo_url']): ?>
                                    <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" 
                                         alt="Candidate Photo" 
                                         class="w-16 h-16 rounded-full object-cover mr-4"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none;" class="w-16 h-16 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($candidate['student_name']); ?></h4>
                                        <?php if ($user_vote && $user_vote['candidate_id'] == $candidate['id']): ?>
                                            <span class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">Your Vote</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($candidate['student_reg_id']); ?></p>
                                    <p class="text-gray-600 text-sm mb-3"><strong>VP:</strong> <?php echo htmlspecialchars($candidate['vp_name']); ?></p>
                                    <p class="text-gray-700 text-sm"><?php echo substr(htmlspecialchars($candidate['manifesto']), 0, 200); ?>...</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Active Voting -->
    <form method="POST" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Cast Your Vote</h3>
            <p class="text-gray-600 text-sm mt-1">Select one candidate to vote for Society President</p>
        </div>
        
        <?php if (empty($candidates)): ?>
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No approved candidates available for voting</p>
            </div>
        <?php else: ?>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <?php foreach ($candidates as $candidate): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" 
                                   class="sr-only peer" required>
                            <div class="border-2 border-gray-200 rounded-lg p-6 peer-checked:border-cause-purple peer-checked:bg-purple-50 hover:border-gray-300 transition">
                                <div class="flex items-start">
                                    <?php if ($candidate['photo_url']): ?>
                                        <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" 
                                             alt="Candidate Photo" 
                                             class="w-16 h-16 rounded-full object-cover mr-4"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none;" class="w-16 h-16 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded-full mr-4 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($candidate['student_name']); ?></h4>
                                        <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($candidate['student_reg_id']); ?></p>
                                        <p class="text-gray-600 text-sm mb-3"><strong>VP:</strong> <?php echo htmlspecialchars($candidate['vp_name']); ?></p>
                                        <p class="text-gray-700 text-sm mb-3"><?php echo substr(htmlspecialchars($candidate['manifesto']), 0, 150); ?>...</p>
                                        
                                        <div class="flex items-center text-cause-purple text-sm font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Select this candidate
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-center">
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to vote for the selected candidate? This action cannot be undone.');"
                                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
                            Cast My Vote
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </form>
<?php endif; ?>

<?php require_once 'includes/student_footer.php'; ?>