<?php
// Student Profile page
session_start();
require_once 'config/db.php';

$page_title = "My Profile";
require_once 'includes/student_header.php';

// User info fetch karo
try {
    $stmt = $pdo->prepare("SELECT u.*, t.term_name FROM users u 
                           LEFT JOIN academic_terms t ON u.current_term_id = t.id 
                           WHERE u.id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    $user = null;
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <div class="bg-cause-purple rounded-full p-4 mr-4">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($user['reg_id']); ?></p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Email Address</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Role</p>
            <p class="font-semibold text-gray-800"><?php echo ucfirst($user['role']); ?></p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Current Term</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['term_name'] ?? 'Not Assigned'); ?></p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600 mb-1">Member Since</p>
            <p class="font-semibold text-gray-800"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="mt-6 pt-6 border-t border-gray-200">
        <a href="auth/change_password.php" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            Change Password
        </a>
    </div>
</div>

<?php require_once 'includes/student_footer.php'; ?>
