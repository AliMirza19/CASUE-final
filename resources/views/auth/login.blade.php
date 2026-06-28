@extends('layouts.app')

@section('title', 'Login - CAUSE Smart Society')

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

    /* Mesh/Grid Background Effect */
    .main-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 2px 2px, rgba(124, 58, 237, 0.05) 1px, transparent 0);
        background-size: 40px 40px;
        pointer-events: none;
    }

    .glass-card {
        background: #0f0f12;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 2.5rem;
    }

    .input-box {
        background: #16161a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .input-box:focus-within {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }

    .btn-auth {
        background: #6d28d9;
        transition: all 0.3s ease;
    }

    .btn-auth:hover {
        background: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(109, 40, 217, 0.5);
    }

    .divider-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    }

    .logo-filter {
        filter: brightness(0) invert(1);
    }

    .text-glow {
        text-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div class="h-screen w-full flex items-center justify-center main-bg p-6 lg:p-0">
    <div class="w-full max-w-7xl flex flex-col lg:flex-row items-center justify-between lg:px-20">
        
        <!-- Left Side: Branding -->
        <div class="hidden lg:flex lg:w-1/2 flex-col items-center text-center animate-in">
            <!-- CAUSE Logo -->
            <div class="mb-12">
                <img src="{{ asset('images/cause-logo.png') }}" 
                     alt="CAUSE Logo" 
                     class="w-64 h-auto logo-filter opacity-90">
            </div>

            <div class="space-y-4">
                <h1 class="text-7xl font-black text-white leading-tight tracking-tight uppercase">
                    CAUSE SMART<br>
                    <span class="text-purple-600 text-glow">SOCIETY</span>
                </h1>
                
                <div class="flex items-center justify-center space-x-4 py-6">
                    <div class="h-px w-24 bg-gradient-to-r from-transparent to-white/20"></div>
                    <div class="flex space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                    </div>
                    <div class="h-px w-24 bg-gradient-to-l from-transparent to-white/20"></div>
                </div>

                <p class="text-white/40 text-lg font-bold tracking-[0.5em] uppercase">
                    Management System
                </p>
            </div>
        </div>

        <!-- Right Side: Login Card -->
        <div class="w-full lg:w-[480px] glass-card p-10 lg:p-14 animate-in" style="animation-delay: 0.2s">
            <div class="mb-14 pt-6 relative">
                <h2 class="text-4xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-gray-500 font-medium">Access your portal to continue</p>
                <div class="absolute -top-4 -right-4 grid grid-cols-4 gap-1.5 opacity-20">
                    @for($i=0; $i<16; $i++)
                        <div class="w-1 h-1 rounded-full bg-purple-400"></div>
                    @endfor
                </div>
            </div>

            @if (session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-8 text-sm font-bold flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl mb-8 text-sm font-bold flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">Registration ID</label>
                    <div class="input-box flex items-center rounded-2xl px-5 group">
                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="reg_id" 
                            value="{{ old('reg_id') }}"
                            required 
                            class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                            placeholder="Enter your ID"
                        >
                    </div>
                    @error('reg_id')
                        <p class="text-red-400 text-xs ml-1 font-medium mt-1">{!! $message !!}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">Secure Password</label>
                    <div class="input-box flex items-center rounded-2xl px-5 group">
                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                            placeholder="Enter your password"
                        >
                    </div>
                    <div class="flex justify-end mt-2">
                        <a href="#" onclick="event.preventDefault(); const reg = document.querySelector('input[name=reg_id]').value; window.location.href='{{ route('password.verify.identity') }}' + (reg ? '?reg_id=' + encodeURIComponent(reg) : '');" class="text-xs text-purple-400 hover:text-purple-300 font-semibold uppercase tracking-wider transition-colors">Forgot Password?</a>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="btn-auth w-full flex items-center justify-center py-5 rounded-2xl text-white font-black uppercase tracking-widest text-sm"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Authenticate
                </button>
            </form>
        </div>
    </div>
</div>
@endsection