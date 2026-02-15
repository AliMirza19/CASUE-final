@extends('layouts.dashboard')

@section('title', 'View Feedback - CAUSE Smart Society')
@section('page-title', 'Design Feedback')
@section('page-description', 'View patron feedback and annotations on your design')

@section('sidebar')
    <a href="{{ route('gd.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('gd.dashboard') }}" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Design with Annotations -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Your Design with Patron Feedback</h3>
                        <div class="mt-2">
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                @if($graphic->status == 'approved') bg-green-100 text-green-800
                                @elseif($graphic->status == 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($graphic->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($graphic->image_path)
                            <div class="relative border rounded-lg overflow-hidden" id="image-container">
                                <canvas id="annotation-canvas" class="absolute top-0 left-0 z-10"></canvas>
                                <img id="design-image" 
                                     src="{{ asset('storage/' . $graphic->image_path) }}" 
                                     alt="Design Preview" 
                                     class="w-full max-h-96 object-contain bg-gray-100"
                                     onload="loadAnnotations()">
                            </div>
                            
                            <a href="{{ asset('storage/' . $graphic->image_path) }}" 
                               target="_blank" 
                               class="inline-block mt-3 text-blue-600 hover:text-blue-800 underline">
                                Open Full Size Image
                            </a>
                        @elseif($graphic->image_link)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <p class="text-gray-600 mb-2">External Link:</p>
                                <a href="{{ $graphic->image_link }}" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 underline break-all">
                                    {{ $graphic->image_link }}
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500">No preview available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Feedback Details -->
            <div class="lg:col-span-1">
                <!-- Design Details -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Design Details</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Event</p>
                            <p class="font-semibold text-gray-800">{{ $graphic->event->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Category</p>
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                @if($graphic->design_category == 'poster') bg-blue-100 text-blue-800
                                @elseif($graphic->design_category == 'banner') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $graphic->design_category)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Uploaded</p>
                            <p class="font-semibold text-gray-800">{{ $graphic->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                @if($graphic->status == 'approved') bg-green-100 text-green-800
                                @elseif($graphic->status == 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($graphic->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Patron Feedback -->
                @if($graphic->patron_feedback || $graphic->annotations)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800">Patron Feedback</h3>
                        </div>
                        
                        <div class="p-6">
                            @if($graphic->patron_feedback)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-500 mb-2">Written Feedback:</p>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-800">{{ $graphic->patron_feedback }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($graphic->annotations && count($graphic->annotations) > 0)
                                <div>
                                    <p class="text-sm text-gray-500 mb-2">Visual Annotations:</p>
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <p class="text-blue-800 text-sm">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            The patron has added {{ count($graphic->annotations) }} annotation(s) on your design. 
                                            Check the highlighted areas on the image.
                                        </p>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$graphic->patron_feedback && (!$graphic->annotations || count($graphic->annotations) == 0))
                                <p class="text-gray-500 text-sm">No feedback provided yet.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const annotations = @json($graphic->annotations ?? []);

function loadAnnotations() {
    if (!annotations || annotations.length === 0) return;
    
    const img = document.getElementById('design-image');
    const canvas = document.getElementById('annotation-canvas');
    const ctx = canvas.getContext('2d');
    
    // Set canvas size to match image
    canvas.width = img.offsetWidth;
    canvas.height = img.offsetHeight;
    
    // Draw annotations
    annotations.forEach(annotation => {
        ctx.strokeStyle = annotation.color;
        ctx.fillStyle = annotation.color;
        ctx.lineWidth = 3;
        ctx.beginPath();
        
        if (annotation.tool === 'circle') {
            ctx.arc(annotation.startX, annotation.startY, annotation.radius, 0, 2 * Math.PI);
            ctx.stroke();
        } else if (annotation.tool === 'arrow') {
            drawArrow(ctx, annotation.startX, annotation.startY, annotation.endX, annotation.endY);
            ctx.stroke();
        } else if (annotation.tool === 'text') {
            ctx.font = '16px Arial';
            ctx.fillText(annotation.text, annotation.x, annotation.y);
        }
    });
}

function drawArrow(ctx, fromX, fromY, toX, toY) {
    const headlen = 15;
    const angle = Math.atan2(toY - fromY, toX - fromX);
    
    ctx.moveTo(fromX, fromY);
    ctx.lineTo(toX, toY);
    ctx.lineTo(toX - headlen * Math.cos(angle - Math.PI / 6), toY - headlen * Math.sin(angle - Math.PI / 6));
    ctx.moveTo(toX, toY);
    ctx.lineTo(toX - headlen * Math.cos(angle + Math.PI / 6), toY - headlen * Math.sin(angle + Math.PI / 6));
}

// Reload annotations when window resizes
window.addEventListener('resize', function() {
    setTimeout(loadAnnotations, 100);
});
</script>
@endpush