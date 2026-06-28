@extends('layouts.dashboard')

@section('title', 'Track All Events - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Track All Events</h1>
            <p class="text-gray-600 mt-1">Monitor all events across the system</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Pending President</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $events->get('pending_president', collect())->count() }}</p>
                    </div>
                    <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-6 border-l-4 border-purple-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Pending Patron</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $events->get('pending_patron', collect())->count() }}</p>
                    </div>
                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Pending HOD</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $events->get('pending_hod', collect())->count() }}</p>
                    </div>
                    <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-6 border-l-4 border-green-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Approved</p>
                        <p class="text-2xl font-bold text-green-900">{{ $events->get('approved', collect())->count() }}</p>
                    </div>
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Events by Status -->
        @foreach(['pending_president' => 'Pending President Review', 'pending_patron' => 'Pending Patron Review', 'pending_hod' => 'Pending HOD Approval', 'approved' => 'Approved Events', 'rejected' => 'Rejected Events'] as $status => $title)
            @if($events->has($status) && $events->get($status)->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        @php
                            $statusIcons = [
                                'pending_president' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                'pending_patron' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                'pending_hod' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                'approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            ];
                            $statusColors = [
                                'pending_president' => 'text-yellow-500',
                                'pending_patron' => 'text-purple-500',
                                'pending_hod' => 'text-blue-500',
                                'approved' => 'text-green-500',
                                'rejected' => 'text-red-500',
                            ];
                        @endphp
                        <svg class="w-6 h-6 mr-2 {{ $statusColors[$status] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$status] }}"></path>
                        </svg>
                        {{ $title }} ({{ $events->get($status)->count() }})
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Budget</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($events->get($status) as $event)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $event->title }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $event->student->name }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ ucfirst($event->created_by_role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $event->expected_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">Rs. {{ number_format($event->grand_total, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($event->created_by_role === 'patron')
                                                <a href="{{ route('patron.my-events.show', $event->id) }}" class="text-cause-purple hover:text-purple-700 font-semibold">View</a>
                                            @elseif($status === 'pending_patron')
                                                <a href="{{ route('patron.review-event', $event->id) }}" class="text-cause-purple hover:text-purple-700 font-semibold">Review</a>
                                            @else
                                                <a href="{{ route('student.events.show', $event->id) }}" class="text-cause-purple hover:text-purple-700 font-semibold">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        @if($events->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Events Found</h3>
                <p class="text-gray-500">There are no events in the current term.</p>
            </div>
        @endif
    </div>
</div>
@endsection
