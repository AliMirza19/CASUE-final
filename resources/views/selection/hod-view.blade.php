@extends('layouts.dashboard')

@section('title', 'Review Election Candidates - CAUSE')
@section('page-title', 'Review Election Candidates')
@section('page-description', 'Review Patron-shortlisted candidates and form a selection committee')

@section('sidebar')
    @include('partials.hod-sidebar')
@endsection

@section('content')
<div class="space-y-10">
    @if($finalizedCandidate)
        <div class="bg-gradient-to-br from-indigo-900 to-purple-950 rounded-[3rem] p-12 shadow-2xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center space-x-6 mb-8">
                    <div class="w-24 h-24 rounded-[2rem] bg-white/10 backdrop-blur-md flex items-center justify-center text-4xl shadow-xl border border-white/20">
                        🏆
                    </div>
                    <div>
                        <h2 class="text-4xl font-black mb-1">President Finalized</h2>
                        <p class="text-indigo-300 font-bold uppercase tracking-widest text-sm">Selection Finalized for {{ $activeTerm->term_name ?? 'Current Term' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="bg-white/5 backdrop-blur-sm rounded-[2.5rem] p-8 border border-white/10">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-indigo-500 flex items-center justify-center text-white text-xl font-black shadow-lg">
                                {{ substr($finalizedCandidate->student->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-black">{{ $finalizedCandidate->student->name }}</h3>
                                <p class="text-xs font-bold text-indigo-300 uppercase tracking-widest">{{ $finalizedCandidate->student->reg_id }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-indigo-50 leading-relaxed italic border-l-2 border-indigo-500/50 pl-4">"{{ $finalizedCandidate->manifesto_text }}"</p>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Decision Makers (Committee):</h4>
                        <div class="flex flex-wrap gap-3">
                            @if($selectionCommittee)
                                @foreach($selectionCommittee->members as $member)
                                    <div class="flex items-center bg-white/5 rounded-2xl p-3 border border-white/5 pr-4">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-[10px] font-black mr-3 uppercase">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold truncate">{{ $member->user->name }}</p>
                                            <p class="text-[7px] text-indigo-400 font-black uppercase tracking-widest">{{ $member->user->getDisplayRole() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shortlisted Candidates Grid -->
    @if(!$finalizedCandidate)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($shortlistedCandidates as $candidate)
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4">
                    <div class="bg-green-500 text-white text-[8px] font-black uppercase px-3 py-1 rounded-full border border-green-400">Shortlisted</div>
                </div>
                
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                        {{ substr($candidate->student->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-black text-gray-800">{{ $candidate->student->name }}</h4>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $candidate->student->reg_id }}</p>
                    </div>
                </div>

                <div class="text-gray-500 text-xs italic line-clamp-4 leading-relaxed mb-4">
                    "{{ $candidate->manifesto_text }}"
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 bg-white border border-dashed border-gray-200 rounded-[2.5rem] p-12 text-center">
                <p class="text-gray-400 font-bold italic">No candidates have been shortlisted by the Patron yet.</p>
            </div>
        @endforelse
    </div>
    @endif

    <!-- Committee Formation Card -->
    @if($shortlistedCandidates->isNotEmpty() && !$activeCommittee && !$finalizedCandidate)
        <div class="bg-gradient-to-br from-indigo-900 to-purple-900 rounded-[3rem] p-12 shadow-2xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 max-w-2xl">
                <h3 class="text-3xl font-black mb-4">Form Selection Committee</h3>
                <p class="text-indigo-200 mb-8 leading-relaxed">As HOD, you are required to form a committee of 5 members (You, the Patron, and 3 Faculty Teachers) to discuss and finalize the new President.</p>
                
                <form action="{{ route('selection.form-committee') }}" method="POST" class="space-y-6" id="committeeForm">
                    @csrf
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-300">Select Exactly 3 Faculty Members *</label>
                            <span id="selectionCount" class="text-[10px] font-black text-white bg-indigo-500 px-2 py-0.5 rounded-full">0 / 3 Selected</span>
                        </div>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <input type="text" id="facultySearch" placeholder="Search by name or rank..." 
                                   class="w-full bg-white/10 border border-white/20 rounded-xl py-3 px-10 text-sm text-white placeholder-indigo-300 focus:ring-2 focus:ring-white/30 transition-all">
                            <svg class="w-4 h-4 absolute left-4 top-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        <!-- Faculty List -->
                        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                            <div class="max-h-64 overflow-y-auto custom-scrollbar" id="facultyList">
                                @foreach($facultyMembers as $faculty)
                                    <label class="faculty-item flex items-center p-4 border-b border-white/5 hover:bg-white/10 cursor-pointer transition-colors group" data-name="{{ strtolower($faculty->name) }} {{ strtolower($faculty->academic_rank ?? '') }}">
                                        <div class="relative flex items-center justify-center">
                                            <input type="checkbox" name="faculty_ids[]" value="{{ $faculty->id }}" class="faculty-checkbox w-5 h-5 rounded-lg bg-white/10 border-white/20 text-indigo-500 focus:ring-indigo-500 transition-all cursor-pointer">
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-bold text-white group-hover:text-indigo-200 transition-colors">{{ $faculty->name }}</p>
                                            <p class="text-[9px] text-indigo-300 font-bold uppercase tracking-widest">{{ $faculty->academic_rank ?? 'Faculty Member' }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div id="errorMessage" class="hidden bg-red-500/20 border border-red-500/50 text-red-200 text-[10px] font-bold p-3 rounded-xl animate-pulse">
                        Please select exactly 3 members to proceed.
                    </div>

                    <button type="submit" id="submitBtn" disabled class="w-full py-4 bg-white/20 text-white/50 font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl cursor-not-allowed">
                        Form Committee & Start Discussion
                    </button>
                </form>

                <style>
                    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
                    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
                    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
                    
                    .faculty-item.selected { background-color: rgba(99, 102, 241, 0.2); border-left: 4px solid #fff; }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('facultySearch');
                        const items = document.querySelectorAll('.faculty-item');
                        const checkboxes = document.querySelectorAll('.faculty-checkbox');
                        const countSpan = document.getElementById('selectionCount');
                        const submitBtn = document.getElementById('submitBtn');
                        const errorMsg = document.getElementById('errorMessage');

                        // Search functionality
                        searchInput.addEventListener('input', function(e) {
                            const term = e.target.value.toLowerCase();
                            items.forEach(item => {
                                const text = item.getAttribute('data-name');
                                if (text.includes(term)) {
                                    item.style.display = 'flex';
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                        });

                        // Checkbox logic
                        checkboxes.forEach(cb => {
                            cb.addEventListener('change', function() {
                                const selected = document.querySelectorAll('.faculty-checkbox:checked');
                                const count = selected.length;
                                
                                // Update visual state
                                this.closest('.faculty-item').classList.toggle('selected', this.checked);
                                
                                // Update count
                                countSpan.textContent = `${count} / 3 Selected`;
                                
                                // Limit and enable button
                                if (count === 3) {
                                    submitBtn.disabled = false;
                                    submitBtn.classList.remove('bg-white/20', 'text-white/50', 'cursor-not-allowed');
                                    submitBtn.classList.add('bg-white', 'text-indigo-900', 'hover:scale-105');
                                    errorMsg.classList.add('hidden');
                                    
                                    // Disable others
                                    checkboxes.forEach(other => {
                                        if (!other.checked) other.disabled = true;
                                    });
                                } else {
                                    submitBtn.disabled = true;
                                    submitBtn.classList.add('bg-white/20', 'text-white/50', 'cursor-not-allowed');
                                    submitBtn.classList.remove('bg-white', 'text-indigo-900', 'hover:scale-105');
                                    
                                    if (count > 0) errorMsg.classList.remove('hidden');
                                    else errorMsg.classList.add('hidden');

                                    // Enable others
                                    checkboxes.forEach(other => other.disabled = false);
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>
    @elseif($activeCommittee)
        <div class="bg-indigo-50 border-2 border-indigo-100 rounded-[2.5rem] p-8 text-center">
            <h3 class="text-indigo-800 font-black text-xl mb-2">Active Committee Found</h3>
            <p class="text-indigo-600 mb-6">A selection committee is already active for this term.</p>
            <a href="{{ route('selection.discussion') }}" class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-colors">
                Go to Discussion Room
            </a>
        </div>
    @endif
</div>
@endsection
