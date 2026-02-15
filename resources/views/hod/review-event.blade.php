@extends('layouts.dashboard')

@section('title', 'Review Event - CAUSE Smart Society')
@section('page-title', 'Final Review')
@section('page-description', 'Review and approve event with budget allocation')

@section('sidebar')
    <a href="{{ route('hod.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('hod.budget') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Manage Budget
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('hod.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Budget Warning -->
    @if($event->grand_total > $budget->remaining_amount)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>This event's budget (PKR {{ number_format($event->grand_total, 2) }}) exceeds remaining budget (PKR {{ number_format($budget->remaining_amount, 2) }})</span>
    </div>
    @endif

    <form action="{{ route('hod.approve', $event->id) }}" method="POST">
    @csrf
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
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-lg font-semibold text-gray-800">Budget Details</h4>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="budget-table">
                        <thead>
                            <tr class="bg-[#f2f4f7] border-b border-gray-200">
                                <th class="px-6 py-4 text-sm font-bold text-[#4a5568]">Item (Patron Approved)</th>
                                <th class="px-6 py-4 text-sm font-bold text-[#4a5568] w-32 text-center">Rate</th>
                                <th class="px-6 py-4 text-sm font-bold text-[#4a5568] w-32 text-center">Amount</th>
                                <th class="px-6 py-4 text-sm font-bold text-[#4a5568] w-48 text-center">HOD Action</th>
                                <th class="px-6 py-4 text-sm font-bold text-[#4a5568]">Reason (If Rejected)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($event->items->filter(fn($item) => $item->is_approved_by_patron) as $index => $item)
                                <tr class="item-row border-b border-gray-100" data-index="{{ $index }}">
                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                    <td class="px-6 py-6 text-sm text-gray-800">
                                        {{ $item->item_name }}
                                        <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" step="1" min="1"
                                            class="qty-input w-16 text-center px-1 py-1 border border-gray-400 rounded focus:ring-1 focus:ring-cause-purple transition-all outline-none ml-2">
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <input type="number" name="items[{{ $item->id }}][unit_rate]" value="{{ (int)$item->unit_rate }}" step="0.01" min="0"
                                            class="rate-input w-24 text-center px-1 py-1 border border-gray-400 rounded focus:ring-1 focus:ring-cause-purple transition-all outline-none">
                                    </td>
                                    <td class="px-6 py-6 text-center tabular-nums">
                                        <span class="row-total font-bold text-[#63499a] text-base">{{ number_format($item->total_amount, 0) }}</span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex items-center justify-center space-x-4 text-sm font-medium text-gray-700">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="items[{{ $item->id }}][is_approved_by_hod]" value="1" 
                                                    {{ $item->is_approved_by_hod ? 'checked' : '' }}
                                                    class="approve-radio w-4 h-4 text-blue-600 focus:ring-blue-500 mr-1.5 focus:ring-offset-0">
                                                Appr
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="items[{{ $item->id }}][is_approved_by_hod]" value="0" 
                                                    {{ !$item->is_approved_by_hod ? 'checked' : '' }}
                                                    class="reject-radio w-4 h-4 text-red-600 focus:ring-red-500 mr-1.5 focus:ring-offset-0">
                                                Rej
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <textarea name="items[{{ $item->id }}][hod_comment]" rows="1"
                                            class="reason-input w-full text-xs px-2 py-1 bg-white border border-gray-300 rounded focus:ring-1 focus:ring-cause-purple outline-none" 
                                            placeholder="Reason (if rejected)">{{ $item->hod_comment }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Patron Rejected Items -->
                @php $rejectedItems = $event->items->filter(fn($item) => !$item->is_approved_by_patron); @endphp
                @if($rejectedItems->count() > 0)
                <div class="px-6 py-4 bg-[#fff1f2] border-t border-gray-100">
                    <p class="text-[15px] font-bold text-[#c81e1e]">
                        Patron Rejected Items (Not editable): 
                        @foreach($rejectedItems as $item)
                            Rejected: {{ $item->item_name }}. Reason: {{ $item->patron_comment ?? 'No reason provided.' }}
                            @if(!$loop->last) | @endif
                        @endforeach
                    </p>
                </div>
                @endif

                <div class="bg-[#f1f3f6] px-6 py-6 flex items-center border-t border-gray-200">
                    <div class="text-base font-black text-black">FINAL BUDGET:</div>
                    <div class="ml-24 text-2xl font-black text-[#15803d] tabular-nums">
                        <span id="grand-total">{{ (int)$event->grand_total }}</span>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const table = document.getElementById('budget-table');
                    const qtyInputs = table.querySelectorAll('.qty-input');
                    const rateInputs = table.querySelectorAll('.rate-input');
                    const actionRadios = table.querySelectorAll('.approve-radio, .reject-radio');
                    const grandTotalSpan = document.getElementById('grand-total');

                    function calculateTotals() {
                        let grandTotal = 0;
                        const rows = table.querySelectorAll('.item-row');
                        
                        rows.forEach(row => {
                            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                            const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
                            const approved = row.querySelector('.approve-radio').checked;
                            const rowTotalSpan = row.querySelector('.row-total');
                            
                            const total = qty * rate;
                            rowTotalSpan.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            
                            if (approved) {
                                grandTotal += total;
                                rowTotalSpan.classList.remove('text-red-500', 'line-through');
                                rowTotalSpan.classList.add('text-[#63499a]');
                            } else {
                                rowTotalSpan.classList.add('text-red-500', 'line-through');
                                rowTotalSpan.classList.remove('text-[#63499a]');
                            }
                        });
                        
                        grandTotalSpan.textContent = grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }

                    qtyInputs.forEach(input => input.addEventListener('input', calculateTotals));
                    rateInputs.forEach(input => input.addEventListener('input', calculateTotals));
                    actionRadios.forEach(radio => radio.addEventListener('change', calculateTotals));
                    
                    // Initial calculation
                    calculateTotals();
                });
            </script>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Budget Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Budget Status</h4>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Remaining Budget</span>
                        <span class="font-semibold text-green-600">PKR {{ number_format($budget->remaining_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Event Cost</span>
                        <span class="font-semibold {{ $event->grand_total > $budget->remaining_amount ? 'text-red-600' : 'text-gray-800' }}">
                            PKR {{ number_format($event->grand_total, 2) }}
                        </span>
                    </div>
                    <hr>
                    <div class="flex justify-between">
                        <span class="text-gray-600">After Approval</span>
                        <span class="font-semibold {{ ($budget->remaining_amount - $event->grand_total) < 0 ? 'text-red-600' : 'text-green-600' }}">
                            PKR {{ number_format($budget->remaining_amount - $event->grand_total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

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

            <!-- Action Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Review Action</h4>
                

                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                        <textarea name="comments" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="Add comments..."></textarea>
                    </div>
                    
                    <div class="space-y-3">
                        @if($event->grand_total <= $budget->remaining_amount)
                        <button type="submit" name="action" value="approve"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve & Forward to SA
                        </button>
                        @else
                        <button type="button" disabled
                            class="w-full bg-gray-300 text-gray-500 font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                            Insufficient Budget
                        </button>
                        @endif
                        
                        <button type="submit" name="action" value="reject"
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject Event
                        </button>
                    </div>

            </div>
        </div>
    </div>
    </form>
@endsection
