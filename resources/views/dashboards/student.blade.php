@extends('layouts.dashboard')

@section('title', 'Student Dashboard - CAUSE Smart Society')
@section('page-title', 'Student Dashboard')
@section('page-description', 'Manage your events and activities')

@section('sidebar')
    <a href="{{ route('student.dashboard') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('student.events.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        My Events
    </a>
    <a href="{{ route('student.events.create') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Request Event
    </a>
@endsection

@section('content')
    <!-- System Inactive Warning -->
    @if(!($systemActive ?? true))
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-semibold">System Currently Inactive</p>
                <p class="text-sm">Event submissions are disabled until the HOD sets the term budget.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Welcome back, {{ auth()->user()->name }}!</h3>
        <p class="text-gray-600">Here's your event submission overview for the current term.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Events Submitted</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalEvents ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedEvents ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Approvals</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingEvents ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Election Voting Section -->
    @if(($votingEnabled ?? false) && ($votingPeriodActive ?? false))
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold mb-2">🗳️ Society Elections - Vote Now!</h3>
                @if($hasVoted ?? false)
                    <p class="text-purple-100">Thank you for voting! Your vote has been recorded.</p>
                @else
                    <p class="text-purple-100">Cast your vote for the Society President. Your voice matters!</p>
                @endif
                @if($electionSettings ?? null)
                    <p class="text-purple-200 text-sm mt-1">Voting ends: {{ \Carbon\Carbon::parse($electionSettings->voting_end_date)->format('F d, Y g:i A') }}</p>
                @endif
            </div>
            <div>
                @if($hasVoted ?? false)
                    <span class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold">
                        ✓ Voted
                    </span>
                @else
                    <a href="#" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition animate-pulse">
                        Vote Now
                    </a>
                @endif
            </div>
        </div>
    </div>
    @elseif($votingEnabled ?? false)
    <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">🗳️ Society Elections</h3>
                @if($electionSettings ?? null)
                    <p class="text-gray-600">Voting period: {{ \Carbon\Carbon::parse($electionSettings->voting_start_date)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($electionSettings->voting_end_date)->format('F d, Y') }}</p>
                @endif
            </div>
            <span class="bg-gray-300 text-gray-600 px-6 py-3 rounded-lg font-semibold">
                Voting Closed
            </span>
        </div>
    </div>
    @endif

    <!-- Candidate Profile Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Election Candidacy</h3>
            <a href="#" class="text-cause-purple hover:text-purple-700 font-medium text-sm">
                Manage Profile →
            </a>
        </div>
        
        @if($candidateProfile ?? null)
            <div class="flex items-center">
                <div class="p-2 rounded-full {{ $candidateProfile->status === 'approved' ? 'bg-green-100' : ($candidateProfile->status === 'rejected' ? 'bg-red-100' : 'bg-orange-100') }} mr-3">
                    <svg class="w-5 h-5 {{ $candidateProfile->status === 'approved' ? 'text-green-600' : ($candidateProfile->status === 'rejected' ? 'text-red-600' : 'text-orange-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Candidate Profile Status</p>
                    <p class="text-sm {{ $candidateProfile->status === 'approved' ? 'text-green-600' : ($candidateProfile->status === 'rejected' ? 'text-red-600' : 'text-orange-600') }}">
                        {{ $candidateProfile->status === 'approved' ? 'Approved for Election' : ($candidateProfile->status === 'rejected' ? 'Rejected' : 'Pending Patron Approval') }}
                    </p>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-gray-600 mb-3">Interested in running for Society President?</p>
                <a href="#" class="bg-cause-purple hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    Submit Candidate Profile
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($systemActive ?? true)
            <a href="{{ route('student.events.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                <div class="bg-purple-100 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Request New Event</p>
                    <p class="text-sm text-gray-600">Submit a new event proposal</p>
                </div>
            </a>
            @else
            <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-50 cursor-not-allowed opacity-60">
                <div class="bg-gray-200 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-500">Request New Event</p>
                    <p class="text-sm text-gray-400">System inactive</p>
                </div>
            </div>
            @endif
            
            <a href="{{ route('student.events.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-cause-purple hover:bg-purple-50 transition">
                <div class="bg-blue-100 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">View My Events</p>
                    <p class="text-sm text-gray-600">Check status of your submissions</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h4>
        
        @if(($recentActivities ?? collect())->isEmpty())
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">No recent activity</p>
                <p class="text-gray-400 text-sm">Your activity will appear here when you submit events</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <div class="p-1 bg-cause-purple rounded-full mr-3 mt-1">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-800 text-sm">{{ $activity->action_text }}</p>
                            <p class="text-gray-500 text-xs mt-1">{{ $activity->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
