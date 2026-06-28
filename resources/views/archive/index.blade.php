@extends('layouts.dashboard')

@section('title', 'Digital Archive - CAUSE')
@section('page-title', 'Institutional Memory & Archive')
@section('page-description', 'Access historical data, financial records, and institutional knowledge.')

@section('content')
<div class="max-w-7xl mx-auto space-y-12 pb-20">
    <!-- Hero Section -->
    <div class="relative rounded-[3rem] overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 p-12 shadow-2xl">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-indigo-500/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-purple-500/20 rounded-full blur-[100px]"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 text-center md:text-left">
                <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/10">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em]">Secure Digital Vault</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tighter">
                    Institutional <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-200">Memory</span>
                </h1>
                <p class="text-indigo-100/60 max-w-lg text-lg font-medium leading-relaxed">
                    Explore past academic terms and archived events. Maintain the legacy of CAUSE society through documented excellence.
                </p>
            </div>
            <div class="w-48 h-48 bg-white/5 backdrop-blur-2xl rounded-[3rem] border border-white/10 flex items-center justify-center shadow-inner">
                <svg class="w-24 h-24 text-indigo-300/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Terms Grid -->
    <div class="space-y-6">
        <div class="flex items-center justify-between px-4">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center">
                <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </span>
                Academic Terms
            </h2>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ count($terms) }} Folders</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($terms as $term)
                <a href="{{ route('archive.term', $term->id) }}" class="group relative">
                    <div class="h-48 rounded-[2.5rem] bg-white border border-gray-100 shadow-xl group-hover:shadow-2xl group-hover:-translate-y-2 transition-all duration-500 overflow-hidden">
                        <!-- Folder Tab Decoration -->
                        <div class="absolute top-0 left-8 w-16 h-2 bg-indigo-500 rounded-b-full"></div>
                        
                        <div class="p-8 flex flex-col h-full justify-between">
                            <div class="space-y-1">
                                <h3 class="text-xl font-black text-gray-800">{{ $term->term_name }}</h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $term->term_code }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 border-2 border-white flex items-center justify-center text-[10px] font-black text-indigo-600">
                                        {{ $term->events()->whereIn('status', ['approved', 'completed'])->count() }}
                                    </div>
                                    <div class="px-3 py-1 bg-gray-50 rounded-full text-[10px] font-black text-gray-400 uppercase ml-4">
                                        Files
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-gray-50 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center transition-colors duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
