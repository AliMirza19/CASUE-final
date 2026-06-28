@extends('layouts.dashboard')

@section('title', 'My Profile - CAUSE Smart Society')
@section('page-title', 'My Profile')
@section('page-description', 'Manage your personal information and security settings')

@section('sidebar')
    @php
        $role = auth()->user()->getActiveRole();
    @endphp

    @if($role === 'admin')
        @include('partials.admin-sidebar')
    @elseif($role === 'student')
        @include('partials.student-sidebar')
    @elseif($role === 'hod')
        @include('partials.hod-sidebar')
    @elseif($role === 'patron')
        @include('partials.patron-sidebar')
    @elseif($role === 'faculty')
        @include('partials.faculty-sidebar')
    @elseif($role === 'vc')
        @include('partials.vc-sidebar')
    @elseif($role === 'sa')
        @include('partials.sa-sidebar')
    @elseif($role === 'president')
        @include('partials.president-sidebar')
    @elseif(in_array($role, ['gd', 'smt', 'photo', 'video', 'doc', 'deco']))
        @include('partials.team-sidebar', ['role' => $role])
    @endif
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Profile Summary -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-cause-purple to-purple-600"></div>
                <div class="px-6 pb-8 text-center -mt-16">
                    <div class="relative inline-block">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=128' }}" 
                             alt="{{ $user->name }}" 
                             class="w-32 h-32 rounded-2xl border-4 border-white shadow-md object-cover">
                        <div class="absolute -bottom-2 -right-2 bg-green-500 border-4 border-white w-6 h-6 rounded-full" title="Online"></div>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->reg_id }}</p>
                    <div class="mt-4 flex justify-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->getDisplayRoleColor() }}">
                            {{ $user->getDisplayRole() }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mt-3">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $user->contact_number ?? 'No phone added' }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content: Forms -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Section A: Personal Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Personal & Academic Details</h3>
                    <span class="text-xs text-gray-500">Updating profile notifies administrators</span>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Read-Only Fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration ID / Roll No</label>
                            <input type="text" value="{{ $user->reg_id }}" readonly 
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CNIC Number</label>
                            <input type="text" value="{{ $user->cnic }}" readonly 
                                   class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed">
                        </div>
                        
                        <!-- Editable Fields -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                                   placeholder="e.g., 0300-1234567">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mailing Address</label>
                            <textarea name="mailing_address" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">{{ old('mailing_address', $user->mailing_address) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Degree / Academic Rank</label>
                            <input type="text" name="academic_rank" value="{{ old('academic_rank', $user->academic_rank) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                                   placeholder="e.g. BSCS, Assistant Professor">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                            <div class="mt-1 flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" class="h-12 w-12 rounded-lg object-cover border">
                                </div>
                                <input type="file" name="profile_picture" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-cause-purple hover:file:bg-purple-100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-bold py-2 px-8 rounded-lg transition-all shadow-lg shadow-purple-200">
                            Update Details
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section B: Security -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Security & Password</h3>
                </div>
                <form action="{{ route('profile.password') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="new_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-8 rounded-lg transition-all shadow-lg shadow-gray-200">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
