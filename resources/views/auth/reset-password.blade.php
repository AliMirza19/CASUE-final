@extends('layouts.app')

@section('title', 'Reset Password - CAUSE Smart Society')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    body {
        background-color: #050505;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }

    .main-bg {
        background: linear-gradient(135deg, #0a0a0a 0%, #1a0b2e 100%);
        position: relative;
    }

    .main-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 2px 2px, rgba(124, 58, 237, 0.05) 1px, transparent 0);
        background-size: 40px 40px;
        pointer-events: none;
    }

    .glass-card {
        background: rgba(15, 15, 18, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 2.5rem;
        box-shadow: 
            0 0 80px rgba(124, 58, 237, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    .input-box {
        background: rgba(22, 22, 26, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input-box:focus-within {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1), 0 0 20px rgba(124, 58, 237, 0.05);
    }

    .btn-reset {
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-reset::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s;
    }

    .btn-reset:hover::before {
        left: 100%;
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -5px rgba(109, 40, 217, 0.5);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeIn 0.8s ease-out forwards;
    }

    .info-tag {
        background: rgba(124, 58, 237, 0.1);
        border: 1px solid rgba(124, 58, 237, 0.2);
    }
</style>
@endpush

@section('content')
<div class="h-screen w-full flex items-center justify-center main-bg p-6 lg:p-0">
    <div class="w-full max-w-lg glass-card p-10 lg:p-14 animate-in relative z-10">
        
        <div class="mb-10 pt-2 relative text-center">
            <!-- Lock Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-full bg-purple-600/20 border border-purple-500/30 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
            </div>
            <div class="info-tag inline-block px-3 py-1 rounded-full mb-4">
                <span class="text-[10px] font-black text-purple-400 uppercase tracking-widest">Step 2 of 2</span>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Set New Password</h2>
            <p class="text-gray-500 font-medium text-sm">Choose a strong password for your account</p>
        </div>

        <!-- Error Message -->
        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-8 text-sm font-bold flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('password.reset.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- New Password -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">New Password</label>
                <div class="input-box flex items-center rounded-2xl px-5 group">
                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        required 
                        minlength="6"
                        maxlength="30"
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="Minimum 6 characters"
                    >
                </div>
                @error('password')
                    <p class="text-red-400 text-xs ml-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">Confirm Password</label>
                <div class="input-box flex items-center rounded-2xl px-5 group">
                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation"
                        required 
                        minlength="6"
                        maxlength="30"
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="Re-enter your password"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="btn-reset w-full flex items-center justify-center py-5 rounded-2xl text-white font-black uppercase tracking-widest text-sm"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Reset Password
            </button>
        </form>

        <!-- Divider -->
        <div class="my-8 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-purple-400 text-sm font-semibold transition-colors inline-flex items-center group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
