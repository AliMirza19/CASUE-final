@extends('layouts.dashboard')

@section('title', 'Chat with Student - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cause-purple to-purple-700 text-white p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('president.student-messages') }}" class="mr-4 hover:bg-purple-600 p-2 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div class="w-10 h-10 rounded-full bg-white text-cause-purple flex items-center justify-center text-lg font-semibold mr-3">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ $student->name }}</h1>
                            <p class="text-purple-100 text-sm">{{ $student->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                <div class="flex-shrink-0 {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
                                    <div class="w-8 h-8 rounded-full {{ $message->sender_id === auth()->id() ? 'bg-cause-purple' : 'bg-gray-400' }} flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($message->sender->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-cause-purple text-white' : 'bg-white text-gray-800' }} shadow">
                                        <p class="text-sm">{{ $message->message_text }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : '' }}">
                                        {{ $message->formatted_time }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="border-t bg-white p-4">
                <form id="message-form" class="flex space-x-3">
                    @csrf
                    <input 
                        type="text" 
                        id="message-input" 
                        name="message" 
                        placeholder="Type your message..." 
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                        required
                        maxlength="1000"
                    >
                    <button 
                        type="submit" 
                        class="bg-cause-purple hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200 flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const studentId = {{ $student->id }};

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    scrollToBottom();

    // Send message
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch(`/president/student-messages/${studentId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.success) {
                messageInput.value = '';
                await fetchMessages();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    // Fetch messages
    async function fetchMessages() {
        try {
            const response = await fetch(`/president/student-messages/${studentId}/fetch`);
            const data = await response.json();
            
            updateMessages(data.messages);
            scrollToBottom();
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    // Update messages display
    function updateMessages(messages) {
        if (messages.length === 0) {
            messagesContainer.innerHTML = `
                <div class="text-center text-gray-500 py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            `;
            return;
        }

        messagesContainer.innerHTML = messages.map(msg => {
            const isOwn = msg.sender_id === {{ auth()->id() }};
            return `
                <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="flex items-end ${isOwn ? 'flex-row-reverse' : ''}">
                            <div class="flex-shrink-0 ${isOwn ? 'ml-2' : 'mr-2'}">
                                <div class="w-8 h-8 rounded-full ${isOwn ? 'bg-cause-purple' : 'bg-gray-400'} flex items-center justify-center text-white text-sm font-semibold">
                                    ${msg.sender.name.charAt(0)}
                                </div>
                            </div>
                            <div>
                                <div class="px-4 py-2 rounded-lg ${isOwn ? 'bg-cause-purple text-white' : 'bg-white text-gray-800'} shadow">
                                    <p class="text-sm">${msg.message_text}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ${isOwn ? 'text-right' : ''}">
                                    ${msg.formatted_time}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Poll for new messages every 5 seconds
    setInterval(fetchMessages, 5000);
});
</script>
@endpush
