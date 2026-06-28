@extends('layouts.dashboard')

@section('title', 'Review Events - CAUSE Smart Society')
@section('page-title', 'Review Events')
@section('page-description', 'Review event details and either approve or request revisions')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
    <!-- Term Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-4 border border-gray-100">
        <div class="flex items-center space-x-2">
            <div class="bg-cause-purple/10 p-2 rounded-lg">
                <svg class="w-5 h-5 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-gray-800 font-bold">Event History</h2>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Viewing records for: <span class="font-bold text-cause-purple">{{ $selectedTerm->term_name }}</span></p>
            </div>
        </div>
        
        <form action="{{ route('president.review-list') }}" method="GET" class="flex items-center space-x-3">
            <label class="text-sm font-medium text-gray-600 hidden sm:block">Select Term:</label>
            <select name="term_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-cause-purple focus:border-cause-purple block w-full p-2.5 min-w-[200px]">
                @foreach($allTerms as $term)
                    <option value="{{ $term->id }}" {{ $selectedTerm->id == $term->id ? 'selected' : '' }}>
                        {{ $term->term_name }} {{ $term->status == 'active' ? '(Current)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Pending Review Events -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-lg font-semibold text-gray-800">Events Pending Your Review</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pendingEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $event->items->count() }} budget items</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $event->student->reg_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $event->venue }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                PKR {{ number_format($event->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('president.review', $event->id) }}" class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition inline-flex items-center">
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

    <!-- Revision Needed Events -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
            <h3 class="text-lg font-semibold text-gray-800">Events Sent for Revision</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent on</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($revisionEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $event->student->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Revision Needed</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">No events in revision</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Approved Events History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-semibold text-gray-800">Your Approved Events History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved on</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Current Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($approvedEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $event->student->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $event->updated_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'president_approved' => 'bg-blue-100 text-blue-800',
                                        'pending_patron' => 'bg-indigo-100 text-indigo-800',
                                        'pending_hod' => 'bg-purple-100 text-purple-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'president_approved' => 'Approved by You',
                                        'pending_patron' => 'With Patron',
                                        'pending_hod' => 'With HOD',
                                        'approved' => 'Fully Approved',
                                        'completed' => 'Completed',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClasses[$event->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$event->status] ?? ucfirst(str_replace('_', ' ', $event->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('president.review', $event->id) }}" class="text-cause-purple hover:text-cause-purple-dark font-medium text-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No approved events history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
