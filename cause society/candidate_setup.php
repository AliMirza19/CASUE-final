<?php
// Candidate Setup - Epic E8 (Candidate Profile)
session_start();
require_once 'config/db.php';

$page_title = "Candidate Profile Setup";
require_once 'includes/student_header.php';

// Handle form submission - Create/Update candidate profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manifesto = trim($_POST['manifesto'] ?? '');
    $photo_url = trim($_POST['photo_url'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $vp_name = trim($_POST['vp_name'] ?? '');
    
    if (empty($manifesto) || empty($experience) || empty($vp_name)) {
        $_SESSION['error'] = "Please fill all required fields!";
    } else {
        try {
            // Check karo ke candidate profile pehle se exist karta hai
            $stmt = $pdo->prepare("SELECT id FROM candidate_profiles WHERE student_id = :student_id");
            $stmt->execute(['student_id' => $_SESSION['user_id']]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing profile
                $stmt = $pdo->prepare("UPDATE candidate_profiles 
                                       SET manifesto = :manifesto, photo_url = :photo_url, experience = :experience, 
                                           vp_name = :vp_name, status = 'pending_patron', updated_at = NOW()
                                       WHERE student_id = :student_id");
                $stmt->execute([
                    'manifesto' => $manifesto,
                    'photo_url' => $photo_url,
                    'experience' => $experience,
                    'vp_name' => $vp_name,
                    'student_id' => $_SESSION['user_id']
                ]);
                $_SESSION['success'] = "Candidate profile updated successfully! Sent for Patron approval.";
            } else {
                // Create new profile
                $stmt = $pdo->prepare("INSERT INTO candidate_profiles (student_id, manifesto, photo_url, experience, vp_name, status, created_at) 
                                       VALUES (:student_id, :manifesto, :photo_url, :experience, :vp_name, 'pending_patron', NOW())");
                $stmt->execute([
                    'student_id' => $_SESSION['user_id'],
                    'manifesto' => $manifesto,
                    'photo_url' => $photo_url,
                    'experience' => $experience,
                    'vp_name' => $vp_name
                ]);
                $_SESSION['success'] = "Candidate profile submitted successfully! Sent for Patron approval.";
            }
            
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error submitting candidate profile!";
        }
    }
}

// Existing candidate profile fetch karo
try {
    $stmt = $pdo->prepare("SELECT * FROM candidate_profiles WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $_SESSION['user_id']]);
    $existing_profile = $stmt->fetch();
    
} catch(PDOException $e) {
    $existing_profile = null;
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

<!-- Page Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Society Election - Candidate Profile</h3>
            <p class="text-gray-600 mt-1">Submit your candidacy for Society President position</p>
        </div>
    </div>
</div>

<!-- Current Status (if profile exists) -->
<?php if ($existing_profile): ?>
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Application Status</h3>
    
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-700">Your candidate profile has been submitted</p>
            <p class="text-sm text-gray-500">Last updated: <?php echo date('F d, Y g:i A', strtotime($existing_profile['updated_at'])); ?></p>
        </div>
        <div>
            <?php if ($existing_profile['status'] === 'pending_patron'): ?>
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                    Pending Patron Approval
                </span>
            <?php elseif ($existing_profile['status'] === 'approved'): ?>
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Approved for Election
                </span>
            <?php else: ?>
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Rejected
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($existing_profile['patron_feedback']): ?>
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-700 mb-1">Patron Feedback:</p>
            <p class="text-sm text-gray-600"><?php echo nl2br(htmlspecialchars($existing_profile['patron_feedback'])); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Candidate Profile Form -->
<form method="POST" class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">
        <?php echo $existing_profile ? 'Update Candidate Profile' : 'Submit Candidate Profile'; ?>
    </h3>
    
    <!-- Manifesto -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Manifesto / Vision Statement *</label>
        <textarea name="manifesto" rows="6" required 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                  placeholder="Describe your vision for the society, your goals, and how you plan to serve the student community..."><?php echo htmlspecialchars($existing_profile['manifesto'] ?? ''); ?></textarea>
        <p class="text-xs text-gray-500 mt-1">Minimum 100 characters. Be specific about your plans and commitments.</p>
    </div>
    
    <!-- Experience -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Experience & Achievements *</label>
        <textarea name="experience" rows="4" required 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                  placeholder="List your relevant experience, leadership roles, achievements, and qualifications..."><?php echo htmlspecialchars($existing_profile['experience'] ?? ''); ?></textarea>
        <p class="text-xs text-gray-500 mt-1">Include academic achievements, leadership experience, and extracurricular activities.</p>
    </div>
    
    <!-- Vice President Name -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Proposed Vice President Name *</label>
        <input type="text" name="vp_name" required 
               value="<?php echo htmlspecialchars($existing_profile['vp_name'] ?? ''); ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
               placeholder="Enter the full name of your proposed Vice President">
        <p class="text-xs text-gray-500 mt-1">Your running mate who will serve as Vice President if you win.</p>
    </div>
    
    <!-- Profile Photo URL -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo URL (Optional)</label>
        <input type="url" name="photo_url" 
               value="<?php echo htmlspecialchars($existing_profile['photo_url'] ?? ''); ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
               placeholder="https://example.com/your-photo.jpg">
        <p class="text-xs text-gray-500 mt-1">Upload your photo to Google Drive or any image hosting service and paste the public link here.</p>
    </div>
    
    <!-- Guidelines -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-blue-800 font-medium text-sm">Election Guidelines</p>
                <ul class="text-blue-700 text-sm mt-1 space-y-1">
                    <li>• Only approved candidates will be eligible for voting</li>
                    <li>• Patron will review all candidate profiles before approval</li>
                    <li>• Ensure all information is accurate and truthful</li>
                    <li>• Voting will be conducted online through the student portal</li>
                    <li>• Campaign activities must follow CAUSE guidelines</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" 
                class="px-8 py-3 bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold rounded-lg transition">
            <?php echo $existing_profile ? 'Update Profile' : 'Submit Candidacy'; ?>
        </button>
    </div>
</form>

<?php require_once 'includes/student_footer.php'; ?>