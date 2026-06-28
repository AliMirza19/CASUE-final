@php
    $tasks = \App\Models\Task::with(['assignedTo', 'event', 'media'])
        ->where('assigned_by_user_id', auth()->id());

    $assignedTasks = (clone $tasks)->where('status', '!=', 'approved')
        ->orderByRaw("FIELD(status, 'completed', 'in_progress', 'pending', 'rejected')")
        ->orderBy('updated_at', 'desc')
        ->get();

    $taskHistory = (clone $tasks)->where('status', 'approved')
        ->latest()
        ->take(10)
        ->get();
@endphp

<div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">📋 Tasks Assigned to Teams</h3>
            <p class="text-sm text-gray-500 mt-1">Review and manage the tasks you have assigned.</p>
        </div>
        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $assignedTasks->count() }} Total</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission / Feedback</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($assignedTasks as $task)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500 max-w-xs break-words">{{ $task->description }}</div>
                            @if($task->event)
                                <div class="mt-1"><span class="px-2 py-0.5 text-[10px] bg-blue-100 text-blue-800 rounded-full">{{ $task->event->title }}</span></div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-800 uppercase font-semibold">{{ $task->assigned_to_role }}</div>
                            @if($task->assignedTo)
                                <div class="text-xs text-gray-500">{{ $task->assignedTo->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($task->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Submitted</span>
                            @elseif($task->status === 'approved')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Approved</span>
                            @elseif($task->status === 'rejected')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                            @elseif($task->status === 'in_progress')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($task->submission_notes)
                                <div class="mb-2"><strong class="text-xs text-gray-500">Submission:</strong><br>{{ $task->submission_notes }}</div>
                            @endif
                            @if($task->submission_file)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($task->submission_file) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        View Attached File
                                    </a>
                                </div>
                            @endif
                            @if($task->before_image || $task->after_image)
                                <div class="mt-3 flex space-x-3">
                                    @if($task->before_image)
                                        <div class="flex-1">
                                            <p class="text-[9px] uppercase font-bold text-gray-400">Before</p>
                                            <img src="{{ Storage::url($task->before_image) }}" class="h-12 w-full object-cover rounded border mt-1">
                                        </div>
                                    @endif
                                    @if($task->after_image)
                                        <div class="flex-1">
                                            <p class="text-[9px] uppercase font-bold text-gray-400">After</p>
                                            <img src="{{ Storage::url($task->after_image) }}" class="h-12 w-full object-cover rounded border mt-1">
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($task->media->count() > 0)
                                <div class="mt-3">
                                    <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Uploaded Media ({{ $task->media->count() }})</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($task->media as $media)
                                            <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="block w-10 h-10 relative group" title="{{ $media->original_filename }}">
                                                @if($media->media_type === 'video')
                                                    <div class="w-full h-full bg-indigo-900 rounded flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                                    </div>
                                                @else
                                                    <img src="{{ Storage::url($media->file_path) }}" class="w-full h-full object-cover rounded border border-gray-200 hover:scale-110 transition-transform">
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($task->feedback)
                                <div><strong class="text-xs text-gray-500">Feedback:</strong><br>{{ $task->feedback }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(in_array($task->status, ['completed', 'rejected', 'approved']))
                                <button onclick="document.getElementById('review-modal-{{ $task->id }}').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Review
                                </button>

                                <!-- Review Modal -->
                                <div id="review-modal-{{ $task->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('review-modal-{{ $task->id }}').classList.add('hidden')"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Review Task: {{ $task->title }}</h3>
                                                    <div class="mt-4">
                                                        @if($task->submission_notes)
                                                            <div class="bg-gray-50 p-3 rounded text-sm text-gray-700 mb-4">
                                                                <strong>Team Submission Notes:</strong><br>
                                                                {{ $task->submission_notes }}
                                                            </div>
                                                        @else
                                                            <div class="bg-yellow-50 p-3 rounded text-sm text-yellow-700 mb-4">
                                                                No submission notes provided by the team.
                                                            </div>
                                                        @endif

                                                        @if($task->before_image || $task->after_image)
                                                            <div class="mb-4 grid grid-cols-2 gap-4">
                                                                @if($task->before_image)
                                                                    <div>
                                                                        <p class="text-xs font-bold text-gray-500 mb-1 tracking-wider uppercase">Before:</p>
                                                                        <a href="{{ Storage::url($task->before_image) }}" target="_blank">
                                                                            <img src="{{ Storage::url($task->before_image) }}" class="w-full rounded-lg border shadow-sm hover:opacity-90 transition">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if($task->after_image)
                                                                    <div>
                                                                        <p class="text-xs font-bold text-gray-500 mb-1 tracking-wider uppercase">After:</p>
                                                                        <a href="{{ Storage::url($task->after_image) }}" target="_blank">
                                                                            <img src="{{ Storage::url($task->after_image) }}" class="w-full rounded-lg border shadow-sm hover:opacity-90 transition">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                        @endif
                                                        
                                                        @if($task->media->count() > 0)
                                                            <div class="mb-4">
                                                                <p class="text-xs font-bold text-gray-500 mb-2 tracking-wider uppercase">Uploaded Media:</p>
                                                                <div class="grid grid-cols-4 gap-2">
                                                                    @foreach($task->media as $media)
                                                                        <div class="relative group">
                                                                            @if($media->media_type === 'video')
                                                                                <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="block aspect-square bg-indigo-900 rounded flex items-center justify-center">
                                                                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="block aspect-square">
                                                                                    <img src="{{ Storage::url($media->file_path) }}" class="w-full h-full object-cover rounded border shadow-sm">
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        @if($task->submission_file && preg_match('/\.(jpg|jpeg|png|gif)$/i', $task->submission_file))
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium text-gray-700 mb-2">Annotate Image</label>
                                                                <button type="button" onclick="openAnnotationModal('{{ Storage::url($task->submission_file) }}', {{ $task->id }})" class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-semibold py-2 px-3 rounded inline-flex items-center transition">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                    Draw / Add Notes to Image
                                                                </button>
                                                                <input type="hidden" name="annotated_image" id="annotated-input-{{ $task->id }}">
                                                                <div id="annotated-preview-container-{{ $task->id }}" class="mt-3 hidden bg-gray-50 p-2 rounded border border-gray-200">
                                                                    <p class="text-xs text-green-600 font-semibold mb-2">✓ Annotated Image Saved (will be sent with feedback)</p>
                                                                    <img id="annotated-preview-{{ $task->id }}" class="max-h-32 rounded border border-gray-300">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        <label class="block text-sm font-medium text-gray-700">Provide Feedback</label>
                                                        <textarea name="feedback" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $task->feedback }}</textarea>
                                                        
                                                        <div class="mt-4">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">Decision</label>
                                                            <div class="flex space-x-4">
                                                                <label class="inline-flex items-center">
                                                                    <input type="radio" name="status" value="approved" class="form-radio text-green-600" required @checked($task->status == 'approved')>
                                                                    <span class="ml-2 text-sm text-gray-700">Approve</span>
                                                                </label>
                                                                <label class="inline-flex items-center">
                                                                    <input type="radio" name="status" value="rejected" class="form-radio text-red-600" required @checked($task->status == 'rejected')>
                                                                    <span class="ml-2 text-sm text-gray-700">Reject / Request Revision</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Submit Review
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('review-modal-{{ $task->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Waiting for submission</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p>You haven't assigned any tasks yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($taskHistory->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 opacity-75 grayscale-[0.5] hover:grayscale-0 transition-all">
    <div class="px-6 py-3 bg-gray-50 flex justify-between items-center border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Task Assignment History</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <tbody class="divide-y divide-gray-100">
                @foreach($taskHistory as $history)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <div class="text-sm font-bold text-gray-700">{{ $history->title }}</div>
                            <div class="text-[10px] text-gray-400">Approved on {{ $history->updated_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $history->assigned_to_role }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                Approved
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Global Annotation Modal -->
<div id="annotation-modal" class="fixed inset-0 z-[60] hidden bg-gray-900 bg-opacity-95 flex flex-col">
    <div class="flex justify-between items-center p-4 bg-gray-800 text-white shadow-md">
        <h3 class="text-lg font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Draw on Image
        </h3>
        <div class="flex space-x-3 items-center">
            <input type="color" id="draw-color" value="#ff0000" class="w-8 h-8 rounded cursor-pointer border-none bg-transparent">
            <button type="button" onclick="clearCanvas()" class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 rounded text-sm font-medium transition">Clear</button>
            <button type="button" onclick="saveAnnotation()" class="px-4 py-1.5 bg-green-600 hover:bg-green-500 rounded text-sm font-medium shadow transition">Save & Attach</button>
            <button type="button" onclick="closeAnnotationModal()" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 rounded text-sm font-medium transition">Cancel</button>
        </div>
    </div>
    <div class="flex-1 overflow-auto flex justify-center items-center p-4" id="canvas-container">
        <canvas id="annotation-canvas" class="cursor-crosshair shadow-2xl border border-gray-600 max-w-full bg-black"></canvas>
    </div>
</div>

<script>
    let canvas = null;
    let ctx = null;
    let isDrawing = false;
    let currentTaskId = null;
    let baseImage = new Image();

    function initCanvas() {
        if (!canvas) {
            canvas = document.getElementById('annotation-canvas');
            ctx = canvas.getContext('2d');
            
            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
            
            // Touch events for mobile/tablets
            canvas.addEventListener('touchstart', handleTouchStart, {passive: false});
            canvas.addEventListener('touchmove', handleTouchMove, {passive: false});
            canvas.addEventListener('touchend', stopDrawing);
        }
    }

    function openAnnotationModal(imageUrl, taskId) {
        initCanvas();
        currentTaskId = taskId;
        
        baseImage.onload = function() {
            // Set canvas size to match image, but cap it if it's too huge
            let maxWidth = window.innerWidth * 0.9;
            let maxHeight = window.innerHeight * 0.8;
            
            let width = baseImage.width;
            let height = baseImage.height;
            
            if (width > maxWidth) {
                height = height * (maxWidth / width);
                width = maxWidth;
            }
            if (height > maxHeight) {
                width = width * (maxHeight / height);
                height = maxHeight;
            }
            
            canvas.width = width;
            canvas.height = height;
            
            ctx.drawImage(baseImage, 0, 0, width, height);
            
            // Setup drawing style
            ctx.lineWidth = 4;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            document.getElementById('annotation-modal').classList.remove('hidden');
        };
        // Adding timestamp to prevent CORS/caching issues with canvas
        baseImage.src = imageUrl + '?t=' + new Date().getTime();
    }

    function closeAnnotationModal() {
        document.getElementById('annotation-modal').classList.add('hidden');
    }

    function startDrawing(e) {
        isDrawing = true;
        draw(e);
    }
    
    function handleTouchStart(e) {
        if(e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent("mousedown", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }
    }
    
    function handleTouchMove(e) {
        if(e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent("mousemove", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }
    }

    function draw(e) {
        if (!isDrawing) return;
        
        const rect = canvas.getBoundingClientRect();
        
        // Calculate scale (in case CSS resizes the canvas)
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        const x = (e.clientX - rect.left) * scaleX;
        const y = (e.clientY - rect.top) * scaleY;
        
        ctx.strokeStyle = document.getElementById('draw-color').value;
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function stopDrawing() {
        isDrawing = false;
        ctx.beginPath();
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(baseImage, 0, 0, canvas.width, canvas.height);
    }

    function saveAnnotation() {
        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById(`annotated-input-${currentTaskId}`).value = dataUrl;
        
        const previewImg = document.getElementById(`annotated-preview-${currentTaskId}`);
        previewImg.src = dataUrl;
        document.getElementById(`annotated-preview-container-${currentTaskId}`).classList.remove('hidden');
        
        closeAnnotationModal();
    }
</script>
