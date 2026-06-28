@extends('layouts.dashboard')

@section('title', 'Committee Discussion Room - CAUSE')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
<div class="flex flex-col lg:flex-row h-[calc(100vh-160px)] gap-8 overflow-hidden">
    <!-- LEFT SIDE: Shortlisted Candidates Reference -->
    <div class="lg:w-1/3 flex flex-col h-full bg-white border border-gray-100 rounded-[2.5rem] shadow-xl overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Shortlisted Candidates
            </h3>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            @foreach($candidates as $candidate)
                <div class="p-6 rounded-3xl border-2 {{ $candidate->status === 'finalized_president' ? 'border-green-500 bg-green-50' : 'border-gray-100' }} hover:border-indigo-200 transition-all group">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black shadow-sm">
                            {{ substr($candidate->student->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-black text-gray-800 truncate">{{ $candidate->student->name }}</h4>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $candidate->student->reg_id }}</p>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 italic mb-4 leading-relaxed line-clamp-3">
                        "{{ $candidate->manifesto_text }}"
                    </div>

                    <!-- Finalize Action (HOD Only) -->
                    @if(Auth::id() === $committee->hod_id && $candidate->status === 'patron_shortlisted')
                        <form action="{{ route('selection.finalize', [$committee->id, $candidate->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-100 flex items-center justify-center">
                                Finalize as President 👑
                            </button>
                        </form>
                    @elseif($candidate->status === 'finalized_president')
                        <div class="w-full py-2.5 bg-green-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl text-center shadow-lg shadow-green-100">
                            Finalized President 👑
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT SIDE: Real-Time Style Chat Room -->
    <div class="flex-1 flex flex-col h-full bg-white border border-gray-100 rounded-[2.5rem] shadow-xl overflow-hidden relative">
        <!-- Chat Header -->
        <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex -space-x-3">
                    @foreach($committee->members as $member)
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-indigo-200 flex items-center justify-center text-[10px] font-bold text-indigo-700 shadow-sm" title="{{ $member->user->name }}">
                            {{ substr($member->user->name, 0, 1) }}
                        </div>
                    @endforeach
                </div>
                <div>
                    <h3 class="text-sm font-black text-indigo-900 leading-none">Committee Discussion Room</h3>
                    <p class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest mt-1">Active Committee Session</p>
                </div>
            </div>
            <div class="px-3 py-1 bg-green-100 text-green-600 text-[8px] font-black uppercase rounded-full animate-pulse">Live Discussion</div>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-gray-50/30" id="chat-messages">
            @forelse($committee->messages as $msg)
                <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] flex flex-col {{ $msg->sender_id === Auth::id() ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center space-x-2 mb-1 px-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $msg->sender->name }}</span>
                            <span class="text-[8px] px-2 py-0.5 rounded-full {{ $msg->sender->id === $committee->hod_id ? 'bg-red-100 text-red-600' : ($msg->sender->id === $committee->patron_id ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600') }} font-black uppercase">{{ $msg->sender->getDisplayRole() }}</span>
                        </div>
                        <div class="p-4 rounded-3xl {{ $msg->sender_id === Auth::id() ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-100' : 'bg-white text-gray-700 rounded-tl-none shadow-sm border border-gray-100' }} shadow-xl">
                            <p class="text-sm leading-relaxed">{{ $msg->message }}</p>
                        </div>
                        <span class="text-[8px] text-gray-400 mt-1 px-2 font-bold">{{ $msg->created_at->format('h:i A') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex items-center justify-center h-full flex-col text-center opacity-30">
                    <svg class="w-20 h-20 mb-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    <p class="text-lg font-black italic">No messages yet. Start the discussion!</p>
                </div>
            @endforelse
        </div>

        <!-- Chat Input Area -->
        <div class="p-6 border-t border-gray-100 bg-white">
            <form action="{{ route('selection.send-message', $committee->id) }}" method="POST" class="flex space-x-4">
                @csrf
                <div class="flex-1 relative">
                    <input type="text" name="message" required placeholder="Type your observation or recommendation..." 
                           class="w-full py-4 px-6 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-medium">
                </div>
                <button type="submit" class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 transition-all transform active:scale-95">
                    <svg class="w-6 h-6 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-scroll to bottom of chat
    const chatArea = document.getElementById('chat-messages');
    chatArea.scrollTop = chatArea.scrollHeight;
</script>
@endsection
