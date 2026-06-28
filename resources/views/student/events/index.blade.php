@extends('layouts.dashboard')

@section('title', 'My Events - CAUSE Smart Society')
@section('page-title', 'My Events')
@section('page-description', 'View and manage your event submissions')

@section('sidebar')
    @include('partials.student-sidebar')
@endsection

@section('content')
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Your Event Submissions</h3>
            <p class="text-gray-600 text-sm">Track the status of your event proposals</p>
        </div>
        <a href="{{ route('student.events.create') }}" class="bg-cause-purple hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Request New Event
        </a>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($event->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800">{{ $event->expected_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $event->venue }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending_president' => 'bg-yellow-100 text-yellow-800',
                                        'president_approved' => 'bg-blue-100 text-blue-800',
                                        'pending_patron' => 'bg-yellow-100 text-yellow-800',
                                        'pending_hod' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'revision_needed' => 'bg-orange-100 text-orange-800',
                                    ];
                                    $statusLabels = [
                                        'pending_president' => 'Pending President',
                                        'president_approved' => 'President OK - Forward to Patron',
                                        'pending_patron' => 'Pending Patron',
                                        'pending_hod' => 'Pending HOD',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'revision_needed' => 'Revision Needed',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$event->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('student.events.show', $event->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    @if(in_array($event->status, ['revision_needed', 'pending_president']))
                                        <a href="{{ route('student.events.edit', $event->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    @if($event->status === 'president_approved')
                                        <form action="{{ route('student.events.forward', $event->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Forward to Patron">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">No events submitted yet</p>
                                <a href="{{ route('student.events.create') }}" class="bg-cause-purple hover:bg-purple-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Submit Your First Event
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
