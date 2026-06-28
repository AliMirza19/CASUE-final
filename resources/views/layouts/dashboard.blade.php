<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CAUSE Smart Society')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        .sidebar-link.active {
            background-color: rgba(124, 58, 237, 0.1);
            border-left: 3px solid #7C3AED;
        }

        /* ── CAUSE-AI Chatbot Widget ── */
        #cause-ai-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            font-family: 'Inter', sans-serif;
        }

        #cause-ai-toggle-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(124,58,237,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }
        #cause-ai-toggle-btn:hover {
            transform: scale(1.07);
            box-shadow: 0 12px 30px rgba(124,58,237,0.55);
        }
        #cause-ai-toggle-btn .pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(124,58,237,0.25);
            animation: cause-pulse 2s ease-out infinite;
        }
        @keyframes cause-pulse {
            0%   { transform: scale(1);   opacity: 1; }
            70%  { transform: scale(1.55); opacity: 0; }
            100% { transform: scale(1.55); opacity: 0; }
        }

        #cause-ai-window {
            width: 355px;
            height: 500px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: absolute;
            bottom: 70px;
            right: 0;
            opacity: 0;
            transform: translateY(20px) scale(0.97);
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #cause-ai-window.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        /* Header */
        .cause-ai-header {
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .cause-ai-header-left { display: flex; align-items: center; gap: 10px; }
        .cause-ai-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px; color: #fff;
        }
        .cause-ai-title { color: #fff; font-weight: 700; font-size: 15px; }
        .cause-ai-subtitle { color: rgba(255,255,255,0.75); font-size: 11px; margin-top: 1px; }
        .cause-ai-close-btn {
            background: rgba(255,255,255,0.15);
            border: none; cursor: pointer;
            border-radius: 50%; width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; transition: background 0.2s;
        }
        .cause-ai-close-btn:hover { background: rgba(255,255,255,0.3); }

        /* Messages */
        #cause-ai-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f9f7ff;
        }
        #cause-ai-messages::-webkit-scrollbar { width: 4px; }
        #cause-ai-messages::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 4px; }

        .cause-msg {
            max-width: 82%;
            padding: 10px 13px;
            border-radius: 16px;
            font-size: 13.5px;
            line-height: 1.5;
            word-break: break-word;
        }
        .cause-msg.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .cause-msg.bot {
            align-self: flex-start;
            background: #fff;
            color: #1f2937;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        /* Typing indicator */
        .cause-typing {
            align-self: flex-start;
            display: flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            padding: 10px 14px;
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .cause-typing span {
            width: 7px; height: 7px;
            background: #7C3AED;
            border-radius: 50%;
            display: inline-block;
            animation: cause-bounce 1.2s infinite;
        }
        .cause-typing span:nth-child(2) { animation-delay: 0.2s; }
        .cause-typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes cause-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.5; }
            40%            { transform: translateY(-6px); opacity: 1; }
        }

        /* Input area */
        .cause-ai-input-area {
            padding: 12px 14px;
            background: #fff;
            border-top: 1px solid #ede9fe;
            display: flex;
            gap: 8px;
            align-items: flex-end;
            flex-shrink: 0;
        }
        #cause-ai-input {
            flex: 1;
            border: 1.5px solid #ddd6fe;
            border-radius: 12px;
            padding: 9px 13px;
            font-size: 13.5px;
            outline: none;
            resize: none;
            max-height: 90px;
            transition: border-color 0.2s;
            font-family: inherit;
            line-height: 1.4;
        }
        #cause-ai-input:focus { border-color: #7C3AED; }
        #cause-ai-send-btn {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            border: none; border-radius: 10px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: opacity 0.2s, transform 0.15s;
        }
        #cause-ai-send-btn:hover { opacity: 0.9; transform: scale(1.05); }
        #cause-ai-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        #cause-ai-send-btn svg { width: 18px; height: 18px; color: #fff; }

        /* Unread badge */
        #cause-ai-badge {
            position: absolute;
            top: -3px; right: -3px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            display: none;
        }

        /* ── Marquee Ticker Animation ── */
        .animate-marquee {
            display: flex;
            animation: marquee 40s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 hidden md:block">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="p-6 border-b bg-gradient-to-br from-cause-purple to-indigo-900">
                        <div class="flex flex-col items-center text-center space-y-2">
                            <img src="{{ asset('images/cause-logo.png') }}" alt="CAUSE Society Logo" class="w-36 h-auto object-contain brightness-0 invert">
                            <div>
                                <h1 class="font-black text-white text-lg tracking-tight leading-tight">CAUSE SMART SOCIETY</h1>
                                <p class="text-[9px] text-purple-200 uppercase tracking-widest font-bold">MANAGEMENT SYSTEM</p>
                            </div>
                        </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    @yield('sidebar')
                </nav>
                
                <!-- User Info -->
                <div class="p-4 border-t bg-gray-50">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-cause-purple rounded-xl flex items-center justify-center shadow-md overflow-hidden border-2 border-white">
                            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=7C3AED&color=fff' }}" 
                                 alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-semibold">{{ auth()->user()->getDisplayRole() }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" class="w-full text-left text-xs font-bold text-cause-purple hover:text-cause-purple-dark flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors mb-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        My Profile
                    </a>
                    
                    @php
                        $user = auth()->user();
                        $presidentTeam = ['president', 'sa', 'vc', 'gd', 'photo', 'video', 'smt', 'doc', 'deco'];
                        $canSwitchToStudent = in_array($user->role, $presidentTeam);
                    @endphp

                    @if($canSwitchToStudent)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full text-left text-xs font-bold text-gray-700 hover:text-gray-900 flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors mb-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Switch Role
                            </span>
                            <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" style="display: none;" class="absolute bottom-full left-0 mb-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">
                            @if($canSwitchToStudent)
                                @if($user->getActiveRole() !== 'student')
                                <form action="{{ route('profile.switch-role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role" value="student">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-cause-purple font-semibold">Switch to Student</button>
                                </form>
                                @endif
                                @if($user->getActiveRole() !== $user->role)
                                <form action="{{ route('profile.switch-role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-cause-purple font-semibold">Switch to {{ ucfirst($user->role) }}</button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full text-left text-xs font-bold text-red-600 hover:text-red-800 flex items-center p-2 rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Alert Notifications -->
            @if(session('success') || session('error') || session('info'))
            <div class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm w-full animate-in fade-in slide-in-from-top-4 duration-300">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 shadow-lg rounded-r-lg flex items-start">
                        <div class="flex-shrink-0 text-green-500 mr-3">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 shadow-lg rounded-r-lg flex items-start">
                        <div class="flex-shrink-0 text-red-500 mr-3">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 shadow-lg rounded-r-lg flex items-start">
                        <div class="flex-shrink-0 text-blue-500 mr-3">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-blue-800">{{ session('info') }}</p>
                    </div>
                @endif
            </div>
            @endif

            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <!-- CUST Branding in Header -->
                        <div class="hidden lg:flex items-center space-x-3 pr-6 border-r border-gray-200">
                            <img src="https://admission.cust.edu.pk/web/image/website/1/logo?unique=f3e0a29" alt="CUST Logo" class="w-12 h-auto object-contain">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-800 text-sm leading-tight">Capital University of</span>
                                <span class="font-bold text-cause-purple text-xs leading-tight">Science & Technology</span>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 leading-tight">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-[11px] text-gray-500 font-medium">@yield('page-description', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            @php
                                $notifRoute = 'notifications.index';
                            @endphp
                            <a href="{{ Route::has($notifRoute) ? route($notifRoute) : '#' }}" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="flex items-center space-x-3 pl-4 border-l border-gray-200">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-gray-500 uppercase font-semibold mt-1">{{ auth()->user()->getDisplayRole() }}</p>
                            </div>
                            <div class="w-9 h-9 rounded-lg overflow-hidden border-2 border-gray-100 shadow-sm">
                                <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=random' }}" 
                                     alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">&times;</button>
                    </div>
                @endif
                
                @if (session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                        <span>{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900">&times;</button>
                    </div>
                @endif
                
                
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    {{-- ── CAUSE-AI Chatbot Widget (HOD / Patron / President only) ── --}}
    @php
        $causeUser = auth()->user();
        $showCauseAI = $causeUser && 
            in_array($causeUser->role, ['admin', 'hod', 'patron', 'president', 'sa', 'gd', 'student', 'photo', 'video', 'smt', 'doc', 'deco', 'vc', 'faculty']);
        
        $causeRole = 'User';
        if ($causeUser) {
            if ($causeUser->isAppointedHod()) $causeRole = 'HOD';
            else if ($causeUser->isAppointedPatron()) $causeRole = 'Patron';
            else $causeRole = ucfirst($causeUser->role);
        }
    @endphp

    @if($showCauseAI)
    <div id="cause-ai-widget" role="complementary" aria-label="CAUSE-AI Virtual Assistant">
        {{-- Chat Window --}}
        <div id="cause-ai-window" aria-live="polite">
            {{-- Header --}}
            <div class="cause-ai-header">
                <div class="cause-ai-header-left">
                    <div class="cause-ai-avatar">AI</div>
                    <div>
                        <div class="cause-ai-title">CAUSE-AI</div>
                        <div class="cause-ai-subtitle">Virtual Assistant &bull; {{ $causeRole }}</div>
                    </div>
                </div>
                <button id="cause-ai-close-btn" class="cause-ai-close-btn" title="Close" aria-label="Close chat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Messages --}}
            <div id="cause-ai-messages">
                <div class="cause-msg bot">
                    👋 Hello, <strong>{{ $causeRole }}</strong>! I'm <strong>CAUSE-AI</strong>, your virtual assistant for the CAUSE Society at CUST.<br><br>
                    How can I help you today?
                </div>
            </div>

            {{-- Input --}}
            <div class="cause-ai-input-area">
                <textarea
                    id="cause-ai-input"
                    placeholder="Ask me anything…"
                    rows="1"
                    aria-label="Message input"
                    maxlength="1000"
                ></textarea>
                <button id="cause-ai-send-btn" aria-label="Send message">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Toggle Button --}}
        <button id="cause-ai-toggle-btn" aria-label="Open CAUSE-AI assistant">
            <div class="pulse-ring"></div>
            {{-- Chat icon (closed state) --}}
            <svg id="cause-ai-icon-chat" fill="none" stroke="white" viewBox="0 0 24 24" width="26" height="26">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            {{-- Close icon (open state) --}}
            <svg id="cause-ai-icon-close" fill="none" stroke="white" viewBox="0 0 24 24" width="24" height="24" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div id="cause-ai-badge">1</div>
        </button>
    </div>

    <script>
    (function () {
        const ROLE    = {{ Js::from($causeRole) }};
        const CSRF    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const widget      = document.getElementById('cause-ai-widget');
        const win         = document.getElementById('cause-ai-window');
        const toggleBtn   = document.getElementById('cause-ai-toggle-btn');
        const closeBtn    = document.getElementById('cause-ai-close-btn');
        const messages    = document.getElementById('cause-ai-messages');
        const input       = document.getElementById('cause-ai-input');
        const sendBtn     = document.getElementById('cause-ai-send-btn');
        const badge       = document.getElementById('cause-ai-badge');
        const iconChat    = document.getElementById('cause-ai-icon-chat');
        const iconClose   = document.getElementById('cause-ai-icon-close');

        let isOpen   = false;
        let isWaiting = false;

        // Toggle open/close
        function openChat() {
            isOpen = true;
            win.classList.add('open');
            iconChat.style.display  = 'none';
            iconClose.style.display = 'block';
            badge.style.display     = 'none';
            input.focus();
            scrollBottom();
        }
        function closeChat() {
            isOpen = false;
            win.classList.remove('open');
            iconChat.style.display  = 'block';
            iconClose.style.display = 'none';
        }

        toggleBtn.addEventListener('click', () => isOpen ? closeChat() : openChat());
        closeBtn.addEventListener('click', closeChat);

        // Auto-grow textarea
        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 90) + 'px';
        });

        // Send on Enter (Shift+Enter = newline)
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        sendBtn.addEventListener('click', sendMessage);

        function scrollBottom() {
            setTimeout(() => { messages.scrollTop = messages.scrollHeight; }, 50);
        }

        function addMessage(text, type) {
            const div = document.createElement('div');
            div.className = 'cause-msg ' + type;
            if (type === 'user') {
                div.textContent = text;
            } else {
                div.innerHTML = '';
                typewriter(div, text);
            }
            messages.appendChild(div);
            scrollBottom();
            return div;
        }

        function typewriter(el, text, speed = 18) {
            let i = 0;
            el.innerHTML = '';
            const interval = setInterval(() => {
                el.innerHTML = formatText(text.slice(0, i + 1));
                i++;
                scrollBottom();
                if (i >= text.length) clearInterval(interval);
            }, speed);
        }

        function formatText(raw) {
            return raw
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g,   '<em>$1</em>')
                .replace(/\n/g, '<br>');
        }

        function showTyping() {
            const el = document.createElement('div');
            el.className = 'cause-typing';
            el.id = 'cause-typing-indicator';
            el.innerHTML = '<span></span><span></span><span></span>';
            messages.appendChild(el);
            scrollBottom();
        }
        function hideTyping() {
            const el = document.getElementById('cause-typing-indicator');
            if (el) el.remove();
        }

        async function sendMessage() {
            const text = input.value.trim();
            if (!text || isWaiting) return;

            isWaiting = true;
            sendBtn.disabled = true;
            input.value = '';
            input.style.height = 'auto';

            addMessage(text, 'user');
            showTyping();

            try {
                const res = await fetch('/api/ai-chat', {
                    method : 'POST',
                    headers: {
                        'Content-Type'    : 'application/json',
                        'X-CSRF-TOKEN'   : CSRF,
                        'Accept'          : 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message: text, role: ROLE }),
                });

                const data = await res.json();
                hideTyping();

                if (data.success) {
                    addMessage(data.response, 'bot');
                    if (!isOpen) {
                        badge.style.display = 'flex';
                    }
                } else {
                    addMessage('⚠️ ' + (data.error || 'Something went wrong. Please try again.'), 'bot');
                }
            } catch (err) {
                hideTyping();
                addMessage('⚠️ Network error. Please check your connection.', 'bot');
            }

            isWaiting = false;
            sendBtn.disabled = false;
            input.focus();
        }
    })();
    </script>
    @endif
    
    <!-- CSRF Token Refresh Script -->
    <script>
        // Setup CSRF token for all AJAX requests
        window.Laravel = {
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        // Function to refresh CSRF token
        async function refreshCSRFToken() {
            try {
                const response = await fetch('/csrf-token', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    // Update meta tag
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    // Update all CSRF input fields
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.csrf_token;
                    });
                    window.Laravel.csrfToken = data.csrf_token;
                    return data.csrf_token;
                }
            } catch (error) {
                console.log('CSRF token refresh failed:', error);
            }
            return null;
        }
        
        // Handle form submissions with CSRF token refresh on 419 error
        document.addEventListener('DOMContentLoaded', function() {
            // Intercept all form submissions
            document.querySelectorAll('form').forEach(form => {
                // Skip forms that already have custom handlers
                if (form.dataset.csrfHandled) return;
                form.dataset.csrfHandled = 'true';
                
                form.addEventListener('submit', async function(e) {
                    // Refresh CSRF token before submission
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        const newToken = await refreshCSRFToken();
                        if (newToken) {
                            csrfInput.value = newToken;
                        }
                    }
                });
            });
            
            // Refresh CSRF token every 10 minutes to prevent expiry
            setInterval(refreshCSRFToken, 10 * 60 * 1000);
            
            // Also refresh on page visibility change (when user comes back to tab)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    refreshCSRFToken();
                }
            });
        });
        
        // Handle 419 errors globally for fetch requests
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            const response = await originalFetch(...args);
            if (response.status === 419) {
                await refreshCSRFToken();
                alert('Your session was refreshed. Please try again.');
            }
            return response;
        };
    </script>
</body>
</html>
