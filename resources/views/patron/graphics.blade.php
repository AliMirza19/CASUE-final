@extends('layouts.dashboard')

@section('title', 'Graphics Review - CAUSE Smart Society')
@section('page-title', 'Graphics Review')
@section('page-description', 'Review all graphic designs submitted by designers')

@section('sidebar')
    <a href="{{ route('patron.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    
    <a href="{{ route('patron.graphics') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Graphics Review
    </a>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Review</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingGraphics->count() }}</p>
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedGraphics->count() }}</p>
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $rejectedGraphics->count() }}</p>
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
                    Pending Review ({{ $pendingGraphics->count() }})
                </button>
                <button onclick="showTab('approved')" id="approved-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Approved ({{ $approvedGraphics->count() }})
                </button>
                <button onclick="showTab('rejected')" id="rejected-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Rejected ({{ $rejectedGraphics->count() }})
                </button>
            </nav>
        </div>
    </div>

    <!-- Pending Graphics -->
    <div id="pending-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pending Graphics Review</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($pendingGraphics as $graphic)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                        @if($graphic->image_path)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $graphic->image_path) }}" 
                                     alt="Design Preview" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-video bg-gray-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $graphic->event->title }}</h4>
                            <p class="text-sm text-gray-600 mb-2">By: {{ $graphic->designer->name }}</p>
                            <p class="text-xs text-gray-500 mb-3">{{ $graphic->created_at->format('M d, Y H:i') }}</p>
                            
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($graphic->design_category == 'poster') bg-blue-100 text-blue-800
                                @elseif($graphic->design_category == 'banner') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $graphic->design_category)) }}
                            </span>
                            
                            <div class="mt-4">
                                <a href="{{ route('patron.review-graphics', $graphic->id) }}" 
                                   class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg block text-center">
                                    Review & Annotate
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No graphics pending review
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Approved Graphics -->
    <div id="approved-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Approved Graphics</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($approvedGraphics as $graphic)
                    <div class="border rounded-lg overflow-hidden">
                        @if($graphic->image_path)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $graphic->image_path) }}" 
                                     alt="Design Preview" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $graphic->event->title }}</h4>
                            <p class="text-sm text-gray-600 mb-2">By: {{ $graphic->designer->name }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No approved graphics
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Rejected Graphics -->
    <div id="rejected-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Rejected Graphics</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($rejectedGraphics as $graphic)
                    <div class="border rounded-lg overflow-hidden">
                        @if($graphic->image_path)
                            <div class="aspect-video bg-gray-100">
                                <img src="{{ asset('storage/' . $graphic->image_path) }}" 
                                     alt="Design Preview" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $graphic->event->title }}</h4>
                            <p class="text-sm text-gray-600 mb-2">By: {{ $graphic->designer->name }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                            @if($graphic->patron_feedback)
                                <p class="text-xs text-gray-600 mt-2">{{ $graphic->patron_feedback }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No rejected graphics
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