<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CAUSE Smart Society')</title>
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
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-purple-50 to-purple-100 min-h-screen">
    
    @yield('content')

    @stack('scripts')
</body>
</html>