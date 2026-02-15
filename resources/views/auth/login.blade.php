@extends('layouts.app')

@section('title', 'Login - CAUSE Smart Society')

@section('content')
<div class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header Section -->
        <div class="text-center mb-8">
            <div class="inline-block mb-4">
                <img src="https://play-lh.googleusercontent.com/8QQUZDWOC8RpSqVsw2apjdnLiHvyLc1vJBpOC0MNQcE3_-JHv3XtW1K5m6YmVA6I-A" 
                     alt="CAUSE Logo" 
                     class="w-20 h-20 mx-auto rounded-full shadow-lg border-4 border-white"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <!-- Fallback icon if image fails to load -->
                <div class="hidden bg-cause-purple text-white rounded-full p-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">CAUSE Smart Society</h1>
            <p class="text-gray-600">Management System</p>
        </div>

        <!-- Login Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login</h2>
            
            <!-- Display Messages -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Registration ID Field -->
                <div>
                    <label for="reg_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Registration ID
                    </label>
                    <input 
                        type="text" 
                        id="reg_id" 
                        name="reg_id" 
                        value="{{ old('reg_id') }}"
                        required 
                        minlength="6" 
                        maxlength="12"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent transition duration-200 @error('reg_id') border-red-500 @enderror"
                        placeholder="Enter your Registration ID"
                    >
                    @error('reg_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="6" 
                        maxlength="30"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-cause-purple focus:ring-offset-2"
                >
                    Login
                </button>
            </form>

            <!-- Footer Text -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>CAUSE Smart Society Management System</p>
                <p class="mt-1">© 2024 All Rights Reserved</p>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>For login issues, please contact the administrator</p>
        </div>
    </div>
</div>
@endsection