<a href="{{ route('president.dashboard') }}" class="sidebar-link {{ request()->routeIs('president.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Dashboard
</a>


<a href="{{ route('announcements.create') }}" class="sidebar-link {{ request()->routeIs('announcements.create') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
    </svg>
    Post Announcement
</a>

<a href="{{ route('president.manage-teams') }}" class="sidebar-link {{ request()->routeIs('president.manage-teams') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    Manage Leads
</a>

<a href="{{ route('president.tasks.index') }}" class="sidebar-link {{ request()->routeIs('president.tasks.index') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    View Tasks
</a>

<a href="{{ route('president.tasks.assign') }}" class="sidebar-link {{ request()->routeIs('president.tasks.assign') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    Assign Tasks
</a>

<a href="{{ route('president.review-list') }}" class="sidebar-link {{ request()->routeIs('president.review-list') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    Review Events
</a>

<a href="{{ route('president.track-events') }}" class="sidebar-link {{ request()->routeIs('president.track-events') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    Track All Events
</a>

<a href="{{ route('president.my-events.index') }}" class="sidebar-link {{ request()->routeIs('president.my-events.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    My Events
</a>

<a href="{{ route('chat.index') }}" class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
    </svg>
    Community Chat
</a>

<a href="{{ route('direct-chat.index') }}" class="sidebar-link {{ request()->routeIs('direct-chat.index') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
    Direct Messages
</a>

<a href="{{ route('president.student-messages') }}" class="sidebar-link {{ request()->routeIs('president.student-messages') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
    </svg>
    Student Messages
    @php
        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())
            ->whereIn('sender_id', \App\Models\User::where('role', 'student')->pluck('id'))
            ->where('is_read', false)
            ->count();
    @endphp
    @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
    @endif
</a>

