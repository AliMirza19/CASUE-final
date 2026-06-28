@php
    $role = auth()->user()->role;
    $dashboardRoute = $role . '.dashboard';
@endphp

<a href="{{ route($dashboardRoute) }}" class="sidebar-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Dashboard
</a>

@php
    $overviewRoute = $role . '.overview';
@endphp
@if(Route::has($overviewRoute))
<a href="{{ route($overviewRoute) }}" class="sidebar-link {{ request()->routeIs($overviewRoute) ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
    </svg>
    {{ in_array($role, ['gd', 'photo', 'video', 'smt', 'doc', 'deco']) ? 'View Tasks' : 'Overview' }}
</a>
@endif

@if($role === 'gd')
<a href="{{ route('gd.certificate.generator') }}" class="sidebar-link {{ request()->routeIs('gd.certificate.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    Certificate Generator
</a>
@endif

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

<!-- President Selection Links -->
@if(auth()->user()->role === 'patron' || auth()->user()->isAppointedPatron())
<a href="{{ route('selection.patron') }}" class="sidebar-link {{ request()->routeIs('selection.patron') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    Shortlist President
</a>
@endif

@if(auth()->user()->role === 'hod' || auth()->user()->isAppointedHod())
<a href="{{ route('selection.hod') }}" class="sidebar-link {{ request()->routeIs('selection.hod') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
    Select President
</a>
@endif

@php
    $isCommitteeMember = \App\Models\CommitteeMember::where('faculty_user_id', auth()->id())
        ->whereHas('committee', function($q) { $q->where('is_active', true); })->exists();
@endphp
@if($isCommitteeMember)
<a href="{{ route('selection.discussion') }}" class="sidebar-link {{ request()->routeIs('selection.discussion') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
    Committee Chat
</a>
@endif

