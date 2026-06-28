<div class="space-y-6">
    <!-- Main Navigation -->
    <div>
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Core Management</p>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>
        <a href="{{ route('admin.budget') }}" class="sidebar-link {{ request()->routeIs('admin.budget') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">Manage Budget</span>
        </a>
        <a href="{{ route('admin.terms.index') }}" class="sidebar-link {{ request()->routeIs('admin.terms.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">Manage Terms</span>
        </a>


    </div>

    <!-- User Management -->
    <div>
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">User & Roles</p>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Manage Users</span>
        </a>
        <a href="{{ route('admin.manage-hod') }}" class="sidebar-link {{ request()->routeIs('admin.manage-hod') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">HOD Management</span>
        </a>
    </div>

    <!-- Data Collection -->
    <div>
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Data Acquisition</p>
        <a href="{{ route('admin.students.create') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Add Student Data</span>
        </a>
        <a href="{{ route('admin.faculty.create') }}" class="sidebar-link {{ request()->routeIs('admin.faculty.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">Add Faculty Data</span>
        </a>
    </div>
    <!-- Digital Archive -->
    <div class="mt-4 pt-4 border-t border-gray-100">
        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Knowledge Base</p>
        <a href="{{ route('archive.index') }}" class="sidebar-link {{ request()->routeIs('archive.*') ? 'active' : '' }} flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1 transition-all">
            <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="font-medium">Digital Archive</span>
        </a>
    </div>
</div>

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
