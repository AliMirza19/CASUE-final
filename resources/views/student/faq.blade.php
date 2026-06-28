@extends('layouts.dashboard')

@section('title', 'Smart FAQ - CAUSE Smart Society')
@section('page-title', 'Smart FAQ')
@section('page-description', 'Ask any question about society rules, event history, or protocols')

@section('sidebar')
    <a href="{{ route('student.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('student.events.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        My Events
    </a>
    <a href="{{ route('student.faq') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Smart FAQ
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- AI Search Header -->
    <div class="bg-gradient-to-r from-cause-purple to-cause-purple-dark rounded-2xl shadow-xl p-8 mb-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-bold mb-2">How can I help you?</h3>
            <p class="text-purple-100 mb-6">Ask about event rules, budget policies, or past society achievements.</p>
            
            <div class="relative">
                <input type="text" id="faqInput" 
                    placeholder="e.g. What is the maximum budget for a seminar?" 
                    class="w-full pl-6 pr-16 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-4 focus:ring-white/10 backdrop-blur-md transition-all text-lg">
                <button onclick="askFaq()" id="btnAsk" class="absolute right-2 top-2 bottom-2 bg-white text-cause-purple px-6 rounded-lg font-bold hover:bg-purple-50 transition active:scale-95 flex items-center">
                    Ask AI
                </button>
            </div>
            
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs text-purple-200">Suggestions:</span>
                <button onclick="quickAsk('Budget rules')" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full transition">Budget rules</button>
                <button onclick="quickAsk('Event approval protocol')" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full transition">Event approval protocol</button>
                <button onclick="quickAsk('Past election results')" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full transition">Past election results</button>
            </div>
        </div>
        
        <!-- Decoration -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-purple-400/10 rounded-full blur-3xl"></div>
    </div>

    <!-- AI Response Area -->
    <div id="faqResponseArea" class="hidden mb-8">
        <div class="bg-white rounded-2xl shadow-md p-8 border border-purple-100 animate-fade-in">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-cause-purple rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">CAUSE-AI Response</h4>
                    <p class="text-xs text-gray-500">Based on society protocols</p>
                </div>
            </div>
            
            <div id="faqResponseContent" class="text-gray-700 leading-relaxed whitespace-pre-line prose max-w-none">
                <!-- Response here -->
            </div>
        </div>
    </div>

    <!-- Static FAQ Topics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                Budget & Finances
            </h4>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('How to request a budget increase?')">How to request a budget increase?</li>
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('What are the itemized cost rules?')">What are the itemized cost rules?</li>
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('Can I modify my budget after submission?')">Can I modify my budget after submission?</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </span>
                Event Planning
            </h4>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('How early should I submit an event?')">How early should I submit an event?</li>
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('Who approves the event venue?')">Who approves the event venue?</li>
                <li class="p-2 hover:bg-gray-50 rounded transition cursor-pointer" onclick="quickAsk('What types of events are allowed?')">What types of events are allowed?</li>
            </ul>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
<script>
    function quickAsk(text) {
        document.getElementById('faqInput').value = text;
        askFaq();
    }

    function askFaq() {
        const query = document.getElementById('faqInput').value.trim();
        if (!query) return;

        const btn = document.getElementById('btnAsk');
        const originalText = btn.innerText;
        btn.innerText = 'Searching...';
        btn.disabled = true;

        const responseArea = document.getElementById('faqResponseArea');
        const responseContent = document.getElementById('faqResponseContent');

        // Use the existing ai-chat API or a new one
        fetch('/api/ai-chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                message: "This is a Smart FAQ inquiry. Please answer the following question about the CAUSE Society based on general rules and protocols: " + query,
                role: 'Student Support'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseContent.innerText = data.response;
                responseArea.classList.remove('hidden');
                responseArea.scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }

    document.getElementById('faqInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') askFaq();
    });
</script>
@endpush
@endsection
