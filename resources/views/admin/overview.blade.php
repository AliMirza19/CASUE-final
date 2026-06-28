@extends('layouts.dashboard')

@section('title', 'System Overview - CAUSE Smart Society')
@section('page-title', 'System Overview')
@section('page-description', 'Detailed analytics and system management summary')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <!-- HOD Assignment Alert -->
    @if(isset($needsHodAssignment) && $needsHodAssignment && $activeTerm)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-bold">New Term Detected!</p>
                <p class="text-sm">No HOD has been assigned for {{ $activeTerm->term_name }}. Please assign an HOD.</p>
            </div>
        </div>
        <a href="{{ route('admin.manage-hod') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition">
            Assign HOD
        </a>
    </div>
    @endif

    <!-- Term Expired Warning -->
    @if($activeTermExpired)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>The active term has expired! Please <a href="{{ route('admin.terms.index') }}" class="font-semibold underline">create a new term</a> or update the current one.</span>
    </div>
    @endif

    <!-- Term Selector -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Select Term:</label>
            <select name="term_id" onchange="this.form.submit()" 
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                @foreach($allTerms as $term)
                    <option value="{{ $term->id }}" {{ $selectedTermId == $term->id ? 'selected' : '' }}>
                        {{ $term->name }} {{ $term->status === 'active' ? '(Active)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Terms</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTerms }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Term Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $termEventsCount }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Budget Spent</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">PKR {{ number_format($termBudgetSpent, 0) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Status Summary -->
    <div class="mb-8">
        @include('partials.tasks-widget')
    </div>


    <!-- Quick Actions & Term Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h4>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.terms.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-purple-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">New Term</p>
                        <p class="text-sm text-gray-500">Create term</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.terms.index') }}" class="sidebar-link flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-green-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">View Terms</p>
                        <p class="text-sm text-gray-500">Manage terms</p>
                    </div>
                </a>

                <a href="{{ route('admin.budget') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-red-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Assign Budget</p>
                        <p class="text-sm text-gray-500">Set term limits</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-yellow-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">View Users</p>
                        <p class="text-sm text-gray-500">Manage users</p>
                    </div>
                </a>

                <a href="{{ route('admin.students.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-indigo-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Add Student Data</p>
                        <p class="text-sm text-gray-500">F25 AI-SE Template</p>
                    </div>
                </a>

                <a href="{{ route('admin.faculty.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                    <div class="bg-blue-100 rounded-lg p-3 mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Add Faculty Data</p>
                        <p class="text-sm text-gray-500">Admin Template</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Selected Term Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Term Information</h4>
            @if($selectedTerm)
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Term Name</span>
                        <span class="font-medium text-gray-800">{{ $selectedTerm->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $selectedTerm->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($selectedTerm->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Start Date</span>
                        <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($selectedTerm->start_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">End Date</span>
                        <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($selectedTerm->end_date)->format('M d, Y') }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pending Events</span>
                        <span class="font-medium text-yellow-600">{{ $termPendingEvents }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Approved Events</span>
                        <span class="font-medium text-green-600">{{ $termApprovedEvents }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No term selected</p>
                </div>
            @endif
        </div>
    </div>

    <!-- HOD Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Current HOD</h4>
            <a href="{{ route('admin.manage-hod') }}" class="text-cause-purple hover:text-cause-purple-dark text-sm font-medium">
                Manage →
            </a>
        </div>
        @if(isset($currentHodAssignment) && $currentHodAssignment)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-orange-600 font-bold text-lg">{{ substr($currentHodAssignment->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $currentHodAssignment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $currentHodAssignment->user->reg_id }} • {{ $currentHodAssignment->user->email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-500 mt-1">Since {{ $currentHodAssignment->assigned_at->format('M d, Y') }}</p>
                </div>
            </div>
        @elseif($hod)
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                    <span class="text-orange-600 font-bold text-lg">{{ substr($hod->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-medium text-gray-800">{{ $hod->name }}</p>
                    <p class="text-sm text-gray-500">{{ $hod->reg_id }} • {{ $hod->email }}</p>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-gray-500 mb-3">No HOD assigned for this term</p>
                <a href="{{ route('admin.manage-hod') }}" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-2 px-4 rounded-lg transition">
                    Assign HOD
                </a>
            </div>
        @endif
    </div>
@endsection
