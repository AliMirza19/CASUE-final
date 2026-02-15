<?php
// Common header file for all dashboard pages
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CAUSE Smart Society'; ?></title>
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
    <!-- Navigation Bar -->
    <nav class="bg-cause-purple shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-white text-xl font-bold">CAUSE Smart Society</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-white text-sm">
                        <?php echo htmlspecialchars($_SESSION['name']); ?> 
                        <span class="text-purple-200">(<?php echo strtoupper($_SESSION['role']); ?>)</span>
                    </span>
                    <a href="auth/logout.php" class="bg-white text-cause-purple px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-50 transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
