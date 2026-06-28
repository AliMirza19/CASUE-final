@extends('layouts.dashboard')

@section('title', 'Events Review - CAUSE Smart Society')
@section('page-title', 'Events Review')
@section('page-description', 'Review and approve event budget proposals')

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalPendingEvents }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        

        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedEvents }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Events Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Events Pending Patron Review</h3>
            <p class="text-gray-600 text-sm mt-1">Review budget details and approve before forwarding to HOD</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pendingEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500">{{ $event->items->count() }} items</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $event->student->reg_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-800">
                                {{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                PKR {{ number_format($event->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('patron.review-event', $event->id) }}" 
                                   class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No events pending review</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>



@endsection
