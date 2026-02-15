@extends('layouts.dashboard')

@section('title', 'Review Candidate - CAUSE Smart Society')
@section('page-title', 'Review Candidate Profile')
@section('page-description', 'Review and approve election candidate')

@section('sidebar')
    <a href="{{ route('patron.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('patron.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Candidate Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start mb-6">
                    @if($candidate->photo_url)
                        <img src="{{ $candidate->photo_url }}" alt="Candidate Photo" class="w-24 h-24 rounded-full object-cover mr-6">
                    @else
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mr-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $candidate->student->name }}</h3>
                        <p class="text-gray-500">{{ $candidate->student->reg_id }}</p>
                        <p class="text-gray-500">{{ $candidate->student->email }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Proposed Vice President</p>
                        <p class="font-medium text-gray-800">{{ $candidate->vp_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Submitted On</p>
                        <p class="font-medium text-gray-800">{{ $candidate->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-2">Manifesto</p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $candidate->manifesto }}</p>
                    </div>
                </div>
                
                @if($candidate->experience)
                <div>
                    <p class="text-sm text-gray-500 mb-2">Experience</p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $candidate->experience }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Form -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Review Action</h4>
                
                <form action="{{ route('patron.approve-candidate', $candidate->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                        <textarea name="feedback" rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="Add feedback..."></textarea>
                    </div>
                    
                    <div class="space-y-3">
                        <button type="submit" name="action" value="approve"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Candidate
                        </button>
                        
                        <button type="submit" name="action" value="reject"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
