@extends('layouts.dashboard')

@section('title', 'Election Configuration - CAUSE Smart Society')
@section('page-title', 'Election Control Panel')
@section('page-description', 'Manage registration and voting timelines for ' . $activeTerm->term_name)

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Current Status Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 flex items-center justify-between border-l-8 {{ $settings->is_active ? 'border-green-500' : 'border-gray-300' }}">
        <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Election Status</h3>
            <div class="mt-2 flex items-center">
                <span class="px-3 py-1 text-sm font-bold rounded-full {{ $settings->getStatusColor() }}">
                    {{ $settings->getStatus() }}
                </span>
                @if(!$settings->is_active)
                    <span class="ml-3 text-xs text-red-500 font-medium italic">* Election is globally disabled</span>
                @endif
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Active Term</p>
            <p class="font-bold text-gray-800">{{ $activeTerm->term_name }}</p>
        </div>
    </div>

    <!-- Configuration Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Configure Timeline</h3>
        </div>
        
        <form action="{{ route('patron.election.settings.update') }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-bold text-gray-800 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-sm">1</span>
                        Candidate Registration Period
                    </h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="registration_start" class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                        <input type="datetime-local" id="registration_start" name="registration_start" 
                               value="{{ $settings->registration_start ? $settings->registration_start->format('Y-m-d\TH:i') : '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cause-purple focus:border-cause-purple">
                    </div>
                    <div>
                        <label for="registration_end" class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
                        <input type="datetime-local" id="registration_end" name="registration_end" 
                               value="{{ $settings->registration_end ? $settings->registration_end->format('Y-m-d\TH:i') : '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cause-purple focus:border-cause-purple">
                    </div>
                </div>
            </div>

            <hr class="mb-8 border-gray-100">

            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-bold text-gray-800 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-2 text-sm">2</span>
                        Voting Period
                    </h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="voting_start" class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                        <input type="datetime-local" id="voting_start" name="voting_start" 
                               value="{{ $settings->voting_start ? $settings->voting_start->format('Y-m-d\TH:i') : '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cause-purple focus:border-cause-purple">
                    </div>
                    <div>
                        <label for="voting_end" class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
                        <input type="datetime-local" id="voting_end" name="voting_end" 
                               value="{{ $settings->voting_end ? $settings->voting_end->format('Y-m-d\TH:i') : '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-cause-purple focus:border-cause-purple">
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500 italic">Note: Voting usually starts after registration has closed.</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg flex items-center justify-between mb-8 border border-gray-200">
                <div class="flex items-center">
                    <div class="mr-3">
                        <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Enable Election System</p>
                        <p class="text-xs text-gray-600">If disabled, no one can register or vote regardless of dates.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $settings->is_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cause-purple"></div>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-bold py-2 px-8 rounded-lg transition-colors shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
