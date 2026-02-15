<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - CAUSE</title>
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
<body class="bg-gradient-to-br from-purple-50 to-purple-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full text-center">
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="inline-block bg-cause-purple text-white rounded-full p-6 mb-4">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Error Message -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-6xl font-bold text-cause-purple mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
            <p class="text-gray-600 mb-6">
                Maazrat! Aap jo page dhund rahe hain woh mojood nahi hai. 
                Ho sakta hai link galat ho ya page move kar diya gaya ho.
            </p>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="javascript:history.back()" 
                   class="block w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-6 rounded-lg transition">
                    Go Back
                </a>
                <a href="index.php" 
                   class="block w-full border border-cause-purple text-cause-purple hover:bg-cause-purple hover:text-white font-semibold py-3 px-6 rounded-lg transition">
                    Home Page
                </a>
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