@extends('layouts.dashboard')

@section('title', 'Event Details - CAUSE Smart Society')
@section('page-title', 'Event Details')
@section('page-description', 'View event submission details')

@section('sidebar')
    @include('partials.student-sidebar')
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
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'revision_needed' => 'bg-orange-100 text-orange-800',
                    ];
                    $statusLabels = [
                        'pending_president' => 'Pending President Review',
                        'president_approved' => 'President Approved - Forward to Patron',
                        'pending_patron' => 'Pending Patron Review',
                        'pending_hod' => 'Pending HOD Review',
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
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-semibold text-gray-800">{{ $statusLabels[$event->status] ?? ucfirst($event->status) }}</p>
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
                @if($event->guest_speaker_profile_link)
                <div>
                    <p class="text-sm text-gray-500">Guest Speaker Profile</p>
                    <a href="{{ $event->guest_speaker_profile_link }}" target="_blank" class="font-semibold text-cause-purple hover:underline flex items-center">
                        View Profile
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
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

        @if($event->status === 'approved')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-bold text-green-800">Event Fully Approved!</h4>
                    <p class="text-green-700 mt-1">Your event has been authorized by the HOD. You can now download the formal approval form.</p>
                </div>
                <a href="{{ route('student.events.download-approval', $event->id) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-bold flex items-center shadow-lg transition-all hover:scale-105">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Download Approval Form
                </a>
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

    <!-- Premium Event Requirements / Budget Breakdown -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-cause-purple-dark flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white tracking-tight">Event Requirements & Allocation</h3>
            </div>
            @if($event->status === 'approved')
                <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-black rounded-full backdrop-blur-sm uppercase tracking-widest border border-green-500/30">Finalized</span>
            @else
                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-black rounded-full backdrop-blur-sm uppercase tracking-widest border border-yellow-500/30">In Review</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-separate border-spacing-0">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-left">Item Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Quantity</th>
                        @if($event->status !== 'pending_president')
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Patron Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">HOD Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($event->items as $index => $item)
                    @php
                        $isRejected = (!$item->is_approved_by_patron || ($item->is_approved_by_hod === false));
                    @endphp
                    <tr class="group transition-all duration-200 {{ $isRejected ? 'bg-red-50/30' : 'hover:bg-gray-50/50' }}">
                        <td class="px-8 py-6">
                            <div class="flex items-center">
                                <span class="text-xs font-bold text-gray-300 mr-4 tabular-nums">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div class="font-bold text-gray-800 text-base {{ $isRejected ? 'text-red-900/70' : '' }}">{{ $item->item_name }}</div>
                                    @if($item->patron_comment || $item->hod_comment)
                                        <div class="mt-2 space-y-1">
                                            @if($item->patron_comment)
                                                <div class="flex items-start space-x-1">
                                                    <span class="text-[10px] font-black text-purple-600 uppercase mt-0.5">Patron:</span>
                                                    <span class="text-xs text-purple-800 italic">"{{ $item->patron_comment }}"</span>
                                                </div>
                                            @endif
                                            @if($item->hod_comment)
                                                <div class="flex items-start space-x-1">
                                                    <span class="text-[10px] font-black text-teal-600 uppercase mt-0.5">HOD:</span>
                                                    <span class="text-xs text-teal-800 italic">"{{ $item->hod_comment }}"</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-gray-700 font-bold tabular-nums">{{ $item->quantity }}</span>
                        </td>
                        @if($event->status !== 'pending_president')
                        <td class="px-6 py-6">
                            <div class="flex justify-center">
                                @if($item->is_approved_by_patron)
                                    <div class="flex items-center text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[10px] font-black uppercase tracking-wider">Approved</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-100">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[10px] font-black uppercase tracking-wider">Rejected</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-center">
                                @php
                                    $hodReviewed = !in_array($event->status, ['pending_president', 'pending_patron', 'pending_hod']);
                                @endphp
                                @if($hodReviewed)
                                    @if($item->is_approved_by_hod)
                                        <div class="flex items-center text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            <span class="text-[10px] font-black uppercase tracking-wider">Approved</span>
                                        </div>
                                    @else
                                        <div class="flex items-center text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-100">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                            <span class="text-[10px] font-black uppercase tracking-wider">Rejected</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                        <span class="text-[10px] font-black uppercase tracking-wider">Awaiting Review</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
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
