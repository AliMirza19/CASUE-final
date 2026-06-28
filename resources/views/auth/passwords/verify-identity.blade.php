@extends('layouts.app')

@section('title', 'Verify Identity - CAUSE Smart Society')

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
            <h2 class="text-4xl font-bold text-white mb-2">Verify Identity</h2>
            <p class="text-gray-500 font-medium">Verify your details to recover your account</p>
        </div>

        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl mb-8 text-sm font-bold flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify.identity.submit') }}" class="space-y-6">
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
                        value="{{ old('reg_id', $reg_id ?? '') }}"
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="e.g. BSE223139"
                    >
                </div>
                @error('reg_id')
                    <p class="text-red-400 text-xs ml-1 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">CNIC</label>
                <div class="input-box flex items-center rounded-2xl px-5 group">
                    <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <input 
                        type="text" 
                        name="cnic" 
                        value="{{ old('cnic') }}"
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                        placeholder="12345-1234567-1"
                    >
                </div>
                @error('cnic')
                    <p class="text-red-400 text-xs ml-1 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="btn-auth w-full flex items-center justify-center py-5 rounded-2xl text-white font-black uppercase tracking-widest text-sm mt-8"
            >
                Verify Identity
            </button>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-xs text-purple-400 hover:text-purple-300 font-semibold uppercase tracking-wider transition-colors">Back to Login</a>
            </div>
        </form>
    </div>
</div>
@endsection
