<?php
// Patron Dashboard - Main page
session_start();
require_once 'config/db.php';

$page_title = "Pending Reviews";
require_once 'includes/patron_header.php';

// Pending events fetch karo (jo president se aaye hain)
try {
    $stmt = $pdo->prepare("SELECT e.*, u.name as student_name, u.reg_id as student_reg_id,
                           (SELECT COUNT(*) FROM event_items WHERE event_id = e.id) as item_count
                           FROM events e 
                           JOIN users u ON e.student_id = u.id 
                           WHERE e.status = 'pending_patron' AND e.term_id = :term_id
                           ORDER BY e.created_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_events = $stmt->fetchAll();
    
    // Pending graphics fetch karo (Epic E7 - Graphics Review)
    $stmt = $pdo->prepare("SELECT eg.*, e.title as event_title, u.name as student_name, u.reg_id as student_reg_id,
                           gd.name as gd_name, gd.reg_id as gd_reg_id
                           FROM event_graphics eg
                           JOIN events e ON eg.event_id = e.id
                           JOIN users u ON e.student_id = u.id
                           JOIN users gd ON eg.gd_id = gd.id
                           WHERE eg.status = 'pending_patron' AND e.term_id = :term_id
                           ORDER BY eg.created_at ASC");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $pending_graphics = $stmt->fetchAll();
    
    // Pending candidate profiles fetch karo (Epic E8 - Election Management)
    $stmt = $pdo->prepare("SELECT cp.*, u.name as student_name, u.reg_id as student_reg_id, u.email as student_email
                           FROM candidate_profiles cp
                           JOIN users u ON cp.student_id = u.id
                           WHERE cp.status = 'pending_patron'
                           ORDER BY cp.created_at ASC");
    $stmt->execute();
    $pending_candidates = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $pending_events = [];
    $pending_graphics = [];
    $pending_candidates = [];
    $_SESSION['error'] = "Error fetching pending events!";
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

<!-- Pending Events Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Events Pending Patron Review</h3>
        <p class="text-gray-600 text-sm mt-1">Review budget details and approve/modify items before forwarding to HOD</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pending_events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500">No events pending review</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_events as $event): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="text-sm text-gray-500 mt-1"><?php echo $event['item_count']; ?> budget items</div>
                                <div class="text-sm text-gray-500"><?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($event['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['student_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800"><?php echo date('M d, Y', strtotime($event['expected_date'])); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($event['venue']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">PKR <?php echo number_format($event['grand_total'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Pending Review
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="patron_review_event.php?id=<?php echo $event['id']; ?>" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    Detail Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Graphics Pending Approval Section (Epic E7) -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Graphics Pending Approval</h3>
        <p class="text-gray-600 text-sm mt-1">Review and approve graphics created by Graphic Designer</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event & Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designer</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Design Preview</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pending_graphics)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">No graphics pending approval</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_graphics as $graphic): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($graphic['event_title']); ?></div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                        <?php echo ucfirst(str_replace('_', ' ', $graphic['design_category'])); ?>
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($graphic['student_name']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($graphic['gd_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($graphic['gd_reg_id']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?php echo htmlspecialchars($graphic['image_link']); ?>" target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Design
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($graphic['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="patron_review_graphics.php?id=<?php echo $graphic['id']; ?>" 
                                   class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    Review Graphics
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Candidate Profiles Pending Approval Section (Epic E8) -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Election Candidates Pending Approval</h3>
        <p class="text-gray-600 text-sm mt-1">Review and approve candidate profiles for society elections</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Candidate Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proposed VP</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Profile Photo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pending_candidates)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-gray-500">No candidate profiles pending approval</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_candidates as $candidate): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($candidate['student_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidate['student_reg_id']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($candidate['student_email']); ?></div>
                                <div class="text-sm text-gray-600 mt-2">
                                    <strong>Manifesto:</strong> <?php echo substr(htmlspecialchars($candidate['manifesto']), 0, 100); ?>...
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($candidate['vp_name']); ?></div>
                                <div class="text-sm text-gray-500">Proposed Vice President</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($candidate['photo_url']): ?>
                                    <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" 
                                         alt="Candidate Photo" 
                                         class="w-12 h-12 rounded-full mx-auto object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div style="display: none;" class="w-12 h-12 bg-gray-200 rounded-full mx-auto flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($candidate['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="patron_review_candidate.php?id=<?php echo $candidate['id']; ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    Review Candidate
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Election Management Section -->
<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Election Management</h3>
    
    <?php
    // Check current election status
    $stmt = $pdo->prepare("SELECT * FROM election_settings WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $election_settings = $stmt->fetch();
    
    // Count approved candidates
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM candidate_profiles WHERE status = 'approved'");
    $approved_candidates_count = $stmt->fetch()['count'];
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Approved Candidates</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $approved_candidates_count; ?></p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Voting Status</p>
            <p class="text-lg font-semibold <?php echo ($election_settings && $election_settings['voting_enabled']) ? 'text-green-600' : 'text-red-600'; ?>">
                <?php echo ($election_settings && $election_settings['voting_enabled']) ? 'Active' : 'Inactive'; ?>
            </p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Election Period</p>
            <?php if ($election_settings): ?>
                <p class="text-sm text-gray-800">
                    <?php echo date('M d', strtotime($election_settings['voting_start_date'])); ?> - 
                    <?php echo date('M d, Y', strtotime($election_settings['voting_end_date'])); ?>
                </p>
            <?php else: ?>
                <p class="text-sm text-gray-500">Not set</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="flex space-x-4">
        <a href="patron_election_control.php" 
           class="px-6 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            Manage Elections
        </a>
        <a href="patron_election_results.php" 
           class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition">
            View Results
        </a>
    </div>
</div>

<?php require_once 'includes/patron_footer.php'; ?>
