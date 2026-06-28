<a href="{{ route('vc.dashboard') }}" class="sidebar-link {{ request()->routeIs('vc.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Dashboard
</a>

<a href="{{ route('vc.overview') }}" class="sidebar-link {{ request()->routeIs('vc.overview') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    Volunteers
</a>

<a href="{{ route('direct-chat.index') }}" class="sidebar-link {{ request()->routeIs('direct-chat.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
    Direct Messages
    @php
        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
    @endphp
    @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
    @endif
</a>


