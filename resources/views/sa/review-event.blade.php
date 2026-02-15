@extends('layouts.dashboard')

@section('title', 'Final Review - CAUSE Smart Society')
@section('page-title', 'Final Event Review')
@section('page-description', 'Grant final approval for this event')

@section('sidebar')
    <a href="{{ route('sa.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('sa.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Approval Status Banner -->
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>This event has been approved by President, Patron, and HOD. Your approval is the final step.</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $event->title }}</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Expected Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($event->expected_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Venue</p>
                        <p class="font-medium text-gray-800">{{ $event->venue }}</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-gray-700">{{ $event->description }}</p>
                </div>
                
                <!-- Guest Speaker & Faculty Mentor -->
                @if($event->guest_speaker_name || $event->guest_speaker_designation || $event->facultyMentor)
                <div class="mb-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-3">Guest Speaker & Faculty Mentor</p>
                    <div class="grid grid-cols-2 gap-4">
                        @if($event->guest_speaker_name)
                        <div>
                            <p class="text-sm text-gray-500">Guest Speaker Name</p>
                            <p class="font-medium text-gray-800">{{ $event->guest_speaker_name }}</p>
                        </div>
                        @endif
                        @if($event->guest_speaker_designation)
                        <div>
                            <p class="text-sm text-gray-500">Guest Speaker Designation</p>
                            <p class="font-medium text-gray-800">{{ $event->guest_speaker_designation }}</p>
                        </div>
                        @endif
                        @if($event->facultyMentor)
                        <div>
                            <p class="text-sm text-gray-500">Faculty Mentor</p>
                            <p class="font-medium text-gray-800">{{ $event->facultyMentor->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Budget Items -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800">Budget Items</h4>
                </div>
                
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($event->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $item->item_name }}</td>
                                <td class="px-6 py-4 text-right text-gray-800">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right text-gray-800">PKR {{ number_format($item->unit_rate, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">PKR {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-800">Grand Total:</td>
                            <td class="px-6 py-4 text-right font-bold text-cause-purple text-lg">PKR {{ number_format($event->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Student</h4>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-cause-purple rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold text-lg">{{ substr($event->student->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $event->student->name }}</p>
                        <p class="text-sm text-gray-500">{{ $event->student->reg_id }}</p>
                        @if($event->student->current_semester)
                        <p class="text-sm text-gray-500">{{ $event->student->current_semester }} Semester</p>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-2 text-sm border-t border-gray-100 pt-3 mt-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-gray-800">{{ $event->student->email }}</span>
                    </div>
                    @if($event->student->father_name)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Father Name:</span>
                        <span class="text-gray-800">{{ $event->student->father_name }}</span>
                    </div>
                    @endif
                    @if($event->student->cnic)
                    <div class="flex justify-between">
                        <span class="text-gray-500">CNIC:</span>
                        <span class="text-gray-800">{{ $event->student->cnic }}</span>
                    </div>
                    @endif
                    @if($event->student->contact_number)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Contact:</span>
                        <span class="text-gray-800">{{ $event->student->contact_number }}</span>
                    </div>
                    @endif
            </div>

            <!-- Approval History -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Approval History</h4>
                <div class="space-y-3">
                    <div class="flex items-center text-green-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm">President Approved</span>
                    </div>
                    <div class="flex items-center text-green-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm">Patron Approved</span>
                    </div>
                    <div class="flex items-center text-green-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm">HOD Approved (Budget Allocated)</span>
                    </div>
                    <div class="flex items-center text-yellow-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">Awaiting SA Final Approval</span>
                    </div>
                </div>
            </div>

            <!-- Action Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Final Decision</h4>
                
                <form action="{{ route('sa.approve', $event->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                        <textarea name="comments" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="Add comments..."></textarea>
                    </div>
                    
                    <div class="space-y-3">
                        <button type="submit" name="action" value="approve"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Grant Final Approval
                        </button>
                        
                        <button type="submit" name="action" value="reject"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
