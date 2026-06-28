@extends('layouts.dashboard')

@section('title', "Events in {$term->term_name} - Archive")
@section('page-title', $term->term_name)
@section('page-description', "Completed events and documentation for the {$term->term_name} academic term.")

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-3 text-sm font-bold uppercase tracking-widest">
        <a href="{{ route('archive.index') }}" class="text-indigo-400 hover:text-indigo-600 transition-colors">Archive</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-500">{{ $term->term_name }}</span>
    </nav>

    <!-- Header Section -->
    <div class="bg-white rounded-[3rem] p-10 shadow-xl border border-gray-100 flex flex-col md:flex-row items-center gap-10">
        <div class="w-32 h-32 rounded-[2.5rem] bg-indigo-50 flex items-center justify-center text-indigo-500 flex-shrink-0">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
            </svg>
        </div>
        <div class="space-y-2 text-center md:text-left flex-1">
            <h2 class="text-3xl font-black text-gray-800">{{ $term->term_name }} Archives</h2>
            <p class="text-gray-500 font-medium">Browse completed events to access their reports, financial records, and creative assets.</p>
            <div class="flex flex-wrap gap-4 mt-4 justify-center md:justify-start">
                <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $events->count() }} Completed Events</span>
                <span class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $term->term_code }}</span>
            </div>
        </div>
    </div>

    @if($termDocuments->isNotEmpty())
    <div class="space-y-6 mt-12">
        <div class="flex items-center justify-between px-4">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                Term Reports & Financials
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($termDocuments as $doc)
                <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-lg flex items-center justify-between hover:border-emerald-200 transition-all group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-800 text-sm">{{ $doc->original_filename }}</h4>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $doc->description }}</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-black uppercase hover:bg-emerald-600 hover:text-white transition-all">
                        View
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Events Grid -->
    @if($events->isEmpty())
        <div class="bg-white rounded-[3rem] p-20 text-center shadow-xl border border-gray-100">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2">No Archived Events</h3>
            <p class="text-gray-500 max-w-md mx-auto">We couldn't find any completed events for this academic term in the digital archive.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($events as $event)
                <a href="{{ route('archive.event', $event->id) }}" class="group relative">
                    <div class="rounded-[2.5rem] bg-white border border-gray-100 shadow-xl group-hover:shadow-2xl transition-all duration-500 overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 group-hover:scale-110 transition-transform duration-700">
                                <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 4" />
                                </svg>
                            </div>
                            <div class="w-16 h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                        </div>
                        <div class="p-8 space-y-4">
                            <div>
                                <h3 class="text-xl font-black text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $event->title }}</h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Archived {{ $event->updated_at->format('M d, Y') }}</p>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed font-medium">
                                {{ $event->description ?: 'No description provided for this archived event.' }}
                            </p>
                            <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-xs font-bold text-indigo-500">{{ $event->documents->count() }} documents found</span>
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
