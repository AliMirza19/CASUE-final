@extends('layouts.dashboard')

@section('title', 'Election Center - CAUSE Smart Society')
@section('page-title', 'Election Center')
@section('page-description', 'Participate in the democratic process of the society')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Election Status Banner -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
        <div class="p-8 flex flex-col md:flex-row items-center justify-between bg-gradient-to-r from-cause-purple to-purple-800 text-white">
            <div class="mb-6 md:mb-0">
                <h2 class="text-3xl font-bold">University Elections</h2>
                <p class="text-purple-100 mt-2 text-lg">
                    @if(!$settings)
                        Election schedule has not been announced yet.
                    @else
                        Status: <span class="font-bold uppercase">{{ $settings->getStatus() }}</span>
                    @endif
                </p>
            </div>
            @if($settings)
            <div class="flex space-x-4">
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-3 text-center border border-white border-opacity-20">
                    <p class="text-xs uppercase font-semibold">Active Term</p>
                    <p class="text-xl font-bold">{{ $settings->term->term_name }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="p-8 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Candidate Registration Section -->
                <div class="border rounded-xl p-6 {{ $settings && $settings->isRegistrationOpen() ? 'border-blue-200 bg-blue-50' : 'border-gray-100 bg-gray-50' }}">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Candidacy Registration</h3>
                    </div>
                    
                    @if($hasRegistered)
                        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center mb-4">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-medium">You have already registered as a candidate.</span>
                        </div>
                    @elseif($settings && $settings->isRegistrationOpen())
                        <p class="text-gray-600 mb-6">Want to lead the society? Submit your manifesto and join the race for the upcoming term.</p>
                        <a href="{{ route('student.election.register') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all shadow-md">
                            Register Now
                        </a>
                    @else
                        <p class="text-gray-400 italic">Registration is currently closed.</p>
                    @endif
                </div>

                <!-- Voting Section -->
                <div class="border rounded-xl p-6 {{ $settings && $settings->isVotingActive() ? 'border-green-200 bg-green-50' : 'border-gray-100 bg-gray-50' }}">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Cast Your Vote</h3>
                    </div>

                    @if($hasVoted)
                        <div class="bg-purple-100 border border-purple-200 text-purple-700 px-4 py-3 rounded-lg flex items-center mb-4">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-medium">Thank you! Your vote has been cast.</span>
                        </div>
                    @elseif($settings && $settings->isVotingActive())
                        <p class="text-gray-600 mb-6">Electronic voting is now live. Review the candidates and cast your vote securely.</p>
                        <a href="{{ route('student.election.vote') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-all shadow-md">
                            Go to Voting Hall
                        </a>
                    @else
                        <p class="text-gray-400 italic">Voting is currently closed.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Candidate List (Always visible if approved) -->
    @if($approvedCandidates->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Meet the Candidates
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($approvedCandidates as $candidate)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-purple-200 overflow-hidden border-2 border-white shadow-sm flex-shrink-0">
                            @if($candidate->photo_path)
                                <img src="{{ asset('storage/' . $candidate->photo_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-purple-600 font-bold text-xl uppercase">
                                    {{ substr($candidate->student->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $candidate->student->name }}</h4>
                            <p class="text-xs text-gray-500">VP: {{ $candidate->vp_name }}</p>
                            <p class="text-xs text-cause-purple font-semibold mt-1">Status: Approved</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
