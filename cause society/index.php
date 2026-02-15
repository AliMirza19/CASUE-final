<?php
// Login page - CAUSE Smart Society Management System
session_start();

// Agar user already logged in hai to dashboard par redirect karo
if (isset($_SESSION['user_id']) && isset($_SESSION['dashboard'])) {
    header("Location: " . $_SESSION['dashboard']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAUSE Smart Society - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Purple theme customize karo
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cause-purple': '#7C3AED',
                        'cause-purple-dark': '#5B21B6',
                        'cause-purple-light': '#A78BFA',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-purple-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo/Header Section -->
        <div class="text-center mb-8">
            <div class="inline-block mb-4">
                <img src="https://play-lh.googleusercontent.com/8QQUZDWOC8RpSqVsw2apjdnLiHvyLc1vJBpOC0MNQcE3_-JHv3XtW1K5m6YmVA6I-A" 
                     alt="CAUSE Logo" 
                     class="w-20 h-20 mx-auto rounded-full shadow-lg border-4 border-white"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <!-- Fallback icon if image fails to load -->
                <div class="hidden bg-cause-purple text-white rounded-full p-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">CAUSE Smart Society</h1>
            <p class="text-gray-600">Management System</p>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login</h2>
            
            <?php
            // Error message display karo agar session mein ho
            if (isset($_SESSION['error'])) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">';
                echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['error']) . '</span>';
                echo '</div>';
                unset($_SESSION['error']);
            }
            
            // Success message display karo
            if (isset($_SESSION['success'])) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">';
                echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['success']) . '</span>';
                echo '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="auth/login_process.php" method="POST" class="space-y-6">
                <!-- Registration ID Field -->
                <div>
                    <label for="reg_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Registration ID
                    </label>
                    <input 
                        type="text" 
                        id="reg_id" 
                        name="reg_id" 
                        required 
                        minlength="6" 
                        maxlength="12"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent transition duration-200"
                        placeholder="Enter your Registration ID"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="6" 
                        maxlength="30"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent transition duration-200"
                        placeholder="Enter your password"
                    >
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-cause-purple focus:ring-offset-2"
                >
                    Login
                </button>
            </form>

            <!-- Footer Text -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>CAUSE Smart Society Management System</p>
                <p class="mt-1">© 2024 All Rights Reserved</p>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>For login issues, please contact the administrator</p>
        </div>
    </div>

</body>
</html>
