@extends('layouts.app')

@section('title', 'Verify Identity - Secure Password Reset | CAUSE')

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

    /* Animated gradient orbs */
    .orb-1 {
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.15), transparent 70%);
        border-radius: 50%;
        top: -100px;
        right: -100px;
        animation: float-orb 8s ease-in-out infinite;
    }

    .orb-2 {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(167, 139, 250, 0.1), transparent 70%);
        border-radius: 50%;
        bottom: -80px;
        left: -80px;
        animation: float-orb 10s ease-in-out infinite reverse;
    }

    @keyframes float-orb {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(30px, -20px) scale(1.1); }
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

    .btn-verify {
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-verify::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s;
    }

    .btn-verify:hover::before {
        left: 100%;
    }

    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -5px rgba(109, 40, 217, 0.5);
    }

    .btn-verify:active {
        transform: translateY(0);
    }

    .shield-icon {
        filter: drop-shadow(0 0 10px rgba(124, 58, 237, 0.4));
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeIn 0.8s ease-out forwards;
    }

    .alert-animate {
        animation: fadeInDown 0.5s ease-out forwards;
    }

    .text-glow {
        text-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
    }

    /* Subtle pulse on the shield */
    @keyframes pulse-glow {
        0%, 100% { opacity: 0.7; }
        50% { opacity: 1; }
    }

    .pulse-glow {
        animation: pulse-glow 3s ease-in-out infinite;
    }

    /* Info tag styling */
    .info-tag {
        background: rgba(124, 58, 237, 0.1);
        border: 1px solid rgba(124, 58, 237, 0.2);
    }
</style>
@endpush

@section('content')
<div class="h-screen w-full flex items-center justify-center main-bg p-6 lg:p-0">
    <!-- Floating Orbs -->
    <div class="orb-1"></div>
    <div class="orb-2"></div>

    <div class="w-full max-w-7xl flex flex-col lg:flex-row items-center justify-between lg:px-20 relative z-10">
        
        <!-- Left Side: Branding & Info -->
        <div class="hidden lg:flex lg:w-1/2 flex-col items-center text-center animate-in">
            <!-- Shield Icon -->
            <div class="mb-10 shield-icon pulse-glow">
                <svg class="w-32 h-32 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>

            <div class="space-y-4">
                <h1 class="text-5xl font-black text-white leading-tight tracking-tight uppercase">
                    SECURE<br>
                    <span class="text-purple-600 text-glow">PASSWORD</span><br>
                    RESET
                </h1>
                
                <div class="flex items-center justify-center space-x-4 py-4">
                    <div class="h-px w-24 bg-gradient-to-r from-transparent to-white/20"></div>
                    <div class="flex space-x-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                    </div>
                    <div class="h-px w-24 bg-gradient-to-l from-transparent to-white/20"></div>
                </div>

                <p class="text-white/30 text-sm font-bold tracking-[0.4em] uppercase">
                    2-Step Identity Verification
                </p>

                <!-- How it works -->
                <div class="mt-10 space-y-4 max-w-sm mx-auto text-left">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-purple-600/20 border border-purple-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-purple-400 text-xs font-black">1</span>
                        </div>
                        <p class="text-white/40 text-sm">Enter your <span class="text-purple-400 font-semibold">Registration ID</span> and <span class="text-purple-400 font-semibold">CNIC</span> to verify identity</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-purple-600/20 border border-purple-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-purple-400 text-xs font-black">2</span>
                        </div>
                        <p class="text-white/40 text-sm">A reset link will be sent to your <span class="text-purple-400 font-semibold">registered university email</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Verification Card -->
        <div class="w-full lg:w-[480px] glass-card p-10 lg:p-14 animate-in" style="animation-delay: 0.2s">
            
            <!-- Mobile shield icon -->
            <div class="lg:hidden flex justify-center mb-6">
                <svg class="w-16 h-16 text-purple-500 shield-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>

            <div class="mb-10 pt-2 relative">
                <div class="info-tag inline-block px-3 py-1 rounded-full mb-4">
                    <span class="text-[10px] font-black text-purple-400 uppercase tracking-widest">Step 1 of 2</span>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Verify Identity</h2>
                <p class="text-gray-500 font-medium text-sm">Confirm your identity to receive a password reset link</p>
                <div class="absolute -top-4 -right-4 grid grid-cols-4 gap-1.5 opacity-20">
                    @for($i=0; $i<16; $i++)
                        <div class="w-1 h-1 rounded-full bg-purple-400"></div>
                    @endfor
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl mb-8 text-sm font-bold flex items-start alert-animate">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-8 text-sm font-bold flex items-start alert-animate">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('password.verify-identity') }}" method="POST" class="space-y-6" id="verify-form">
                @csrf
                
                <!-- Reg ID Field -->
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">Roll No / Registration ID</label>
                    <div class="input-box flex items-center rounded-2xl px-5 group">
                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="reg_id" 
                            id="reg_id"
                            value="{{ old('reg_id') }}"
                            required 
                            class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                            placeholder="e.g. FA21-BSE-069"
                        >
                    </div>
                    @error('reg_id')
                        <p class="text-red-400 text-xs ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CNIC Field -->
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-purple-500 uppercase tracking-widest ml-1">CNIC Number</label>
                    <div class="input-box flex items-center rounded-2xl px-5 group">
                        <svg class="w-5 h-5 text-gray-500 group-focus-within:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="cnic" 
                            id="cnic"
                            value="{{ old('cnic') }}"
                            required 
                            maxlength="15"
                            class="w-full bg-transparent border-none focus:ring-0 py-4 px-4 text-white font-medium placeholder-gray-600"
                            placeholder="35201-1234567-1"
                        >
                    </div>
                    @error('cnic')
                        <p class="text-red-400 text-xs ml-1 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-[11px] ml-1">Format: XXXXX-XXXXXXX-X</p>
                </div>

                <button 
                    type="submit" 
                    id="verify-btn"
                    class="btn-verify w-full flex items-center justify-center py-5 rounded-2xl text-white font-black uppercase tracking-widest text-sm"
                >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span id="btn-text">Verify & Send Reset Link</span>
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
</div>
@endsection

@push('scripts')
<script>
    // Auto-format CNIC input
    document.getElementById('cnic').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 13) value = value.slice(0, 13);
        
        let formatted = '';
        if (value.length > 0) formatted += value.slice(0, 5);
        if (value.length > 5) formatted += '-' + value.slice(5, 12);
        if (value.length > 12) formatted += '-' + value.slice(12, 13);
        
        e.target.value = formatted;
    });

    // Loading state on submit
    document.getElementById('verify-form').addEventListener('submit', function() {
        const btn = document.getElementById('verify-btn');
        const btnText = document.getElementById('btn-text');
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btnText.textContent = 'Verifying...';
    });
</script>
@endpush
