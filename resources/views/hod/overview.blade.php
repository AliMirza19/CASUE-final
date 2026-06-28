@extends('layouts.dashboard')

@section('title', 'Review Events - CAUSE Smart Society')
@section('page-title', 'Review Events')
@section('page-description', 'Budget status and pending department approvals')

@section('sidebar')
    @include('partials.hod-sidebar')
@endsection

@section('content')
    <!-- Patron Assignment Alert -->
    @if(isset($needsPatronAssignment) && $needsPatronAssignment && $currentTerm)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-bold">New Term Detected!</p>
                <p class="text-sm">No Patron has been assigned for {{ $currentTerm->term_name }}. Please assign a Patron.</p>
            </div>
        </div>
        <a href="{{ route('hod.manage-patron') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition">
            Assign Patron
        </a>
    </div>
    @endif

    <!-- Budget Warning -->
    @if(!$budget || $budget->remaining_amount <= 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Please <a href="{{ route('hod.budget') }}" class="font-semibold underline">set the term budget</a> to enable event approvals.</span>
    </div>
    @endif

    <!-- Welcome Section -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Welcome back, {{ auth()->user()->name }}!</h3>
        <p class="text-gray-600 mt-1">Here's your review events for the current term.</p>
    </div>


    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Term Total Budget</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">PKR {{ number_format($totalBudget, 2) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Remaining Balance</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">PKR {{ number_format($remainingBudget, 2) }}</p>
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
                    <p class="text-gray-600 text-sm font-medium">Pending Events</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $totalPending }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $totalApproved }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>



    <!-- Pending HOD Approvals -->
    @if($budget && $pendingEvents->count() > 0)
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Events Pending HOD Approval</h3>
            <p class="text-gray-600 text-sm mt-1">Review patron-approved events and finalize budget allocation</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($pendingEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500">{{ $event->venue }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $event->student->reg_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold {{ $event->grand_total > $remainingBudget ? 'text-red-600' : 'text-gray-800' }}">
                                    PKR {{ number_format($event->grand_total, 2) }}
                                </span>
                                @if($event->grand_total > $remainingBudget)
                                    <div class="text-xs text-red-500">Exceeds budget</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('hod.review', $event->id) }}" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($budget)
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-gray-500">No events pending approval</p>
    </div>
    @endif
@endsection
