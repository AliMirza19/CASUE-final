@extends('layouts.dashboard')

@section('title', 'Design Overview - CAUSE Smart Society')
@section('page-title', 'Design Overview')
@section('page-description', 'Summary of your design tasks and approval status')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'gd'])
    </div>

    <!-- Approved Events - Quick Upload -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 bg-gray-50 flex justify-between items-center border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Event Graphics Queue</h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Upload designs for approved events</p>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Event Detail</th>
                        <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Expected Date</th>
                        <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($approvedEvents as $event)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-800">{{ $event->title }}</div>
                                <div class="text-xs text-gray-400 flex items-center mt-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $event->venue }}
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <a href="{{ route('gd.upload', $event->id) }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-cause-purple hover:bg-cause-purple-dark text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-purple-100 hover:shadow-purple-200">
                                    Upload Poster
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-4">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-gray-400 font-bold italic">No pending graphics for approved events.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- My Designs -->
    @if($myGraphics->count() > 0)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">My Portfolio</h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Your uploaded event graphics</p>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Event</th>
                        <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Preview</th>
                        <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($myGraphics as $graphic)
                        <tr class="hover:bg-purple-50/20 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-800">{{ $graphic->event->title }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $graphic->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 text-[10px] font-black uppercase bg-purple-100 text-purple-700 rounded-full">
                                    {{ str_replace('_', ' ', $graphic->design_category) }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($graphic->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span> Approved
                                    </span>
                                @elseif($graphic->status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></span> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-yellow-100 text-yellow-700 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-2 animate-pulse"></span> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php $link = $graphic->image_link ?: ($graphic->image_path ? Storage::url($graphic->image_path) : '#'); @endphp
                                <a href="{{ $link }}" target="_blank" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark font-bold text-xs group transition-all">
                                    View Design
                                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($graphic->status !== 'pending_patron')
                                    <a href="{{ route('gd.view-feedback', $graphic->id) }}" 
                                       class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-[10px] font-black uppercase rounded-lg transition-colors border border-indigo-100">
                                        Feedback
                                    </a>
                                @else
                                    <span class="text-gray-300 text-[10px] font-black uppercase italic">Processing</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="mt-8">
        @include('partials.team-chat')
    </div>
@endsection
