@extends('layouts.dashboard')

@section('title', 'Event Details - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <a href="{{ route('president.my-events.index') }}" class="text-gray-600 hover:text-gray-900 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Event Details</h1>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            @php
                $statusConfig = [
                    'pending_patron' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending Patron Review', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'pending_hod' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Pending HOD Approval', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                $config = $statusConfig[$event->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($event->status), 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-lg {{ $config['bg'] }} {{ $config['text'] }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                </svg>
                <span class="font-semibold">{{ $config['label'] }}</span>
            </div>
        </div>

        <!-- Event Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $event->title }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Expected Date</label>
                    <div class="flex items-center text-gray-900">
                        <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $event->expected_date->format('F d, Y') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Grand Total</label>
                    <div class="flex items-center text-gray-900 font-bold text-lg">
                        <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Rs. {{ number_format($event->grand_total, 2) }}
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                <p class="text-gray-700 leading-relaxed">{{ $event->description }}</p>
            </div>

            @if($event->guest_speaker_name)
                <div class="bg-purple-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Guest Speaker
                    </h3>
                    <p class="text-gray-900 font-medium">{{ $event->guest_speaker_name }}</p>
                    @if($event->guest_speaker_designation)
                        <p class="text-gray-600 text-sm">{{ $event->guest_speaker_designation }}</p>
                    @endif
                </div>
            @endif

            @if($event->facultyMentor)
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Faculty Mentor
                    </h3>
                    <p class="text-gray-900 font-medium">{{ $event->facultyMentor->name }}</p>
                    <p class="text-gray-600 text-sm">{{ $event->facultyMentor->email }}</p>
                </div>
            @endif>
        </div>

        <!-- Budget Items -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Budget Items
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($event->items as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->item_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">Rs. {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Grand Total:</td>
                            <td class="px-4 py-3 text-sm font-bold text-cause-purple text-right">Rs. {{ number_format($event->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Comments Section -->
        @if($event->patron_comments || $event->hod_comments)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Review Comments
                </h3>
                
                @if($event->patron_comments)
                    <div class="mb-4 p-4 bg-yellow-50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 mb-1">Patron Comments:</p>
                        <p class="text-gray-900">{{ $event->patron_comments }}</p>
                    </div>
                @endif
                
                @if($event->hod_comments)
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 mb-1">HOD Comments:</p>
                        <p class="text-gray-900">{{ $event->hod_comments }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
