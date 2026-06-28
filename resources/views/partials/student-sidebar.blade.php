<a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Dashboard
</a>


<a href="{{ route('student.events.index') }}" class="sidebar-link {{ request()->routeIs('student.events.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    My Events
</a>

<a href="{{ route('student.events.create') }}" class="sidebar-link {{ request()->routeIs('student.events.create') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Request Event
</a>

<a href="{{ route('student.overview') }}#volunteer-pool" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    Join as Volunteer
    @if(auth()->user()->is_volunteer_pool)
        <span class="ml-auto w-2 h-2 bg-green-500 rounded-full"></span>
    @endif
</a>

<a href="{{ route('student.messages') }}" class="sidebar-link {{ request()->routeIs('student.messages') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
    Message President
    @php
        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())
            ->where('sender_id', \App\Models\User::where('role', 'president')->first()?->id)
            ->where('is_read', false)
            ->count();
    @endphp
    @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
    @endif
</a>
