<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - CAUSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
<body class="bg-gradient-to-br from-red-50 to-red-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full text-center">
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="inline-block bg-red-500 text-white rounded-full p-6 mb-4">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Error Message -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-6xl font-bold text-red-500 mb-4">403</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Access Denied</h2>
            <p class="text-gray-600 mb-6">
                Maazrat! Aapke paas is page ki ijazat nahi hai. 
                Ye section sirf authorized users ke liye hai.
            </p>
            
            <!-- User Info (if logged in) -->
            <?php 
            session_start();
            if (isset($_SESSION['name']) && isset($_SESSION['role'])): 
            ?>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600">Logged in as:</p>
                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['name']); ?></p>
                <p class="text-sm text-gray-500">Role: <?php echo strtoupper($_SESSION['role']); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="javascript:history.back()" 
                   class="block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                    Go Back
                </a>
                
                <?php if (isset($_SESSION['role'])): ?>
                    <?php
                    // Redirect to appropriate dashboard based on role
                    $dashboard_map = [
                        'admin' => 'admin_dashboard.php',
                        'hod' => 'hod_dashboard.php',
                        'student' => 'student_dashboard.php',
                        'patron' => 'patron_dashboard.php',
                        'president' => 'president_dashboard.php',
                        'sa' => 'sa_dashboard.php',
                        'gd' => 'gd_dashboard.php',
                        'vc' => 'vc_dashboard.php'
                    ];
                    $dashboard = $dashboard_map[$_SESSION['role']] ?? 'index.php';
                    ?>
                    <a href="<?php echo $dashboard; ?>" 
                       class="block w-full border border-cause-purple text-cause-purple hover:bg-cause-purple hover:text-white font-semibold py-3 px-6 rounded-lg transition">
                        My Dashboard
                    </a>
                <?php else: ?>
                    <a href="index.php" 
                       class="block w-full border border-cause-purple text-cause-purple hover:bg-cause-purple hover:text-white font-semibold py-3 px-6 rounded-lg transition">
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm">
                © 2024 CAUSE Smart Society Management System
            </p>
        </div>
    </div>
    
</body>
</html>