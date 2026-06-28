@extends('layouts.dashboard')

@section('title', '{{ $event->title }} - CAUSE Smart Society')
@section('page-title', 'Event Details')
@section('page-description', $event->title)

@section('sidebar')
    @include('partials.faculty-sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('faculty.events') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Events
    </a>
    
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-cause-purple to-cause-purple-dark">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $event->title }}</h2>
                    <p class="text-purple-200 mt-1">Organized by {{ $event->student->name ?? 'Unknown' }}</p>
                </div>
                @if($event->expected_date >= now())
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">Upcoming</span>
                @else
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600">Past Event</span>
                @endif
            </div>
        </div>
        
        <div class="p-6">
            <!-- Event Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-gray-800">{{ $event->expected_date->format('F d, Y') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Venue</p>
                        <p class="font-semibold text-gray-800">{{ $event->venue ?? 'To Be Decided' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Budget</p>
                        <p class="font-semibold text-gray-800">Rs. {{ number_format($event->grand_total ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                <p class="text-gray-600 leading-relaxed">{{ $event->description }}</p>
            </div>
            
            <!-- Guest Speaker & Faculty Mentor -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Guest Speaker & Faculty Mentor</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($event->guest_speaker_name)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $event->guest_speaker_name }}</p>
                            <p class="text-xs text-gray-500">Guest Speaker</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($event->guest_speaker_designation)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $event->guest_speaker_designation }}</p>
                            <p class="text-xs text-gray-500">Designation</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($event->facultyMentor)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-cause-purple rounded-full flex items-center justify-center text-white font-medium mr-3">
                            {{ substr($event->facultyMentor->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $event->facultyMentor->name }}</p>
                            <p class="text-xs text-gray-500">Faculty Mentor</p>
                        </div>
                    </div>
                    @endif
                    
                    @if(!$event->guest_speaker_name && !$event->guest_speaker_designation && !$event->facultyMentor)
                    <p class="text-gray-500 col-span-3">No guest speaker or faculty mentor assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Budget Breakdown -->
    @if($event->items && $event->items->count() > 0)
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Budget Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($event->items as $index => $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->item_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 text-right">Rs. {{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-semibold text-gray-800">Grand Total:</td>
                        <td class="px-6 py-4 text-right font-bold text-cause-purple text-lg">Rs. {{ number_format($event->grand_total ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
