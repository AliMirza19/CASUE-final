@extends('layouts.dashboard')

@section('title', 'Voting Hall - CAUSE Smart Society')
@section('page-title', 'Voting Hall')
@section('page-description', 'Cast your secret ballot for the upcoming leadership')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-xl shadow-lg p-8 text-white mb-10">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-3xl font-bold">Secure Ballot Room</h3>
                <p class="text-green-100 mt-2 text-lg">Every vote is anonymous and counts towards the future of our society.</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-20 h-20 text-white opacity-20" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.68a1 1 0 10-1.415-1.414l.707-.707a1 1 0 001.415 1.415l-.707.708zm10.646.708a1 1 0 00-1.414-1.415l-.708.707a1 1 0 001.414 1.415l.708-.707zM10 8a2 2 0 100 4 2 2 0 000-4z"></path><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0-3.314-2.686-6-6-6S4 6.686 4 10s2.686 6 6 6 6-2.686 6-6z" clip-rule="evenodd"></path></svg>
            </div>
        </div>
    </div>

    <!-- Candidate Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($candidates as $candidate)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 flex flex-col h-full transform transition hover:-translate-y-1 hover:shadow-xl">
                <!-- Header / Image -->
                <div class="h-48 bg-gray-200 relative">
                    @if($candidate->photo_path)
                        <img src="{{ asset('storage/' . $candidate->photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-indigo-600 text-white text-5xl font-bold uppercase">
                            {{ substr($candidate->student->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black opacity-80 text-white">
                        <h4 class="text-xl font-bold truncate">{{ $candidate->student->name }}</h4>
                        <p class="text-xs text-purple-200">VP: {{ $candidate->vp_name }}</p>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 flex-grow">
                    <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Manifesto Snippet</h5>
                    <p class="text-gray-600 line-clamp-4 text-sm leading-relaxed mb-4">
                        {{ $candidate->manifesto }}
                    </p>
                    <button onclick="openManifestoModal('{{ addslashes($candidate->student->name) }}', '{{ addslashes($candidate->manifesto) }}')" 
                            class="text-cause-purple font-bold text-sm hover:underline">
                        Read Full Manifesto →
                    </button>
                </div>

                <!-- Footer / Vote Button -->
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <button onclick="confirmVote({{ $candidate->id }}, '{{ addslashes($candidate->student->name) }}')" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-md transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Cast Vote
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-500">No Approved Candidates</h4>
                <p class="text-gray-400">The candidate list is currently empty for this term.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Full Manifesto Modal -->
<div id="manifesto-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col shadow-2xl">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-xl font-bold text-gray-800" id="modal-candidate-name"></h3>
            <button onclick="closeManifestoModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>
        <div class="p-8 overflow-y-auto flex-grow text-gray-700 leading-relaxed whitespace-pre-wrap" id="modal-manifesto-text"></div>
        <div class="p-6 bg-gray-50 text-right">
            <button onclick="closeManifestoModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition-colors">Close</button>
        </div>
    </div>
</div>

<!-- Vote Confirmation Modal -->
<div id="vote-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl text-center">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Confirm Your Vote</h3>
        <p class="text-gray-600 mb-8" id="vote-message"></p>
        
        <form id="vote-form" action="{{ route('student.election.cast') }}" method="POST">
            @csrf
            <input type="hidden" name="candidate_id" id="target-candidate-id">
            <div class="flex flex-col space-y-3">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-lg">
                    Yes, Confirm My Vote
                </button>
                <button type="button" onclick="closeVoteModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3 rounded-xl transition-all">
                    No, Go Back
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openManifestoModal(name, manifesto) {
        document.getElementById('modal-candidate-name').textContent = "Manifesto: " + name;
        document.getElementById('modal-manifesto-text').textContent = manifesto;
        document.getElementById('manifesto-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeManifestoModal() {
        document.getElementById('manifesto-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmVote(id, name) {
        document.getElementById('target-candidate-id').value = id;
        document.getElementById('vote-message').innerHTML = `You are about to cast your secret ballot for <br><span class="font-bold text-gray-800 text-xl">${name}</span>.<br><br>This action cannot be undone.`;
        document.getElementById('vote-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVoteModal() {
        document.getElementById('vote-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endsection
