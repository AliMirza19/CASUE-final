@extends('layouts.dashboard')

@section('title', 'Profile - CAUSE Smart Society')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your account settings')

@section('sidebar')
    @include('partials.hod-sidebar')
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
