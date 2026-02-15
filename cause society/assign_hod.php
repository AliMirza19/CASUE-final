<?php
// Assign HOD - Admin page
session_start();
require_once 'config/db.php';

$page_title = "Assign HOD";
require_once 'includes/admin_header.php';

$search_result = null;
$searched = false;

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_user'])) {
    $reg_id = trim($_POST['reg_id'] ?? '');
    
    if (empty($reg_id)) {
        $_SESSION['error'] = "Please enter a Registration ID!";
    } else {
        try {
            // User ko search karo
            $stmt = $pdo->prepare("SELECT id, reg_id, name, email, role FROM users WHERE reg_id = :reg_id");
            $stmt->execute(['reg_id' => $reg_id]);
            $search_result = $stmt->fetch();
            $searched = true;
            
            if (!$search_result) {
                $_SESSION['error'] = "No user found with Registration ID: " . htmlspecialchars($reg_id);
            }
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error searching user!";
        }
    }
}

// Handle HOD appointment
if (isset($_GET['appoint']) && is_numeric($_GET['appoint'])) {
    $user_id = (int)$_GET['appoint'];
    
    try {
        // Pehle check karo ke user exist karta hai
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Pehle saare HODs ko unset karo (sirf ek HOD allowed)
            $pdo->exec("UPDATE users SET role = 'student' WHERE role = 'hod'");
            
            // Ab is user ko HOD banao
            $stmt = $pdo->prepare("UPDATE users SET role = 'hod' WHERE id = :id");
            $stmt->execute(['id' => $user_id]);
            
            $_SESSION['success'] = htmlspecialchars($user['name']) . " has been appointed as HOD!";
            header("Location: assign_hod.php");
            exit();
        } else {
            $_SESSION['error'] = "User not found!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error appointing HOD!";
    }
}
?>

<!-- Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Search User Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Search User by Registration ID</h3>
    
    <form method="POST" class="flex gap-4">
        <div class="flex-1">
            <input 
                type="text" 
                name="reg_id" 
                required 
                placeholder="Enter Registration ID (e.g., ADMIN-001)"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                value="<?php echo isset($_POST['reg_id']) ? htmlspecialchars($_POST['reg_id']) : ''; ?>"
            >
        </div>
        <button 
            type="submit" 
            name="search_user"
            class="bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-8 rounded-lg transition"
        >
            Search
        </button>
    </form>
</div>

<!-- Search Result -->
<?php if ($searched && $search_result): ?>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">User Details</h3>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Registration ID</p>
                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($search_result['reg_id']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Name</p>
                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($search_result['name']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Email</p>
                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($search_result['email']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Current Role</p>
                    <p class="text-lg font-semibold">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            <?php 
                            echo $search_result['role'] === 'hod' ? 'bg-blue-100 text-blue-800' : 
                                 ($search_result['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                 'bg-gray-100 text-gray-800'); 
                            ?>">
                            <?php echo strtoupper($search_result['role']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Appoint Button -->
        <?php if ($search_result['role'] !== 'hod'): ?>
            <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div>
                    <p class="font-medium text-gray-800">Appoint as Head of Department</p>
                    <p class="text-sm text-gray-600 mt-1">This will change the user's role to HOD. Any existing HOD will be demoted to Student.</p>
                </div>
                <a 
                    href="?appoint=<?php echo $search_result['id']; ?>" 
                    onclick="return confirm('Are you sure you want to appoint <?php echo htmlspecialchars($search_result['name']); ?> as HOD?');"
                    class="bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-6 rounded-lg transition whitespace-nowrap"
                >
                    Appoint as HOD
                </a>
            </div>
        <?php else: ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-800 font-medium">✓ This user is already appointed as HOD</p>
            </div>
        <?php endif; ?>
    </div>
<?php elseif ($searched && !$search_result): ?>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-8">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-600">No user found with the provided Registration ID</p>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
