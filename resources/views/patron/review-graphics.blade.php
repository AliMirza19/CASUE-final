@extends('layouts.dashboard')

@section('title', 'Review Graphics - CAUSE Smart Society')
@section('page-title', 'Review Graphic Design')
@section('page-description', 'Review and approve/reject the uploaded design')

@section('sidebar')
    <a href="{{ route('patron.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    
    <a href="{{ route('patron.graphics') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mt-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Graphics Review
    </a>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('patron.graphics') }}" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Graphics Review
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Image Annotation Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Design Preview & Annotation</h3>
                            <div class="flex gap-2">
                                <button id="annotate-btn" onclick="toggleAnnotation()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Annotate
                                </button>
                                <button onclick="clearAnnotations()" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($graphic->image_path)
                            <div class="relative border rounded-lg overflow-hidden" id="image-container">
                                <canvas id="annotation-canvas" class="absolute top-0 left-0 z-10 cursor-crosshair hidden"></canvas>
                                <img id="design-image" 
                                     src="{{ asset('storage/' . $graphic->image_path) }}" 
                                     alt="Design Preview" 
                                     class="w-full max-h-96 object-contain bg-gray-100">
                            </div>
                            
                            <!-- Annotation Tools -->
                            <div id="annotation-tools" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                                <div class="flex items-center gap-4 mb-3">
                                    <label class="text-sm font-medium text-gray-700">Annotation Color:</label>
                                    <div class="flex gap-2">
                                        <button onclick="setAnnotationColor('#ef4444')" class="w-6 h-6 bg-red-500 rounded-full border-2 border-gray-300 hover:border-gray-500"></button>
                                        <button onclick="setAnnotationColor('#f97316')" class="w-6 h-6 bg-orange-500 rounded-full border-2 border-gray-300 hover:border-gray-500"></button>
                                        <button onclick="setAnnotationColor('#eab308')" class="w-6 h-6 bg-yellow-500 rounded-full border-2 border-gray-300 hover:border-gray-500"></button>
                                        <button onclick="setAnnotationColor('#22c55e')" class="w-6 h-6 bg-green-500 rounded-full border-2 border-gray-300 hover:border-gray-500"></button>
                                        <button onclick="setAnnotationColor('#3b82f6')" class="w-6 h-6 bg-blue-500 rounded-full border-2 border-gray-300 hover:border-gray-500"></button>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <label class="text-sm font-medium text-gray-700">Tool:</label>
                                    <button onclick="setAnnotationTool('circle')" class="annotation-tool active bg-cause-purple text-white text-sm py-1 px-3 rounded">Circle</button>
                                    <button onclick="setAnnotationTool('arrow')" class="annotation-tool bg-gray-200 text-gray-700 text-sm py-1 px-3 rounded">Arrow</button>
                                    <button onclick="setAnnotationTool('text')" class="annotation-tool bg-gray-200 text-gray-700 text-sm py-1 px-3 rounded">Text</button>
                                </div>
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

            <!-- Details and Review Form -->
            <div class="lg:col-span-1">
                <!-- Graphic Details Card -->
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
                            <p class="text-sm text-gray-500 mb-1">Event Date</p>
                            <p class="font-semibold text-gray-800">{{ $graphic->event->expected_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Designer</p>
                            <p class="font-semibold text-gray-800">{{ $graphic->designer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $graphic->designer->reg_id }}</p>
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
                    </div>
                </div>

                <!-- Review Form -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Review Decision</h3>
                    </div>
                    
                    <form action="{{ route('patron.approve-graphics', $graphic->id) }}" method="POST" class="p-6">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                            <textarea name="feedback" rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                                      placeholder="Enter your feedback or comments about the design...">{{ old('feedback') }}</textarea>
                            @error('feedback')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Hidden field for annotations -->
                        <input type="hidden" name="annotations" id="annotations-data">
                        
                        <div class="space-y-3">
                            <button type="submit" name="action" value="approve" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Design
                            </button>
                            <button type="submit" name="action" value="reject" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Design
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let canvas, ctx, isAnnotating = false, currentTool = 'circle', currentColor = '#ef4444';
let annotations = [];
let isDrawing = false, startX, startY;

function toggleAnnotation() {
    const canvas = document.getElementById('annotation-canvas');
    const tools = document.getElementById('annotation-tools');
    const btn = document.getElementById('annotate-btn');
    
    if (isAnnotating) {
        canvas.classList.add('hidden');
        tools.classList.add('hidden');
        btn.textContent = 'Annotate';
        isAnnotating = false;
    } else {
        setupCanvas();
        canvas.classList.remove('hidden');
        tools.classList.remove('hidden');
        btn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Stop';
        isAnnotating = true;
    }
}

function setupCanvas() {
    const img = document.getElementById('design-image');
    canvas = document.getElementById('annotation-canvas');
    ctx = canvas.getContext('2d');
    
    // Set canvas size to match image
    canvas.width = img.offsetWidth;
    canvas.height = img.offsetHeight;
    
    // Add event listeners
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('click', addTextAnnotation);
}

function startDrawing(e) {
    if (currentTool === 'text') return;
    
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    startX = e.clientX - rect.left;
    startY = e.clientY - rect.top;
}

function draw(e) {
    if (!isDrawing || currentTool === 'text') return;
    
    const rect = canvas.getBoundingClientRect();
    const currentX = e.clientX - rect.left;
    const currentY = e.clientY - rect.top;
    
    // Clear canvas and redraw all annotations
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    redrawAnnotations();
    
    // Draw current annotation
    ctx.strokeStyle = currentColor;
    ctx.lineWidth = 3;
    ctx.beginPath();
    
    if (currentTool === 'circle') {
        const radius = Math.sqrt(Math.pow(currentX - startX, 2) + Math.pow(currentY - startY, 2));
        ctx.arc(startX, startY, radius, 0, 2 * Math.PI);
    } else if (currentTool === 'arrow') {
        drawArrow(startX, startY, currentX, currentY);
    }
    
    ctx.stroke();
}

function stopDrawing(e) {
    if (!isDrawing || currentTool === 'text') return;
    
    isDrawing = false;
    const rect = canvas.getBoundingClientRect();
    const endX = e.clientX - rect.left;
    const endY = e.clientY - rect.top;
    
    // Save annotation
    const annotation = {
        tool: currentTool,
        color: currentColor,
        startX: startX,
        startY: startY,
        endX: endX,
        endY: endY
    };
    
    if (currentTool === 'circle') {
        annotation.radius = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
    }
    
    annotations.push(annotation);
    updateAnnotationsData();
}

function addTextAnnotation(e) {
    if (currentTool !== 'text') return;
    
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const text = prompt('Enter annotation text:');
    if (text) {
        const annotation = {
            tool: 'text',
            color: currentColor,
            x: x,
            y: y,
            text: text
        };
        
        annotations.push(annotation);
        redrawAnnotations();
        updateAnnotationsData();
    }
}

function drawArrow(fromX, fromY, toX, toY) {
    const headlen = 15;
    const angle = Math.atan2(toY - fromY, toX - fromX);
    
    ctx.moveTo(fromX, fromY);
    ctx.lineTo(toX, toY);
    ctx.lineTo(toX - headlen * Math.cos(angle - Math.PI / 6), toY - headlen * Math.sin(angle - Math.PI / 6));
    ctx.moveTo(toX, toY);
    ctx.lineTo(toX - headlen * Math.cos(angle + Math.PI / 6), toY - headlen * Math.sin(angle + Math.PI / 6));
}

function redrawAnnotations() {
    annotations.forEach(annotation => {
        ctx.strokeStyle = annotation.color;
        ctx.fillStyle = annotation.color;
        ctx.lineWidth = 3;
        ctx.beginPath();
        
        if (annotation.tool === 'circle') {
            ctx.arc(annotation.startX, annotation.startY, annotation.radius, 0, 2 * Math.PI);
            ctx.stroke();
        } else if (annotation.tool === 'arrow') {
            drawArrow(annotation.startX, annotation.startY, annotation.endX, annotation.endY);
            ctx.stroke();
        } else if (annotation.tool === 'text') {
            ctx.font = '16px Arial';
            ctx.fillText(annotation.text, annotation.x, annotation.y);
        }
    });
}

function clearAnnotations() {
    annotations = [];
    if (canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    updateAnnotationsData();
}

function setAnnotationColor(color) {
    currentColor = color;
}

function setAnnotationTool(tool) {
    currentTool = tool;
    document.querySelectorAll('.annotation-tool').forEach(btn => {
        btn.classList.remove('active', 'bg-cause-purple', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.add('active', 'bg-cause-purple', 'text-white');
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
}

function updateAnnotationsData() {
    document.getElementById('annotations-data').value = JSON.stringify(annotations);
}

// Handle form submission with CSRF token refresh
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                // Refresh CSRF token before submission
                const response = await fetch('/csrf-token', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = data.csrf_token;
                    }
                }
            } catch (error) {
                console.log('CSRF refresh failed, proceeding with existing token');
            }
            
            // Submit the form
            form.submit();
        });
    }
});
</script>
@endpush
