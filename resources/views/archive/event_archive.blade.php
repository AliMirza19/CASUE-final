@extends('layouts.dashboard')

@section('title', "{$event->title} - Archive")
@section('page-title', 'Event Archive')
@section('page-description', "Access all documentation, financial reports, and media for {$event->title}.")

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-3 text-sm font-bold uppercase tracking-widest">
        <a href="{{ route('archive.index') }}" class="text-indigo-400 hover:text-indigo-600 transition-colors">Archive</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('archive.term', $event->term_id) }}" class="text-indigo-400 hover:text-indigo-600 transition-colors">{{ $event->term->term_name }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-500">{{ $event->title }}</span>
    </nav>

    <!-- Event Banner -->
    <div class="relative h-64 rounded-[3.5rem] overflow-hidden bg-indigo-900 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900 via-indigo-900/80 to-transparent z-10"></div>
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 w-1/2 h-full z-0 opacity-20">
            <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full scale-150 rotate-12">
                <circle cx="200" cy="200" r="150" stroke="white" stroke-width="0.5" />
                <circle cx="200" cy="200" r="100" stroke="white" stroke-width="0.5" stroke-dasharray="10 10" />
            </svg>
        </div>
        
        <div class="relative z-20 h-full p-12 flex flex-col justify-end">
            <div class="flex items-center space-x-4 mb-4">
                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black text-white uppercase tracking-widest border border-white/20">Archived Vault</span>
                <span class="px-4 py-1.5 bg-emerald-500/20 backdrop-blur-md rounded-full text-[10px] font-black text-emerald-300 uppercase tracking-widest border border-emerald-500/20">Status: Completed</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter">{{ $event->title }}</h1>
        </div>
    </div>

    <!-- Storage Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @php
            $sections = [
                ['key' => 'financial_report', 'title' => 'Financial Reports', 'icon' => '💰', 'color' => 'emerald', 'desc' => 'Budgets, expense tracking, and receipts.'],
                ['key' => 'approval_form', 'title' => 'Approval Forms', 'icon' => '📝', 'color' => 'blue', 'desc' => 'Signed digital approval forms and unit rates.'],
                ['key' => 'general_documentation', 'title' => 'Documentation', 'icon' => '📁', 'color' => 'indigo', 'desc' => 'Event descriptions, planning docs, and minutes.'],
                ['key' => 'poster_graphic', 'title' => 'Creative Assets', 'icon' => '🎨', 'color' => 'purple', 'desc' => 'Posters, social media graphics, and thumbnails.'],
                ['key' => 'event_media', 'title' => 'Event Media', 'icon' => '📸', 'color' => 'rose', 'desc' => 'Photography, videography, and highlight reels.'],
            ];
        @endphp

        @foreach($sections as $section)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden flex flex-col">
                <div class="p-8 bg-gray-50/50 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-5">
                        <div class="w-14 h-14 rounded-2xl bg-{{ $section['color'] }}-100 text-{{ $section['color'] }}-600 flex items-center justify-center text-2xl shadow-inner">
                            {{ $section['icon'] }}
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-800">{{ $section['title'] }}</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $section['desc'] }}</p>
                        </div>
                    </div>
                    <span class="px-4 py-2 bg-white border border-gray-100 rounded-2xl text-[10px] font-black text-{{ $section['color'] }}-600 shadow-sm uppercase tracking-widest">
                        {{ isset($documents[$section['key']]) ? $documents[$section['key']]->count() : 0 }} Files
                    </span>
                </div>

                <div class="p-8 flex-1">
                    @if(isset($documents[$section['key']]) && $documents[$section['key']]->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($documents[$section['key']] as $doc)
                                <div class="group flex items-center justify-between p-4 bg-white border border-gray-50 rounded-2xl hover:border-{{ $section['color'] }}-200 hover:bg-{{ $section['color'] }}-50 transition-all duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-white transition-colors">
                                            @if($section['key'] === 'poster_graphic')
                                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @elseif($section['key'] === 'financial_report')
                                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700 group-hover:text-{{ $section['color'] }}-900 truncate max-w-[200px]">{{ $doc->original_filename }}</span>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">{{ $doc->created_at->format('M d, Y') }} &bull; {{ $doc->uploader->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="w-8 h-8 rounded-lg bg-gray-50 group-hover:bg-{{ $section['color'] }}-600 group-hover:text-white flex items-center justify-center transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center py-10 opacity-30">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9l-2-2H5a2 2 0 01-2 2v11a2 2 0 012 2z"></path></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest">No Documents Available</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
