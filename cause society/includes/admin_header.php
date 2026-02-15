<?php
// Admin header with sidebar - Har admin page par include karein
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check karo ke user admin hai ya nahi
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied! Admin privileges required.";
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard - CAUSE'; ?></title>
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
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-cause-purple shadow-lg flex-shrink-0">
            <div class="h-full flex flex-col">
                <!-- Logo/Header -->
                <div class="p-6 border-b border-purple-600">
                    <h1 class="text-white text-xl font-bold">CAUSE Admin</h1>
                    <p class="text-purple-200 text-sm mt-1">Management System</p>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-2">
                    <a href="admin_dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="manage_terms.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'manage_terms.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Manage Terms
                    </a>
                    
                    <a href="assign_hod.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'assign_hod.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Assign HOD
                    </a>
                    
                    <a href="admin_bulk_upload.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_bulk_upload.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Bulk Upload Users
                    </a>
                </nav>
                
                <!-- User Info & Logout -->
                <div class="p-4 border-t border-purple-600">
                    <div class="mb-3 px-4">
                        <p class="text-white text-sm font-medium"><?php echo htmlspecialchars($_SESSION['name']); ?></p>
                        <p class="text-purple-200 text-xs"><?php echo htmlspecialchars($_SESSION['reg_id']); ?></p>
                    </div>
                    <a href="auth/logout.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $page_title ?? 'Admin Dashboard'; ?></h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
