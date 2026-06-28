@extends('layouts.app')

@section('title', 'Create New Password - CAUSE Smart Society')

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
    <div class="w-full lg:w-[480px] glass-card p-10 lg:p-14 animate-in">
        <div class="mb-10 pt-2 relative">
            <h2 class="text-4xl font-bold text-white mb-2">New Password</h2>
            <p class="text-gray-500 font-medium">Please choose a strong password</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-8 text-sm font-bold flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">New Password</label>
                <div class="input-box flex items-center rounded-2xl px-5 group">
                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="Enter new password"
                    >
                </div>
                @error('password')
                    <p class="text-red-400 text-xs ml-1 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">Confirm Password</label>
                <div class="input-box flex items-center rounded-2xl px-5 group">
                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="Re-enter new password"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="btn-auth w-full flex items-center justify-center py-5 rounded-2xl text-white font-black uppercase tracking-widest text-sm mt-8"
            >
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
