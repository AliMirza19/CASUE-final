@extends('layouts.dashboard')

@section('title', 'Notifications - CAUSE Smart Society')
@section('page-title', 'Notifications')
@section('page-description', 'Stay updated with your event requests')

@section('sidebar')
    @php
        $user = auth()->user();
        $sidebar = 'student-sidebar'; // Default
        
        if ($user->role === 'admin') $sidebar = 'admin-sidebar';
        elseif ($user->role === 'hod') $sidebar = 'hod-sidebar';
        elseif ($user->role === 'patron') $sidebar = 'patron-sidebar';
        elseif ($user->role === 'president') $sidebar = 'president-sidebar';
        elseif ($user->role === 'faculty') $sidebar = 'faculty-sidebar';
        elseif ($user->role === 'vc') $sidebar = 'vc-sidebar';
        elseif (in_array($user->role, ['gd', 'photo', 'video', 'smt', 'doc', 'deco', 'sa'])) $sidebar = 'team-sidebar';
        
        // Special case for faculty who are HOD/Patron
        if ($user->role === 'faculty') {
            if ($user->isAppointedHod()) $sidebar = 'hod-sidebar';
            elseif ($user->isAppointedPatron()) $sidebar = 'patron-sidebar';
        }
    @endphp
    @include('partials.' . $sidebar)
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Notifications</h3>
            @if($notifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-cause-purple hover:text-cause-purple-dark hover:underline">Mark all as read</button>
            </form>
            @endif
        </div>
        
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                @php
                                    // Determine icon type from either 'notification_type' (user imports) or 'type' (events)
                                    $iconType = $notification->data['notification_type'] ?? $notification->data['type'] ?? 'info';
                                @endphp
                                @if($iconType == 'success')
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($iconType == 'error')
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @elseif($iconType == 'warning')
                                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    @if(($notification->data['type'] ?? '') === 'user_import')
                                        {{ $notification->data['method'] === 'bulk' ? 'Bulk User Upload' : ucfirst($notification->data['import_type'] ?? 'User') . ' Registration' }}
                                    @else
                                        {{ $notification->data['event_title'] ?? $notification->data['message'] ?? 'Notification' }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $notification->data['message'] }}
                                </p>
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <a href="{{ route('notifications.read', $notification->id) }}" class="text-sm font-medium text-cause-purple hover:text-cause-purple-dark">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
            </div>
        @endif
    </div>
@endsection
