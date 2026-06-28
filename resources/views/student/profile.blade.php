@extends('layouts.dashboard')

@section('title', 'My Profile - CAUSE Smart Society')
@section('page-title', 'My Profile')
@section('page-description', 'Update your personal details, skills, and experience')

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
    <a href="{{ route('student.profile') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        My Profile
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
        </div>
        <form action="{{ route('student.profile.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" value="{{ $user->name }}" disabled class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registration ID</label>
                    <input type="text" value="{{ $user->reg_id }}" disabled class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ $user->contact_number }}" placeholder="03XXXXXXXXX"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CNIC</label>
                    <input type="text" name="cnic" value="{{ $user->cnic }}" placeholder="XXXXX-XXXXXXX-X"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Semester</label>
                    <input type="text" name="current_semester" value="{{ $user->current_semester }}" placeholder="e.g. 4th"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
            </div>

            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    AI Profiling Data (Skills & Experience)
                </h3>
                <p class="text-sm text-gray-600 mb-4">This information helps the Volunteer Coordinator match you with the best events.</p>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                        <textarea name="skills" rows="3" placeholder="e.g. Graphic Design, Content Writing, Stage Management, Public Speaking, Python Programming..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">{{ $user->skills }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Separate skills with commas.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                        <textarea name="experience" rows="3" placeholder="e.g. Organized Tech-Week 2025, Member of Media Club, Volunteered at Blood Drive..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">{{ $user->experience }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-2 px-6 rounded-lg transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
