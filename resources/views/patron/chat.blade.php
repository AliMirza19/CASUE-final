@extends('layouts.dashboard')

@section('title', 'Direct Chat - Patron Dashboard')
@section('page-title', 'Direct Chat with HOD')
@section('page-description', 'Communicate directly with the assigned HOD')

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
    <!-- WhatsApp Style Chat Container -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: 70vh;">
        
        <!-- Chat Header (WhatsApp Style) -->
        <div class="bg-green-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-lg">{{ substr($hod->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold">{{ $hod->name }}</h3>
                    <p class="text-sm text-green-100">HOD - {{ $activeTerm ? $activeTerm->term_name : 'Current Term' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                    <span class="w-2 h-2 bg-green-300 rounded-full mr-1 animate-pulse"></span>
                    Online
                </span>
                
                <button onclick="aiSummarizeChat()" id="btnSummarize" class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center text-xs font-bold shadow" title="AI Summarize Meeting Minutes">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Action Items
                </button>

                <button onclick="refreshMessages()" class="p-2 text-green-100 hover:text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container (WhatsApp Style) -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50" style="height: calc(70vh - 140px); background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 100&quot;><defs><pattern id=&quot;chat-bg&quot; x=&quot;0&quot; y=&quot;0&quot; width=&quot;20&quot; height=&quot;20&quot; patternUnits=&quot;userSpaceOnUse&quot;><circle cx=&quot;10&quot; cy=&quot;10&quot; r=&quot;1&quot; fill=&quot;%23e5e7eb&quot; opacity=&quot;0.3&quot;/></pattern></defs><rect width=&quot;100&quot; height=&quot;100&quot; fill=&quot;url(%23chat-bg)&quot;/></svg>');">
            
            @forelse($messages as $message)
                <div class="mb-4 flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md">
                        @if($message->sender_id === auth()->id())
                            <!-- Own Message (Right - Green like WhatsApp) -->
                            <div class="bg-green-500 text-white rounded-lg px-4 py-2 shadow-md relative">
                                <p class="text-sm">{{ $message->message_text }}</p>
                                <div class="flex items-center justify-end mt-1 space-x-1">
                                    <span class="text-xs text-green-100">{{ $message->formatted_time }}</span>
                                    <!-- Read Status (WhatsApp Style) -->
                                    @if($message->is_read)
                                        <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        <svg class="w-4 h-4 text-blue-200 -ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <!-- Message Tail -->
                                <div class="absolute top-0 right-0 w-0 h-0 border-l-8 border-l-green-500 border-t-8 border-t-transparent transform translate-x-2"></div>
                            </div>
                        @else
                            <!-- Received Message (Left - White like WhatsApp) -->
                            <div class="bg-white rounded-lg px-4 py-2 shadow-md relative">
                                <p class="text-xs text-gray-500 mb-1 font-medium">{{ $message->sender->name }}</p>
                                <p class="text-sm text-gray-900">{{ $message->message_text }}</p>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $message->formatted_time }}</p>
                                <!-- Message Tail -->
                                <div class="absolute top-0 left-0 w-0 h-0 border-r-8 border-r-white border-t-8 border-t-transparent transform -translate-x-2"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                        <p class="text-gray-500">Start a conversation with {{ $hod->name }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Message Input (WhatsApp Style) -->
        <div class="bg-gray-100 p-4 border-t">
            <!-- Simple Test Button -->

            
            <form id="messageForm" onsubmit="return false;" class="flex items-center space-x-3">
                @csrf
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="messageInput" 
                        name="message" 
                        placeholder="Type a message..." 
                        class="w-full px-4 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"
                        maxlength="1000"
                        autocomplete="off"
                    >
                </div>
                <button 
                    type="button" 
                    id="sendButton"
                    onclick="sendMessage()"
                    class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-full transition-colors duration-200 shadow-lg"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let isLoading = false;

    // AI Summarize Chat
    window.aiSummarizeChat = function() {
        if (isLoading) return;
        
        const btn = document.getElementById('btnSummarize');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = 'Thinking...';
        btn.disabled = true;
        isLoading = true;

        fetch('{{ route("patron.chat.summarize") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("AI Meeting Minutes:\n\n" + data.summary);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to generate AI summary.');
        })
        .finally(() => {
            isLoading = false;
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    };

    // Auto-scroll to bottom
    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Send message function
    window.sendMessage = function() {
        if (isLoading) return;
        
        const input = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Show loading state
        isLoading = true;
        sendButton.disabled = true;
        const originalBtnContent = sendButton.innerHTML;
        sendButton.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        
        // Send to server
        fetch('{{ route("patron.chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                addMessageToChat(data.message);
                scrollToBottom();
            } else {
                alert('Error: ' + (data.message || 'Failed to send message'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending message');
        })
        .finally(() => {
            isLoading = false;
            sendButton.disabled = false;
            sendButton.innerHTML = originalBtnContent;
            input.focus();
        });
    }
    
    function addMessageToChat(messageData) {
        const container = document.getElementById('messagesContainer');
        
        // Remove empty state if exists
        const emptyState = container.querySelector('.flex.items-center.justify-center');
        if (emptyState) {
            emptyState.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 flex justify-end';
        
        // Escape HTML to prevent XSS
        const messageText = messageData.text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="bg-green-500 text-white rounded-lg px-4 py-2 shadow-md relative">
                    <p class="text-sm">${messageText}</p>
                    <div class="flex items-center justify-end mt-1 space-x-1">
                        <span class="text-xs text-green-100">${messageData.formatted_time}</span>
                        <svg class="w-4 h-4 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                    </div>
                    <div class="absolute top-0 right-0 w-0 h-0 border-l-8 border-l-green-500 border-t-8 border-t-transparent transform translate-x-2"></div>
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('messageInput');
        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    window.sendMessage();
                }
            });
            input.focus();
        }
        scrollToBottom();
    });
</script>
@endpush
