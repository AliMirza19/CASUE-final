@extends('layouts.dashboard')

@section('title', 'Message President - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cause-purple to-purple-700 text-white p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('student.overview') }}" class="mr-4 hover:bg-purple-600 p-2 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold">Message President</h1>
                            @if($president)
                                <p class="text-purple-100 text-sm">Chat with {{ $president->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($error))
                <div class="p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">{{ $error }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($president)
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
            @endif
        </div>
    </div>
</div>
@endsection

@if($president)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');

    if (!messagesContainer || !messageForm || !messageInput) return;

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
            const response = await fetch('{{ route("student.messages.send") }}', {
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
            const response = await fetch('{{ route("student.messages.fetch") }}');
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
@endif
