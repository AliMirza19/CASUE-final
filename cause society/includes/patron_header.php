<?php
// Patron header with sidebar - Har patron page par include karein
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check karo ke user patron hai ya nahi
if ($_SESSION['role'] !== 'patron') {
    $_SESSION['error'] = "Access denied! Patron privileges required.";
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Patron Portal'; ?> - CAUSE</title>
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
                    <h1 class="text-white text-xl font-bold">CAUSE Patron</h1>
                    <p class="text-purple-200 text-sm mt-1">Budget Review</p>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-2">
                    <a href="patron_dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'patron_dashboard.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Pending Reviews
                    </a>
                    
                    <a href="patron_reviewed.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'patron_reviewed.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Reviewed Events
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
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $page_title ?? 'Patron Portal'; ?></h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">