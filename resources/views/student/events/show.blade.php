@extends('layouts.dashboard')

@section('title', 'Event Details - CAUSE Smart Society')
@section('page-title', 'Event Details')
@section('page-description', 'View event submission details')

@section('sidebar')
    <a href="{{ route('student.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('student.events.index') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        My Events
    </a>
@endsection

@section('content')
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('student.events.index') }}" class="text-cause-purple hover:text-purple-700 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to My Events
        </a>
    </div>

    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h2>
                <p class="text-gray-600 mt-2">{{ $event->description }}</p>
            </div>
            <div class="text-right">
                @php
                    $statusColors = [
                        'pending_president' => 'bg-yellow-100 text-yellow-800',
                        'president_approved' => 'bg-blue-100 text-blue-800',
                        'pending_patron' => 'bg-yellow-100 text-yellow-800',
                        'pending_hod' => 'bg-yellow-100 text-yellow-800',
                        'pending_sa' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'revision_needed' => 'bg-orange-100 text-orange-800',
                    ];
                    $statusLabels = [
                        'pending_president' => 'Pending President Review',
                        'president_approved' => 'President Approved - Forward to Patron',
                        'pending_patron' => 'Pending Patron Review',
                        'pending_hod' => 'Pending HOD Review',
                        'pending_sa' => 'Pending SA Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'revision_needed' => 'Revision Needed',
                    ];
                @endphp
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$event->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                </span>
            </div>
        </div>
        
        <!-- Event Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-200">
            <div>
                <p class="text-sm text-gray-500">Expected Date</p>
                <p class="font-semibold text-gray-800">{{ $event->expected_date->format('F d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Venue</p>
                <p class="font-semibold text-gray-800">{{ $event->venue }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Grand Total</p>
                <p class="font-semibold text-cause-purple text-xl">PKR {{ number_format($event->grand_total, 0) }}</p>
            </div>
        </div>
        
        <!-- Guest Speaker & Faculty Mentor -->
        @if($event->guest_speaker_name || $event->guest_speaker_designation || $event->faculty_mentor_id)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($event->guest_speaker_name)
                <div>
                    <p class="text-sm text-gray-500">Guest Speaker Name</p>
                    <p class="font-semibold text-gray-800">{{ $event->guest_speaker_name }}</p>
                </div>
                @endif
                @if($event->guest_speaker_designation)
                <div>
                    <p class="text-sm text-gray-500">Guest Speaker Designation</p>
                    <p class="font-semibold text-gray-800">{{ $event->guest_speaker_designation }}</p>
                </div>
                @endif
                @if($event->facultyMentor)
                <div>
                    <p class="text-sm text-gray-500">Faculty Mentor</p>
                    <p class="font-semibold text-gray-800">{{ $event->facultyMentor->name }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        @if($event->status === 'president_approved')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 mb-3">
                    <strong>Action Required:</strong> Your event has been approved by the President. Please forward it to the Patron for further review.
                </p>
                <form action="{{ route('student.events.forward', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Forward to Patron
                    </button>
                </form>
            </div>
        </div>
        @endif
        
        @if($event->status === 'revision_needed')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-orange-800 mb-3">
                    <strong>Revision Required:</strong> Please review the feedback and update your event submission.
                </p>
                @if($event->rejection_reason)
                <div class="bg-white rounded p-3 mb-3">
                    <p class="text-sm text-gray-600">Feedback:</p>
                    <p class="text-gray-800">{{ $event->rejection_reason }}</p>
                </div>
                @endif
                <a href="{{ route('student.events.edit', $event->id) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Event
                </a>
            </div>
        </div>
        @endif
        
        @if($event->status === 'rejected')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800 font-semibold mb-2">Event Rejected</p>
                @if($event->rejection_reason)
                <p class="text-red-700">Reason: {{ $event->rejection_reason }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Reviewer Feedback / Comments -->
    @if($event->president_comments || $event->patron_comments || $event->hod_comments || $event->sa_comments || $event->rejection_reason)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            Reviewer Feedback
        </h3>
        
        <div class="space-y-4">
            @if($event->president_comments)
            <div class="border-l-4 border-blue-500 bg-blue-50 rounded-r-lg p-4">
                <div class="flex items-center mb-1">
                    <span class="text-sm font-semibold text-blue-800">👤 President</span>
                    @if($event->status === 'revision_needed')
                        <span class="ml-2 px-2 py-0.5 text-xs bg-orange-100 text-orange-700 rounded-full">Revision Requested</span>
                    @endif
                </div>
                <p class="text-blue-900">{{ $event->president_comments }}</p>
            </div>
            @endif

            @if($event->patron_comments)
            <div class="border-l-4 border-purple-500 bg-purple-50 rounded-r-lg p-4">
                <div class="flex items-center mb-1">
                    <span class="text-sm font-semibold text-purple-800">👨‍🏫 Patron</span>
                    @if($event->status === 'rejected' && $event->rejection_reason === $event->patron_comments)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Rejected</span>
                    @endif
                </div>
                <p class="text-purple-900">{{ $event->patron_comments }}</p>
            </div>
            @endif

            @if($event->hod_comments)
            <div class="border-l-4 border-green-500 bg-green-50 rounded-r-lg p-4">
                <div class="flex items-center mb-1">
                    <span class="text-sm font-semibold text-green-800">👨‍💼 HOD</span>
                    @if($event->status === 'rejected' && $event->rejection_reason === $event->hod_comments)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Rejected</span>
                    @endif
                </div>
                <p class="text-green-900">{{ $event->hod_comments }}</p>
            </div>
            @endif

            @if($event->sa_comments)
            <div class="border-l-4 border-indigo-500 bg-indigo-50 rounded-r-lg p-4">
                <div class="flex items-center mb-1">
                    <span class="text-sm font-semibold text-indigo-800">🏛️ Student Affairs</span>
                    @if($event->status === 'rejected' && $event->rejection_reason === $event->sa_comments)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Rejected</span>
                    @endif
                </div>
                <p class="text-indigo-900">{{ $event->sa_comments }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Budget Items -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Budget Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        @if($event->status !== 'pending_president')
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Patron</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">HOD</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($event->items as $index => $item)
                    <tr class="{{ (!$item->is_approved_by_patron || ($item->is_approved_by_hod === false)) ? 'bg-red-50/50' : '' }}">
                        <td class="px-6 py-4 text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $item->item_name }}</div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-800">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right text-gray-800 text-sm">
                            <span class="text-xs text-gray-400 mr-1">PKR</span>{{ number_format($item->unit_rate, 0) }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            <span class="text-xs text-gray-400 mr-1">PKR</span>{{ number_format($item->total_amount, 0) }}
                        </td>
                        @if($event->status !== 'pending_president')
                        <td class="px-6 py-4 text-center">
                            @if($item->is_approved_by_patron)
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase">Appr</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[10px] font-bold uppercase">Rej</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $hodReviewed = !in_array($event->status, ['pending_president', 'pending_patron', 'pending_hod']);
                            @endphp
                            @if($hodReviewed)
                                @if($item->is_approved_by_hod)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase">Appr</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[10px] font-bold uppercase">Rej</span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-400 rounded-full text-[10px] font-bold uppercase">Wait</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @if($item->patron_comment || $item->hod_comment)
                    <tr class="bg-gray-50">
                        <td colspan="{{ $event->status !== 'pending_president' ? 7 : 5 }}" class="px-6 py-2">
                            <div class="space-y-1">
                                @if($item->patron_comment)
                                <div class="text-xs text-purple-800">
                                    <strong>Patron:</strong> {{ $item->patron_comment }}
                                </div>
                                @endif
                                @if($item->hod_comment)
                                <div class="text-xs text-green-800">
                                    <strong>HOD:</strong> {{ $item->hod_comment }}
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-800">Grand Total:</td>
                        <td class="px-6 py-4 text-right font-bold text-cause-purple text-lg">PKR {{ number_format($event->grand_total, 0) }}</td>
                        @if($event->status !== 'pending_president')
                        <td colspan="2"></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Approval Workflow Progress -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Approval Workflow</h3>
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    ['key' => 'pending_president', 'label' => 'President', 'icon' => '👤'],
                    ['key' => 'pending_patron', 'label' => 'Patron', 'icon' => '👨‍🏫'],
                    ['key' => 'pending_hod', 'label' => 'HOD', 'icon' => '👨‍💼'],
                    ['key' => 'pending_sa', 'label' => 'SA', 'icon' => '🏛️'],
                    ['key' => 'approved', 'label' => 'Approved', 'icon' => '✅'],
                ];
                $currentIndex = array_search($event->status, array_column($steps, 'key'));
                if ($event->status === 'president_approved') $currentIndex = 0.5;
            @endphp
            
            @foreach($steps as $index => $step)
                <div class="flex flex-col items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                        {{ $index < $currentIndex ? 'bg-green-500 text-white' : 
                           ($index == floor($currentIndex) ? 'bg-cause-purple text-white' : 'bg-gray-200 text-gray-500') }}">
                        {{ $step['icon'] }}
                    </div>
                    <span class="text-xs mt-2 text-gray-600">{{ $step['label'] }}</span>
                </div>
                @if($index < count($steps) - 1)
                    <div class="flex-1 h-1 mx-2 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
