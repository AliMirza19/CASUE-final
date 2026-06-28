@extends('layouts.dashboard')

@section('title', 'Review Election Candidates - CAUSE')
@section('page-title', 'Review Election Candidates')
@section('page-description', 'Review student manifestos and shortlist for HOD review')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
<div class="space-y-8">
    @if($finalizedCandidate)
        <div class="bg-gradient-to-br from-green-900 to-emerald-900 rounded-[3rem] p-12 shadow-2xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center space-x-6 mb-8">
                    <div class="w-24 h-24 rounded-[2rem] bg-white/20 backdrop-blur-md flex items-center justify-center text-4xl shadow-xl border border-white/30">
                        👑
                    </div>
                    <div>
                        <h2 class="text-4xl font-black mb-1">Current President</h2>
                        <p class="text-emerald-200 font-bold uppercase tracking-widest text-sm">Selection Finalized for {{ $activeTerm->term_name ?? 'Current Term' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="bg-white/10 backdrop-blur-sm rounded-[2.5rem] p-8 border border-white/10">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500 flex items-center justify-center text-white text-xl font-black shadow-lg">
                                {{ substr($finalizedCandidate->student->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-black">{{ $finalizedCandidate->student->name }}</h3>
                                <p class="text-xs font-bold text-emerald-300 uppercase tracking-widest">{{ $finalizedCandidate->student->reg_id }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-emerald-50 leading-relaxed italic">"{{ $finalizedCandidate->manifesto_text }}"</p>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-emerald-300">Selected By Committee:</h4>
                        <div class="flex flex-wrap gap-4">
                            @if($selectionCommittee)
                                @foreach($selectionCommittee->members as $member)
                                    <div class="flex items-center bg-white/5 rounded-2xl p-3 border border-white/5 pr-6">
                                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-black mr-3 uppercase">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-bold">{{ $member->user->name }}</p>
                                            <p class="text-[8px] text-emerald-300 font-black uppercase tracking-tighter">{{ $member->user->getDisplayRole() }}</p>
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

    @if(!$finalizedCandidate && $candidates->isEmpty())
        <div class="bg-white/70 backdrop-blur-md rounded-3xl p-16 text-center shadow-xl border border-white/20">
            <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-200">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800 mb-2">No Applications Found</h2>
            <p class="text-gray-500">There are no president candidate applications to review at this time.</p>
        </div>
    @elseif(!$finalizedCandidate && $candidates->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($candidates as $candidate)
                <div class="group relative bg-white border border-gray-100 rounded-[2.5rem] shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 p-6">
                        @if($candidate->status === 'pending')
                            <span class="px-4 py-1.5 bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-yellow-200">Pending Review</span>
                        @elseif($candidate->status === 'patron_shortlisted')
                            <span class="px-4 py-1.5 bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-green-200">Shortlisted</span>
                        @else
                            <span class="px-4 py-1.5 bg-gray-100 text-gray-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-gray-200">{{ ucfirst(str_replace('_', ' ', $candidate->status)) }}</span>
                        @endif
                    </div>

                    <div class="p-8">
                        <div class="flex items-center space-x-5 mb-8">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-black shadow-lg">
                                {{ substr($candidate->student->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-800">{{ $candidate->student->name }}</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $candidate->student->reg_id ?? 'STUDENT' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-3xl p-6 mb-8 min-h-[150px] border border-gray-100">
                            <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Manifesto Statement</h4>
                            <p class="text-gray-600 italic leading-relaxed text-sm">"{{ $candidate->manifesto_text }}"</p>
                        </div>

                        @if($candidate->status === 'pending')
                            <form action="{{ route('selection.shortlist', $candidate->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-indigo-200 flex items-center justify-center group">
                                    <span>Shortlist & Forward to HOD</span>
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
