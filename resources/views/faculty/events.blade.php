@extends('layouts.dashboard')

@section('title', 'All Events - CAUSE Smart Society')
@section('page-title', 'All Events')
@section('page-description', 'Browse all approved society events')

@section('sidebar')
    @include('partials.faculty-sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('faculty.events') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Search events by title or description..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Filter Dropdown -->
            <div class="w-full md:w-48">
                <select name="filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Events</option>
                    <option value="upcoming" {{ $filter === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="past" {{ $filter === 'past' ? 'selected' : '' }}>Past Events</option>
                </select>
            </div>
            
            <!-- Submit -->
            <button type="submit" class="px-6 py-2 bg-cause-purple text-white rounded-lg hover:bg-cause-purple-dark transition">
                Filter
            </button>
            
            @if($search || $filter !== 'all')
            <a href="{{ route('faculty.events') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center">
                Clear
            </a>
            @endif
        </form>
    </div>
    
    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-semibold text-lg text-gray-800">{{ $event->title }}</h3>
                    @if($event->expected_date >= now())
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Upcoming</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Past</span>
                    @endif
                </div>
                
                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($event->description, 100) }}</p>
                
                <div class="space-y-2 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $event->expected_date->format('M d, Y') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $event->venue ?? 'Venue TBD' }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $event->student->name ?? 'Unknown' }}
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('faculty.events.show', $event->id) }}" 
                       class="text-cause-purple hover:text-cause-purple-dark font-medium text-sm flex items-center">
                        View Details
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-800 mb-2">No Events Found</h3>
                <p class="text-gray-500">
                    @if($search)
                    No events match your search "{{ $search }}".
                    @elseif($filter === 'upcoming')
                    There are no upcoming events at the moment.
                    @elseif($filter === 'past')
                    There are no past events to display.
                    @else
                    No approved events available yet.
                    @endif
                </p>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($events->hasPages())
    <div class="bg-white rounded-lg shadow-md p-4">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection
