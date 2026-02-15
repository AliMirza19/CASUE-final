@extends('layouts.dashboard')

@section('title', 'Direct Chat - HOD Dashboard')
@section('page-title', 'Direct Chat with Patron')
@section('page-description', 'Communicate directly with the assigned Patron')

@section('sidebar')
    <a href="{{ route('hod.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('hod.manage-patron') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Manage Patron
    </a>
    <a href="{{ route('hod.budget') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Manage Budget
    </a>
    <a href="{{ route('hod.analytics') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Analytics
    </a>
    <a href="{{ route('hod.financial-reports') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Financial Reports
    </a>
    <a href="{{ route('hod.chat') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Direct Chat
        @if($unreadCount > 0)
            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
        @endif
    </a>
@endsection

@section('content')
    <!-- WhatsApp Style Chat Container -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: 70vh;">
        
        <!-- Chat Header (WhatsApp Style) -->
        <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-lg">{{ substr($patron->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold">{{ $patron->name }}</h3>
                    <p class="text-sm text-blue-100">Patron - {{ $activeTerm ? $activeTerm->term_name : 'Current Term' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                    <span class="w-2 h-2 bg-blue-300 rounded-full mr-1 animate-pulse"></span>
                    Online
                </span>
                <button onclick="refreshMessages()" class="p-2 text-blue-100 hover:text-white rounded-lg hover:bg-blue-700 transition">
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
                            <!-- Own Message (Right - Blue like HOD) -->
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 shadow-md relative">
                                <p class="text-sm">{{ $message->message_text }}</p>
                                <div class="flex items-center justify-end mt-1 space-x-1">
                                    <span class="text-xs text-blue-100">{{ $message->formatted_time }}</span>
                                    <!-- Read Status (WhatsApp Style) -->
                                    @if($message->is_read)
                                        <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                        <svg class="w-4 h-4 text-blue-200 -ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <!-- Message Tail -->
                                <div class="absolute top-0 right-0 w-0 h-0 border-l-8 border-l-blue-500 border-t-8 border-t-transparent transform translate-x-2"></div>
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
                        <p class="text-gray-500">Start a conversation with {{ $patron->name }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Message Input (WhatsApp Style) -->
        <div class="bg-gray-100 p-4 border-t">
            <form id="messageForm" onsubmit="return false;" class="flex items-center space-x-3">
                @csrf
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="messageInput" 
                        name="message" 
                        placeholder="Type a message..." 
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                        maxlength="1000"
                        required
                        autocomplete="off"
                    >
                    <!-- Emoji Button (Optional) -->
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                </div>
                <button 
                    type="button" 
                    id="sendButton"
                    onclick="sendMessage()"
                    class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full transition-colors duration-200 shadow-lg"
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
    
    // Auto-scroll to bottom
    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Handle message sending
    window.sendMessage = function() {
        if (isLoading) return;
        
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Show loading state
        isLoading = true;
        sendButton.disabled = true;
        const originalBtnContent = sendButton.innerHTML;
        sendButton.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        
        // Send message via AJAX
        fetch('{{ route("hod.chat.send") }}', {
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.message) {
                messageInput.value = '';
                addMessageToChat(data.message);
                scrollToBottom();
                
                // Refresh messages after a short delay to ensure sync
                setTimeout(refreshMessages, 1000);
            } else {
                alert('Error: ' + (data.message || 'Failed to send message'));
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error sending message');
        })
        .finally(() => {
            isLoading = false;
            sendButton.disabled = false;
            sendButton.innerHTML = originalBtnContent;
            messageInput.focus();
        });
    };

    // Add message to chat UI (WhatsApp style)
    function addMessageToChat(messageData) {
        const container = document.getElementById('messagesContainer');
        
        // Remove empty state if it exists
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
                <div class="bg-blue-500 text-white rounded-lg px-4 py-2 shadow-md relative">
                    <p class="text-sm">${messageText}</p>
                    <div class="flex items-center justify-end mt-1 space-x-1">
                        <span class="text-xs text-blue-100">${messageData.formatted_time}</span>
                        <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                        </svg>
                    </div>
                    <div class="absolute top-0 right-0 w-0 h-0 border-l-8 border-l-blue-500 border-t-8 border-t-transparent transform translate-x-2"></div>
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
    }

    // Refresh messages
    function refreshMessages() {
        if (isLoading) return;

        fetch('{{ route("hod.chat.messages") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const currentCount = document.querySelectorAll('#messagesContainer .mb-4').length;
                if (data.messages.length > currentCount) {
                    updateMessagesDisplay(data.messages);
                    scrollToBottom();
                    showNotification('New message received');
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing messages:', error);
        });
    }

    // Update messages display
    function updateMessagesDisplay(messages) {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = '';
        
        if (messages.length === 0) {
            container.innerHTML += `
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                        <p class="text-gray-500">Start a conversation with {{ $patron->name }}</p>
                    </div>
                </div>
            `;
        } else {
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-4 flex ${msg.is_own_message ? 'justify-end' : 'justify-start'}`;
                
                if (msg.is_own_message) {
                    messageDiv.innerHTML = `
                        <div class="max-w-xs lg:max-w-md">
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 shadow-md relative">
                                <p class="text-sm">${msg.message_text}</p>
                                <div class="flex items-center justify-end mt-1 space-x-1">
                                    <span class="text-xs text-blue-100">${msg.formatted_time}</span>
                                    ${msg.read_status === 'read' ? 
                                        '<svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg><svg class="w-4 h-4 text-blue-200 -ml-2" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>' :
                                        '<svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>'
                                    }
                                </div>
                                <div class="absolute top-0 right-0 w-0 h-0 border-l-8 border-l-blue-500 border-t-8 border-t-transparent transform translate-x-2"></div>
                            </div>
                        </div>
                    `;
                } else {
                    messageDiv.innerHTML = `
                        <div class="max-w-xs lg:max-w-md">
                            <div class="bg-white rounded-lg px-4 py-2 shadow-md relative">
                                <p class="text-xs text-gray-500 mb-1 font-medium">${msg.sender_name}</p>
                                <p class="text-sm text-gray-900">${msg.message_text}</p>
                                <p class="text-xs text-gray-500 mt-1 text-right">${msg.formatted_time}</p>
                                <div class="absolute top-0 left-0 w-0 h-0 border-r-8 border-r-white border-t-8 border-t-transparent transform -translate-x-2"></div>
                            </div>
                        </div>
                    `;
                }
                
                container.appendChild(messageDiv);
            });
        }
    }

    // Show notification
    function showNotification(message) {
        const existing = document.querySelector('.chat-notification');
        if (existing) existing.remove();

        const notification = document.createElement('div');
        notification.className = 'chat-notification fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.focus();
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    window.sendMessage();
                }
            });
        }
        
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                window.sendMessage();
            });
        }
    });

    // Auto-refresh messages every 5 seconds
    setInterval(refreshMessages, 5000);
</script>
@endpush
