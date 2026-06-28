@extends('layouts.dashboard')

@section('title', 'Profile - CAUSE Smart Society')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your account settings')

@section('sidebar')
    @include('partials.faculty-sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Profile Info Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Profile Information</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="w-20 h-20 bg-cause-purple rounded-full flex items-center justify-center text-white text-2xl font-bold mr-6">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ ucfirst($user->role) }}</p>
                    <p class="text-sm text-gray-400 font-mono">{{ $user->reg_id }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Registration ID</p>
                    <p class="font-semibold text-gray-800 font-mono">{{ $user->reg_id }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Email Address</p>
                    <p class="font-semibold text-gray-800">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update Email Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Update Email</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('faculty.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="px-6 py-2 bg-cause-purple text-white rounded-lg hover:bg-cause-purple-dark transition">
                    Update Email
                </button>
            </form>
        </div>
    </div>
    
    <!-- Change Password Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Change Password</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('faculty.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" id="current_password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="new_password" id="new_password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent @error('new_password') border-red-500 @enderror">
                    @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                </div>
                
                <div class="mb-6">
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                </div>
                
                <button type="submit" class="px-6 py-2 bg-cause-purple text-white rounded-lg hover:bg-cause-purple-dark transition">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
