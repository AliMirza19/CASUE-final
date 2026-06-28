@extends('layouts.dashboard')

@section('title', 'Direct Chat - CAUSE Smart Society')
@section('page-title', 'Direct Chat')
@section('page-description', 'Communicate directly with society members')

@section('sidebar')
    @php
        $role = auth()->user()->role;
        $dashboardRoute = $role . '.dashboard';
        // Handle special cases if any, but following standard pattern
        if ($role === 'president') $dashboardRoute = 'president.dashboard';
        elseif ($role === 'gd') $dashboardRoute = 'gd.dashboard';
        elseif ($role === 'photo') $dashboardRoute = 'photo.dashboard';
        elseif ($role === 'video') $dashboardRoute = 'video.dashboard';
        elseif ($role === 'smt') $dashboardRoute = 'smt.dashboard';
        elseif ($role === 'doc') $dashboardRoute = 'doc.dashboard';
        elseif ($role === 'deco') $dashboardRoute = 'deco.dashboard';
        elseif ($role === 'vc') $dashboardRoute = 'vc.dashboard';
        elseif ($role === 'sa') $dashboardRoute = 'student.dashboard'; // SA redirecting to student for now
    @endphp
    
    <a href="{{ route($dashboardRoute) }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('direct-chat.index') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 mb-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Direct Messages
    </a>
@endsection

@section('content')
<div class="flex bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200" style="height: calc(100vh - 200px);">
    <!-- Contacts Sidebar -->
    <div class="w-full md:w-80 border-r border-gray-200 flex flex-col bg-gray-50 {{ $selectedUser ? 'hidden md:flex' : 'flex' }}">
        <div class="p-4 border-b border-gray-200 bg-white">
            <h3 class="font-bold text-gray-800 text-lg">Conversations</h3>
            <div class="mt-2 relative">
                <input type="text" placeholder="Search contacts..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-none rounded-lg text-sm focus:ring-2 focus:ring-cause-purple">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @forelse($contacts as $contact)
                <a href="{{ route('direct-chat.index', ['user_id' => $contact['id']]) }}" 
                   class="flex items-center px-4 py-4 hover:bg-gray-100 transition-colors border-b border-gray-100 {{ $selectedUser && $selectedUser->id == $contact['id'] ? 'bg-purple-50 border-l-4 border-l-cause-purple' : '' }}">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cause-purple to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            {{ substr($contact['name'], 0, 1) }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $contact['name'] }}</h4>
                        </div>
                        <p class="text-xs text-cause-purple font-semibold uppercase tracking-wider">{{ $contact['role_name'] }}</p>
                    </div>
                    @if($contact['unread_count'] > 0)
                        <div class="ml-2 bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                            {{ $contact['unread_count'] }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500 text-sm">No contacts available for your role.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col bg-white {{ $selectedUser ? 'flex' : 'hidden md:flex' }}">
        @if($selectedUser)
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white z-10 shadow-sm">
                <div class="flex items-center">
                    <a href="{{ route('direct-chat.index') }}" class="md:hidden mr-4 text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div class="w-10 h-10 rounded-full bg-cause-purple flex items-center justify-center text-white font-bold">
                        {{ substr($selectedUser->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <h3 class="font-bold text-gray-900 leading-tight">{{ $selectedUser->name }}</h3>
                        <p class="text-xs text-green-500 font-medium flex items-center">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                            Online &bull; {{ $selectedUser->getDisplayRole() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="refreshMessages()" class="p-2 text-gray-400 hover:text-cause-purple transition-colors rounded-lg hover:bg-gray-100" title="Refresh">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages List -->
            <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#f8f9fa]" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-blend-mode: overlay;">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] {{ $message->sender_id === auth()->id() ? 'bg-cause-purple text-white rounded-l-xl rounded-tr-xl' : 'bg-white text-gray-800 rounded-r-xl rounded-tl-xl shadow-sm' }} px-4 py-2 relative">
                            <p class="text-sm leading-relaxed">{{ $message->message_text }}</p>
                            <div class="flex items-center justify-end mt-1 space-x-1 opacity-70">
                                <span class="text-[10px]">{{ $message->formatted_time }}</span>
                                @if($message->sender_id === auth()->id())
                                    @if($message->is_read)
                                        <svg class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/><path d="M10.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" class="-ml-2"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t border-gray-200">
                <form id="directChatForm" class="flex items-center space-x-3">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea id="messageInput" rows="1" placeholder="Type a message..." class="w-full pl-4 pr-12 py-3 bg-gray-100 border-none rounded-2xl text-sm focus:ring-2 focus:ring-cause-purple resize-none"></textarea>
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-cause-purple">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                    </div>
                    <button type="submit" class="p-3 bg-cause-purple text-white rounded-full shadow-lg hover:bg-indigo-700 transition-all transform hover:scale-105">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <!-- No Conversation Selected -->
            <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 text-center p-8">
                <div class="w-24 h-24 bg-white rounded-full shadow-md flex items-center justify-center mb-6 border border-gray-100">
                    <svg class="w-12 h-12 text-cause-purple opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Direct Messages</h3>
                <p class="text-gray-500 max-w-xs mx-auto">Select a team member from the list to start a private conversation.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    @foreach($contacts->take(4) as $contact)
                        <a href="{{ route('direct-chat.index', ['user_id' => $contact['id']]) }}" class="flex flex-col items-center group">
                            <div class="w-12 h-12 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:border-cause-purple transition-all mb-1">
                                <span class="text-gray-400 group-hover:text-cause-purple font-bold">{{ substr($contact['name'], 0, 1) }}</span>
                            </div>
                            <span class="text-[10px] text-gray-500 font-medium truncate max-w-[60px]">{{ explode(' ', $contact['name'])[0] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const selectedUserId = @json($selectedUser ? $selectedUser->id : null);
    let isLoading = false;

    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    if (selectedUserId) {
        document.addEventListener('DOMContentLoaded', () => {
            scrollToBottom();
            
            const form = document.getElementById('directChatForm');
            const input = document.getElementById('messageInput');
            
            input.focus();
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = input.value.trim();
                if (!message || isLoading) return;
                
                isLoading = true;
                input.value = '';
                input.style.height = 'auto';
                
                try {
                    const response = await fetch(`/direct-chat/send/${selectedUserId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        appendMessage(data.message);
                        scrollToBottom();
                    }
                } catch (err) {
                    console.error('Send error:', err);
                } finally {
                    isLoading = false;
                }
            });

            // Auto-grow textarea
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Enter to send
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // Refresh messages every 3 seconds
            setInterval(refreshMessages, 3000);
        });
    }

    async function refreshMessages() {
        if (!selectedUserId || isLoading) return;
        
        try {
            const response = await fetch(`/direct-chat/messages/${selectedUserId}`);
            const data = await response.json();
            
            if (data.success) {
                const container = document.getElementById('messagesContainer');
                const currentCount = container.querySelectorAll('.flex').length;
                
                if (data.messages.length > currentCount) {
                    updateMessagesList(data.messages);
                    scrollToBottom();
                }
            }
        } catch (err) {
            console.error('Refresh error:', err);
        }
    }

    function appendMessage(msg) {
        const container = document.getElementById('messagesContainer');
        const div = document.createElement('div');
        div.className = 'flex justify-end';
        div.innerHTML = `
            <div class="max-w-[75%] bg-cause-purple text-white rounded-l-xl rounded-tr-xl px-4 py-2 relative shadow-md">
                <p class="text-sm leading-relaxed">${msg.text}</p>
                <div class="flex items-center justify-end mt-1 space-x-1 opacity-70">
                    <span class="text-[10px]">${msg.formatted_time}</span>
                    <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    function updateMessagesList(messages) {
        const container = document.getElementById('messagesContainer');
        const currentUserId = {{ auth()->id() }};
        
        container.innerHTML = '';
        messages.forEach(msg => {
            const isOwn = msg.is_own_message;
            const div = document.createElement('div');
            div.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
            
            div.innerHTML = `
                <div class="max-w-[75%] ${isOwn ? 'bg-cause-purple text-white rounded-l-xl rounded-tr-xl' : 'bg-white text-gray-800 rounded-r-xl rounded-tl-xl shadow-sm'} px-4 py-2 relative">
                    <p class="text-sm leading-relaxed">${msg.message_text}</p>
                    <div class="flex items-center justify-end mt-1 space-x-1 opacity-70">
                        <span class="text-[10px]">${msg.formatted_time}</span>
                        ${isOwn ? (msg.read_status === 'read' ? 
                            '<svg class="w-3 h-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/><path d="M10.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" class="-ml-2"/></svg>' :
                            '<svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>') : ''}
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }
</script>
@endpush
@endsection
