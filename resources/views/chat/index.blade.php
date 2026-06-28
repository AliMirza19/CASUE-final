@extends('layouts.dashboard')

@section('title', 'Event Communication - CAUSE Smart Society')
@section('page-title', 'Society Communication')
@section('page-description', 'Real-time communication for event teams')

@section('content')
<div class="flex h-[calc(100vh-12rem)] bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Sidebar: Groups List -->
    <div class="w-1/3 border-r border-gray-100 bg-gray-50/50 flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-white">
            <h3 class="text-lg font-bold text-gray-800">My Groups</h3>
            <p class="text-xs text-gray-500">Communication for approved events</p>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @forelse($groups as $g)
                <a href="{{ route('chat.show', $g->id) }}" 
                   class="flex items-center px-4 py-4 border-b border-gray-50 hover:bg-white transition-all {{ (isset($group) && $group->id == $g->id) ? 'bg-white border-l-4 border-l-cause-purple' : '' }}">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ substr($g->name, 6, 1) }}
                    </div>
                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between items-baseline">
                            <h4 class="text-sm font-semibold text-gray-800 truncate">{{ $g->name }}</h4>
                            @if($g->messages->first())
                                <span class="text-[10px] text-gray-400">{{ $g->messages->first()->created_at->format('H:i') }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-1">
                            @if($g->messages->first())
                                <span class="font-medium">{{ $g->messages->first()->user->name }}:</span> {{ $g->messages->first()->message }}
                            @else
                                <span class="italic">No messages yet</span>
                            @endif
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">No active chat groups</p>
                    <p class="text-gray-400 text-xs mt-1">Groups are created when events are approved.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-white">
        @if(isset($group))
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white shadow-sm z-10">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                        {{ substr($group->name, 6, 1) }}
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-gray-800">{{ $group->name }}</h3>
                        <p class="text-[10px] text-gray-500">{{ $group->members->count() }} members in this group</p>
                    </div>
                </div>
                <div class="flex -space-x-2">
                    @foreach($group->members->take(5) as $member)
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600" title="{{ $member->user->name }}">
                            {{ substr($member->user->name, 0, 1) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50/30 space-y-4" id="messages-container">
                @forelse($group->messages as $msg)
                    <div class="flex {{ $msg->user_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%]">
                            @if($msg->user_id != Auth::id())
                                <p class="text-[10px] font-bold text-gray-500 ml-1 mb-1">{{ $msg->user->name }} ({{ strtoupper($msg->user->role) }})</p>
                            @endif
                            <div class="px-4 py-2 rounded-2xl shadow-sm {{ $msg->user_id == Auth::id() ? 'bg-cause-purple text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none border border-gray-100' }}">
                                @if($msg->image_path)
                                    <div class="mb-2 relative group">
                                        <img src="{{ Storage::url($msg->image_path) }}" class="rounded-lg max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" 
                                             onclick="openImageModal('{{ Storage::url($msg->image_path) }}', {{ $msg->id }}, {{ Auth::user()->role === 'president' ? 'true' : 'false' }})">
                                        
                                        @if(Auth::user()->role === 'president')
                                            <button onclick="openImageModal('{{ Storage::url($msg->image_path) }}', {{ $msg->id }}, true)" 
                                                    class="absolute top-2 right-2 p-1.5 bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($msg->message)
                                    <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                                @endif

                                <p class="text-[9px] mt-1 text-right {{ $msg->user_id == Auth::id() ? 'text-purple-100' : 'text-gray-400' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full inline-block text-xs font-medium">
                            Group created. Start the conversation!
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Input Area -->
            <div class="p-4 border-t border-gray-100 bg-white">
                <form action="{{ route('chat.send', $group->id) }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-2">
                    @csrf
                    <div class="flex items-center space-x-2 mr-2">
                        <label class="cursor-pointer p-2 bg-gray-100 text-gray-500 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </label>
                    </div>
                    <div class="flex-1">
                        <div id="image-preview-container" class="hidden mb-2 relative inline-block">
                            <img id="image-preview" src="#" class="h-20 w-auto rounded-lg shadow-md">
                            <button type="button" onclick="clearImagePreview()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <textarea name="message" rows="1" 
                                  class="w-full px-4 py-2 bg-gray-100 border-none rounded-2xl focus:ring-2 focus:ring-cause-purple resize-none text-sm"
                                  placeholder="Type a message..."></textarea>
                    </div>
                    <button type="submit" class="p-2 bg-cause-purple text-white rounded-full hover:bg-cause-purple-dark transition-colors shadow-lg">
                        <svg class="w-6 h-6 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center p-8 bg-gray-50/20">
                <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Select a group to start chatting</h3>
                <p class="text-gray-500 mt-2 text-center max-w-sm">Connect with your team members and coordinate event details in real-time.</p>
            </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div id="image-modal" class="fixed inset-0 z-50 hidden bg-black/90 flex flex-col">
    <div class="p-4 flex justify-between items-center text-white">
        <h3 class="font-bold">Image View</h3>
        <div class="flex items-center space-x-4">
            <div id="annotation-tools" class="hidden flex items-center space-x-2 bg-gray-800 rounded-lg p-1">
                <button onclick="setTool('pen')" id="btn-pen" class="p-2 rounded hover:bg-gray-700 bg-cause-purple" title="Pen Tool">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button onclick="clearCanvas()" class="p-2 rounded hover:bg-gray-700" title="Clear">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <button onclick="saveAnnotation()" class="p-2 rounded bg-green-600 hover:bg-green-700 ml-2" title="Save Marks">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>
            <button onclick="closeImageModal()" class="p-2 hover:bg-white/10 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    <div class="flex-1 relative flex items-center justify-center overflow-hidden">
        <canvas id="annotation-canvas" class="max-w-full max-h-full object-contain"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto scroll to bottom of messages
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Image Preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImagePreview() {
        const input = document.querySelector('input[name="image"]');
        input.value = '';
        document.getElementById('image-preview-container').classList.add('hidden');
    }

    // Modal & Annotation
    let canvas, ctx, isDrawing = false, currentMessageId = null;
    let bgImage = new Image();

    function initCanvas() {
        canvas = document.getElementById('annotation-canvas');
        ctx = canvas.getContext('2d');
        
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        
        // Touch support
        canvas.addEventListener('touchstart', e => { e.preventDefault(); startDrawing(e.touches[0]); });
        canvas.addEventListener('touchmove', e => { e.preventDefault(); draw(e.touches[0]); });
        canvas.addEventListener('touchend', e => { stopDrawing(); });
    }

    function openImageModal(url, messageId, canAnnotate) {
        currentMessageId = messageId;
        const modal = document.getElementById('image-modal');
        modal.classList.remove('hidden');
        
        if (canAnnotate) {
            document.getElementById('annotation-tools').classList.remove('hidden');
        } else {
            document.getElementById('annotation-tools').classList.add('hidden');
        }

        if (!canvas) initCanvas();

        bgImage.onload = function() {
            // Set canvas size to match image aspect ratio while fitting screen
            const maxWidth = window.innerWidth * 0.9;
            const maxHeight = window.innerHeight * 0.8;
            let width = bgImage.width;
            let height = bgImage.height;

            if (width > maxWidth) {
                height *= maxWidth / width;
                width = maxWidth;
            }
            if (height > maxHeight) {
                width *= maxHeight / height;
                height = maxHeight;
            }

            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(bgImage, 0, 0, width, height);
            
            // Load existing annotations if any (placeholder logic)
            // loadAnnotations(messageId);
        };
        bgImage.src = url;
    }

    function closeImageModal() {
        document.getElementById('image-modal').classList.add('hidden');
    }

    function startDrawing(e) {
        isDrawing = true;
        draw(e);
    }

    function draw(e) {
        if (!isDrawing) return;
        
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || e.pageX) - rect.left;
        const y = (e.clientY || e.pageY) - rect.top;

        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#ef4444'; // Red for mistakes

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
        ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);
    }

    async function saveAnnotation() {
        const dataUrl = canvas.toDataURL('image/png');
        
        try {
            const response = await fetch(`/chat/messages/${currentMessageId}/annotate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ image: dataUrl })
            });

            if (response.ok) {
                alert('Marks saved successfully!');
                location.reload();
            } else {
                alert('Failed to save marks.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving.');
        }
    }

    // Polling for new messages
    const groupId = @json(isset($group) ? $group->id : null);
    const authId = @json(auth()->id());
    const isPresident = @json(auth()->user()->role === 'president');
    
    if (groupId) {
        setInterval(async () => {
            try {
                const response = await fetch(`/chat/groups/${groupId}/messages`);
                const data = await response.json();
                
                if (data.success) {
                    const container = document.getElementById('messages-container');
                    const currentCount = container.querySelectorAll('.flex').length;
                    
                    if (data.messages.length > currentCount) {
                        updateMessagesList(data.messages);
                        container.scrollTop = container.scrollHeight;
                    }
                }
            } catch (e) {
                console.error('Polling error:', e);
            }
        }, 5000);
    }

    function updateMessagesList(messages) {
        const container = document.getElementById('messages-container');
        container.innerHTML = '';
        
        if (messages.length === 0) {
            container.innerHTML = `<div class="text-center py-20"><div class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full inline-block text-xs font-medium">Group created. Start the conversation!</div></div>`;
            return;
        }

        messages.forEach(msg => {
            const isOwn = msg.user_id == authId;
            const div = document.createElement('div');
            div.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
            
            let content = `
                <div class="max-w-[70%]">
                    ${!isOwn ? `<p class="text-[10px] font-bold text-gray-500 ml-1 mb-1">${msg.user_name} (${msg.user_role})</p>` : ''}
                    <div class="px-4 py-2 rounded-2xl shadow-sm ${isOwn ? 'bg-cause-purple text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none border border-gray-100'}">
                        ${msg.image_url ? `
                            <div class="mb-2 relative group">
                                <img src="${msg.image_url}" class="rounded-lg max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity" 
                                     onclick="openImageModal('${msg.image_url}', ${msg.id}, ${isPresident ? 'true' : 'false'})">
                            </div>
                        ` : ''}
                        ${msg.message ? `<p class="text-sm leading-relaxed">${msg.message}</p>` : ''}
                        <p class="text-[9px] mt-1 text-right ${isOwn ? 'text-purple-100' : 'text-gray-400'}">${msg.time}</p>
                    </div>
                </div>
            `;
            div.innerHTML = content;
            container.appendChild(div);
        });
    }
</script>
@endpush
