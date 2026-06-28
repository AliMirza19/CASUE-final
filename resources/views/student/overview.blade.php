@extends('layouts.dashboard')

@section('title', 'My Dashboard - CAUSE Smart Society')
@section('page-title', 'My Dashboard')
@section('page-description', 'Your event submissions, election status, and volunteer pool')

@section('sidebar')
    @include('partials.student-sidebar')
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


    <!-- Volunteer Pool Section -->
    <div id="volunteer-pool" class="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-lg shadow-md p-8 mb-6 text-white text-center">
        <h3 class="text-2xl font-bold mb-4">🤝 Join the Volunteer</h3>
        <p class="text-teal-100 text-lg mb-8 max-w-2xl mx-auto">
            Gain experience, skills, and help make events successful.
        </p>
        
        <div class="flex justify-center">
            <form action="{{ route('student.join-volunteer-pool') }}" method="POST">
                @csrf
                @if(auth()->user()->is_volunteer_pool)
                    <button type="submit" class="bg-white text-teal-700 px-10 py-4 rounded-xl font-bold hover:bg-gray-100 transition shadow-xl transform hover:scale-105 active:scale-95">
                        Leave Pool
                    </button>
                @else
                    <button type="submit" class="bg-white text-teal-700 px-10 py-4 rounded-xl font-bold hover:bg-gray-100 transition shadow-xl animate-bounce transform hover:scale-105 active:scale-95">
                        Join Now
                    </button>
                @endif
            </form>
        </div>
        
        @if(auth()->user()->is_volunteer_pool)
            <p class="mt-6 text-teal-200 text-sm italic font-medium">
                ✓ You are currently in the Volunteer Pool! The Volunteer Coordinator has been notified.
            </p>
        @endif
    </div>
@endsection
