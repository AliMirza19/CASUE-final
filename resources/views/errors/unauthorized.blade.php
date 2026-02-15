@extends('layouts.app')

@section('title', 'Unauthorized Access - CAUSE Smart Society')

@section('content')
<div class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <!-- Error Icon -->
        <div class="text-center mb-8">
            <div class="inline-block mb-4">
                <div class="bg-red-500 text-white rounded-full p-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Access Denied</h1>
            <p class="text-gray-600">You don't have permission to access this page</p>
        </div>

        <!-- Error Details Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Unauthorized Access</h2>
                
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($user)
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6" role="alert">
                        <p><strong>Current Role:</strong> {{ ucfirst($userRole) }}</p>
                        <p class="text-sm mt-1">You are logged in as {{ $user->name }}</p>
                    </div>
                @endif

                <p class="text-gray-600 mb-6">
                    The page you're trying to access requires different permissions. 
                    Please contact your administrator if you believe this is an error.
                </p>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if ($user)
                        @php
                            $dashboardRoutes = [
                                'admin' => 'admin.dashboard',
                                'hod' => 'hod.dashboard',
                                'patron' => 'patron.dashboard',
                                'president' => 'president.dashboard',
                                'student' => 'student.dashboard',
                                'sa' => 'sa.dashboard',
                                'vc' => 'vc.dashboard',
                                'gd' => 'gd.dashboard',
                            ];
                            $dashboardRoute = $dashboardRoutes[$userRole] ?? 'student.dashboard';
                        @endphp
                        
                        <a href="{{ route($dashboardRoute) }}" 
                           class="w-full inline-block bg-cause-purple hover:bg-cause-purple-dark text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105">
                            Go to My Dashboard
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>Need help? Contact the system administrator</p>
        </div>
    </div>
</div>
@endsection