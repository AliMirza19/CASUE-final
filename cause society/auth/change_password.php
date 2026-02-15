<?php
// Password change page - First time login ke baad
session_start();

// Check karo ke user logged in hai
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Agar password already changed hai to dashboard par redirect karo
if ($_SESSION['password_changed'] == 1) {
    header("Location: ../" . $_SESSION['dashboard']);
    exit();
}

require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - CAUSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cause-purple': '#7C3AED',
                        'cause-purple-dark': '#5B21B6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-purple-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-block bg-yellow-100 text-yellow-600 rounded-full p-3 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Change Password</h2>
                <p class="text-gray-600 text-sm">You must change your password on first login</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">';
                echo htmlspecialchars($_SESSION['error']);
                echo '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form action="change_password_process.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        required 
                        minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                        placeholder="Enter current password"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        required 
                        minlength="6" 
                        maxlength="30"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                        placeholder="Enter new password (6-30 characters)"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        required 
                        minlength="6" 
                        maxlength="30"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                        placeholder="Confirm new password"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    Update Password
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="logout.php" class="text-sm text-gray-600 hover:text-cause-purple">Logout</a>
            </div>
        </div>
    </div>

</body>
</html>
