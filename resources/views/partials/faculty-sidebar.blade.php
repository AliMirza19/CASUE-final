<a href="{{ route('faculty.dashboard') }}" class="sidebar-link {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Dashboard
</a>

<a href="{{ route('direct-chat.index') }}" class="sidebar-link {{ request()->routeIs('direct-chat.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
    Direct Messages
@php
    $isCommitteeMember = \App\Models\CommitteeMember::where('faculty_user_id', auth()->id())
        ->whereHas('committee', function($q) { $q->where('is_active', true); })->exists();
@endphp
@if($isCommitteeMember)
<a href="{{ route('selection.discussion') }}" class="sidebar-link {{ request()->routeIs('selection.discussion') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
    Committee Chat
</a>
@endif

    @php
        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
    @endphp
    @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
    @endif
</a>

<a href="{{ route('faculty.events') }}" class="sidebar-link {{ request()->routeIs('faculty.events') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    All Approved Events
</a>

<a href="{{ route('faculty.my-events.index') }}" class="sidebar-link {{ request()->routeIs('faculty.my-events.index') || request()->routeIs('faculty.my-events.show') || request()->routeIs('faculty.my-events.edit') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    My Events
</a>

<a href="{{ route('faculty.my-events.create') }}" class="sidebar-link {{ request()->routeIs('faculty.my-events.create') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Request Event
</a>


