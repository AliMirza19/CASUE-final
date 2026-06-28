@extends('layouts.dashboard')

@section('title', 'Election Candidates Review - CAUSE Smart Society')
@section('page-title', 'Election Candidates Review')
@section('page-description', 'Review and approve candidate profiles for society elections')

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Review</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingCandidates->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedCandidates->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Rejected</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $rejectedCandidates->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('pending')" id="pending-tab" class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Pending Review ({{ $pendingCandidates->count() }})
                </button>
                <button onclick="showTab('approved')" id="approved-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Approved ({{ $approvedCandidates->count() }})
                </button>
                <button onclick="showTab('rejected')" id="rejected-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Rejected ({{ $rejectedCandidates->count() }})
                </button>
            </nav>
        </div>
    </div>

    <!-- Pending Candidates -->
    <div id="pending-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pending Candidates Review</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($pendingCandidates as $candidate)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                        @if($candidate->photo_url)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $candidate->photo_url) }}" 
                                     alt="Candidate Profile Photo" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-video bg-gray-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $candidate->student->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">VP: {{ $candidate->vp_name }}</p>
                            <p class="text-xs text-gray-500 mb-3">{{ $candidate->created_at->format('M d, Y') }}</p>
                            
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                Pending Review
                            </span>
                            
                            <div class="mt-4">
                                <a href="{{ route('patron.review-candidate', $candidate->id) }}" 
                                   class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg block text-center">
                                    Review Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No candidates pending review
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Approved Candidates -->
    <div id="approved-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Approved Candidates</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($approvedCandidates as $candidate)
                    <div class="border rounded-lg overflow-hidden">
                        @if($candidate->photo_url)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $candidate->photo_url) }}" 
                                     alt="Candidate Profile Photo" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $candidate->student->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">VP: {{ $candidate->vp_name }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No approved candidates
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Rejected Candidates -->
    <div id="rejected-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Rejected Candidates</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($rejectedCandidates as $candidate)
                    <div class="border rounded-lg overflow-hidden">
                        @if($candidate->photo_url)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $candidate->photo_url) }}" 
                                     alt="Candidate Profile Photo" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $candidate->student->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">VP: {{ $candidate->vp_name }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                            @if($candidate->patron_feedback)
                                <p class="text-xs text-gray-600 mt-2">{{ $candidate->patron_feedback }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No rejected candidates
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-cause-purple', 'text-cause-purple');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-cause-purple', 'text-cause-purple');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}
</script>
@endpush

@push('styles')
<style>
.tab-button.active {
    border-color: #7C3AED !important;
    color: #7C3AED !important;
}
</style>
@endpush
