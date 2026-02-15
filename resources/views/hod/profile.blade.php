@extends('layouts.dashboard')

@section('title', 'Profile - CAUSE Smart Society')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your account settings')

@section('sidebar')
    <a href="{{ route('hod.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('hod.manage-patron') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Manage Patron
    </a>
    <a href="{{ route('hod.budget') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Manage Budget
    </a>
    <a href="{{ route('hod.analytics') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Analytics
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Profile Information</h3>
        </div>
        
        <form action="{{ route('hod.profile.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="flex items-center mb-6">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mr-6">
                    <span class="text-3xl font-bold text-orange-600">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-800">{{ $user->name }}</p>
                    <p class="text-gray-500">{{ $user->reg_id }}</p>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-orange-100 text-orange-800">HOD</span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registration ID</label>
                    <input type="text" value="{{ $user->reg_id }}" disabled
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                    <p class="mt-1 text-xs text-gray-500">Registration ID cannot be changed</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between items-center">
                <a href="{{ route('password.change') }}" class="text-cause-purple hover:text-cause-purple-dark font-medium">
                    Change Password
                </a>
                <button type="submit" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-6 rounded-lg transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
