// CAUSE-AI Chatbot Widget
class CauseAIChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.isTyping = false;
        this.init();
    }

    init() {
        this.createWidget();
        this.attachEventListeners();
        this.loadHistory();
    }

    createWidget() {
        const widget = document.createElement('div');
        widget.id = 'cause-ai-widget';
        widget.innerHTML = `
            <!-- Floating Button -->
            <button id="cause-ai-toggle" class="fixed bottom-6 right-6 w-16 h-16 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 z-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </button>

            <!-- Chat Window -->
            <div id="cause-ai-window" class="fixed bottom-24 right-6 w-96 h-[500px] bg-white rounded-lg shadow-2xl hidden flex-col z-50 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-lg">AI</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">CAUSE-AI</h3>
                            <p class="text-xs text-purple-100">Your Virtual Assistant</p>
                        </div>
                    </div>
                    <button id="cause-ai-close" class="text-white hover:text-purple-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Messages Area -->
                <div id="cause-ai-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                    <div class="text-center text-gray-500 text-sm py-4">
                        <p>👋 Hi! I'm CAUSE-AI, your virtual assistant.</p>
                        <p class="mt-2">How can I help you today?</p>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="border-t border-gray-200 p-4 bg-white">
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            id="cause-ai-input" 
                            placeholder="Type your message..." 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        />
                        <button 
                            id="cause-ai-send" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(widget);
    }

    attachEventListeners() {
        const toggle = document.getElementById('cause-ai-toggle');
        const close = document.getElementById('cause-ai-close');
        const send = document.getElementById('cause-ai-send');
        const input = document.getElementById('cause-ai-input');

        toggle.addEventListener('click', () => this.toggleChat());
        close.addEventListener('click', () => this.toggleChat());
        send.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const window = document.getElementById('cause-ai-window');
        
        if (this.isOpen) {
            window.classList.remove('hidden');
            window.classList.add('flex');
            document.getElementById('cause-ai-input').focus();
        } else {
            window.classList.add('hidden');
            window.classList.remove('flex');
        }
    }

    async loadHistory() {
        try {
            const response = await fetch('/api/ai-chat/history', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.history.length > 0) {
                    const messagesContainer = document.getElementById('cause-ai-messages');
                    messagesContainer.innerHTML = '';
                    
                    data.history.reverse().slice(-5).forEach(item => {
                        this.addMessage(item.message, 'user', false);
                        this.addMessage(item.response, 'bot', false);
                    });
                }
            }
        } catch (error) {
            console.error('Failed to load chat history:', error);
        }
    }

    async sendMessage() {
        const input = document.getElementById('cause-ai-input');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        try {
            const userRole = this.getUserRole();
            const response = await fetch('/api/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    message: message,
                    role: userRole
                })
            });

            const data = await response.json();
            
            this.hideTypingIndicator();

            if (data.success) {
                this.addMessage(data.response, 'bot', true);
            } else {
                this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Network error. Please check your connection.', 'bot');
        }
    }

    addMessage(text, sender, typewriter = false) {
        const messagesContainer = document.getElementById('cause-ai-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
        
        const bubble = document.createElement('div');
        bubble.className = `max-w-[80%] px-4 py-2 rounded-lg ${
            sender === 'user' 
                ? 'bg-purple-600 text-white' 
                : 'bg-white text-gray-800 shadow-md'
        }`;
        
        if (typewriter && sender === 'bot') {
            bubble.textContent = '';
            messageDiv.appendChild(bubble);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            this.typewriterEffect(bubble, text);
        } else {
            bubble.textContent = text;
            messageDiv.appendChild(bubble);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    typewriterEffect(element, text, index = 0) {
        if (index < text.length) {
            element.textContent += text.charAt(index);
            const messagesContainer = document.getElementById('cause-ai-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            setTimeout(() => this.typewriterEffect(element, text, index + 1), 20);
        }
    }

    showTypingIndicator() {
        this.isTyping = true;
        const messagesContainer = document.getElementById('cause-ai-messages');
        const indicator = document.createElement('div');
        indicator.id = 'typing-indicator';
        indicator.className = 'flex justify-start';
        indicator.innerHTML = `
            <div class="bg-white text-gray-800 shadow-md px-4 py-3 rounded-lg">
                <div class="flex space-x-2">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        `;
        messagesContainer.appendChild(indicator);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    hideTypingIndicator() {
        this.isTyping = false;
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
    }

    getUserRole() {
        // Extract role from the current URL or meta tag
        const path = window.location.pathname;
        if (path.includes('/hod/')) return 'HOD';
        if (path.includes('/patron/')) return 'Patron';
        if (path.includes('/president/')) return 'President';
        if (path.includes('/student/')) return 'Student';
        if (path.includes('/sa/')) return 'Student Affairs';
        if (path.includes('/vc/')) return 'Vice Chancellor';
        if (path.includes('/gd/')) return 'Graphic Designer';
        return 'User';
    }
}

// Initialize chatbot when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new CauseAIChatbot());
} else {
    new CauseAIChatbot();
}
