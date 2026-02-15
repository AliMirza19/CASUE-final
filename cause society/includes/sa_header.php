<?php
// Student Affairs header with sidebar
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check karo ke user SA hai ya nahi
if ($_SESSION['role'] !== 'sa') {
    $_SESSION['error'] = "Access denied! Student Affairs privileges required.";
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Student Affairs'; ?> - CAUSE</title>
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
                    <h1 class="text-white text-xl font-bold">Student Affairs</h1>
                    <p class="text-purple-200 text-sm mt-1">Final Approvals</p>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-2">
                    <a href="sa_dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'sa_dashboard.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Final Approvals
                    </a>
                    
                    <a href="sa_approved_events.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'sa_approved_events.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approved Events History
                    </a>
                    
                    <a href="sa_society_team.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'sa_society_team.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Society Team
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
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $page_title ?? 'Student Affairs'; ?></h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">