@if(auth()->user()->teams->count() > 0)
    @php $team = auth()->user()->teams->latest()->first(); @endphp
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col h-[500px]">
        <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center">
            <div>
                <h4 class="font-bold">Team Chat: {{ $team->name }}</h4>
                <p class="text-[10px] opacity-80 uppercase">{{ $team->type }} Room</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-xs font-medium">Live</span>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chatMessages" class="flex-grow overflow-y-auto p-6 space-y-4 bg-gray-50">
            <!-- Messages will be loaded here -->
            <div class="text-center py-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <form id="chatForm" class="flex items-end gap-2">
                <div class="flex-grow relative">
                    <textarea id="messageInput" placeholder="Type a message..." class="w-full border-gray-200 rounded-xl py-2 pl-4 pr-10 focus:ring-indigo-500 focus:border-indigo-500 resize-none text-sm" rows="1"></textarea>
                    <label class="absolute right-3 bottom-2 cursor-pointer text-gray-400 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="file" id="imageInput" class="hidden" accept="image/*">
                    </label>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-xl transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <div id="imagePreview" class="hidden mt-2 relative inline-block">
                <img src="" class="h-16 w-16 object-cover rounded-lg border">
                <button onclick="clearImage()" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
    </div>

    <script>
        const teamId = {{ $team->id }};
        const currentUserId = {{ auth()->id() }};
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        // Poll for new messages every 3 seconds
        setInterval(fetchMessages, 3000);
        fetchMessages();

        function fetchMessages() {
            fetch(`/teams/${teamId}/messages`)
                .then(res => res.json())
                .then(data => {
                    const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop <= chatMessages.clientHeight + 100;
                    
                    chatMessages.innerHTML = data.map(msg => `
                        <div class="flex ${msg.sender_id === currentUserId ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-[80%] ${msg.sender_id === currentUserId ? 'bg-indigo-600 text-white' : 'bg-white text-gray-800'} rounded-2xl px-4 py-2 shadow-sm border border-gray-100">
                                <p class="text-[10px] font-bold mb-1 opacity-70">${msg.sender.name}</p>
                                ${msg.message ? `<p class="text-sm">${msg.message}</p>` : ''}
                                ${msg.image_path ? `<img src="/storage/${msg.image_path}" class="mt-2 rounded-lg max-w-full h-auto cursor-pointer" onclick="window.open(this.src)">` : ''}
                                <p class="text-[8px] mt-1 opacity-50 text-right">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            </div>
                        </div>
                    `).join('');

                    if (isAtBottom) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });
        }

        imageInput.onchange = function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.querySelector('img').src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        };

        function clearImage() {
            imageInput.value = '';
            imagePreview.classList.add('hidden');
        }

        chatForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('message', messageInput.value);
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            fetch(`/teams/${teamId}/messages`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json()).then(() => {
                messageInput.value = '';
                clearImage();
                fetchMessages();
            });
        };
    </script>
@endif
