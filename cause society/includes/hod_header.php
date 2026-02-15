<?php
// HOD header with sidebar - Har HOD page par include karein
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check karo ke user HOD hai ya nahi
if ($_SESSION['role'] !== 'hod') {
    $_SESSION['error'] = "Access denied! HOD privileges required.";
    header("Location: index.php");
    exit();
}

// Budget lock status check karo
$budget_locked = false;
try {
    $stmt = $pdo->prepare("SELECT is_locked FROM budgets WHERE term_id = :term_id");
    $stmt->execute(['term_id' => $_SESSION['term_id']]);
    $budget = $stmt->fetch();
    $budget_locked = $budget && $budget['is_locked'] == 1;
} catch(PDOException $e) {
    $budget_locked = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'HOD Dashboard'; ?> - CAUSE</title>
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
                    <h1 class="text-white text-xl font-bold">CAUSE HOD</h1>
                    <p class="text-purple-200 text-sm mt-1">Management System</p>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-2">
                    <a href="hod_dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'hod_dashboard.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="assign_patron.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'assign_patron.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Assign Patron
                    </a>
                    
                    <a href="manage_budget.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'manage_budget.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Set Term Budget
                    </a>
                    
                    <!-- Event Approvals - Sirf tab enable jab budget locked ho -->
                    <?php if ($budget_locked): ?>
                        <a href="hod_event_approvals.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'hod_event_approvals.php' ? 'bg-purple-600' : ''; ?>">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Event Approvals
                        </a>
                    <?php else: ?>
                        <div class="flex items-center px-4 py-3 text-purple-300 cursor-not-allowed rounded-lg" title="Set budget first to unlock">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Event Approvals
                            <span class="ml-2 text-xs bg-purple-800 px-2 py-1 rounded">Locked</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Financial Analytics (Epic E10) -->
                    <a href="hod_analytics.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition <?php echo basename($_SERVER['PHP_SELF']) == 'hod_analytics.php' ? 'bg-purple-600' : ''; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Financial Analytics
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
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo $page_title ?? 'HOD Dashboard'; ?></h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
