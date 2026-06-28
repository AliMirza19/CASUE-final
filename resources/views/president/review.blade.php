@extends('layouts.dashboard')

@section('title', 'Review Event - CAUSE Smart Society')
@section('page-title', 'Review Event')
@section('page-description', 'Review event details and approve or request revisions')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('president.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Info Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $event->title }}</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Expected Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($event->expected_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Venue</p>
                        <p class="font-medium text-gray-800">{{ $event->venue ?: 'To be decided' }}</p>
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
                        @if($event->guest_speaker_profile_link)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Guest Speaker Profile</p>
                            <a href="{{ $event->guest_speaker_profile_link }}" target="_blank" class="text-cause-purple hover:underline flex items-center font-medium">
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
                            <p class="font-medium text-gray-800">{{ $event->facultyMentor->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div>
                        <p class="text-sm text-gray-500">Submitted</p>
                        <p class="font-medium text-gray-800">{{ $event->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending Review
                    </span>
                </div>
            </div>

                <form action="{{ route('president.approve', $event->id) }}" method="POST">
                    @csrf
                    
                    <!-- Premium Budget Assignment Section -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-cause-purple-dark flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V15m3.187-6.75l1.413-1.413m-10.788 0l1.413 1.413m10.788 10.788l1.413 1.413m-10.788 0l1.413-1.413"></path>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold text-white tracking-tight">Financial Estimates Assignment</h4>
                            </div>
                            <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full backdrop-blur-sm uppercase tracking-wider">Presidential Allocation</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full border-separate border-spacing-0">
                                <thead>
                                    <tr class="bg-gray-50/80">
                                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">#</th>
                                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Item Name</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Quantity</th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Unit Rate (PKR)</th>
                                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-right">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($event->items as $index => $item)
                                        <tr class="item-row group hover:bg-purple-50/50 transition-all duration-200" data-qty="{{ $item->quantity }}">
                                            <td class="px-8 py-6 text-gray-400 font-medium">{{ $index + 1 }}</td>
                                            <td class="px-8 py-6">
                                                <div class="font-bold text-gray-800 text-base">{{ $item->item_name }}</div>
                                            </td>
                                            <td class="px-6 py-6 text-center">
                                                <span class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 font-bold tabular-nums">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="px-6 py-6">
                                                <div class="flex justify-center">
                                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                                    <input type="number" name="items[{{ $item->id }}][unit_rate]" 
                                                        value="{{ $item->unit_rate }}" step="0.01" min="0" required
                                                        class="unit-rate w-32 text-center px-4 py-2 bg-gray-50 border-2 border-transparent rounded-xl focus:border-cause-purple focus:bg-white focus:ring-0 transition-all duration-200 font-bold text-gray-800 shadow-sm"
                                                        oninput="updateRowTotal(this)">
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div class="relative inline-block group/amount">
                                                    <input type="number" name="items[{{ $item->id }}][total_amount]" 
                                                        value="{{ $item->total_amount }}" step="0.01" min="0" readonly
                                                        class="total-amount w-40 text-right px-4 py-2 border-none bg-transparent focus:ring-0 outline-none transition-all tabular-nums text-xl font-black text-cause-purple">
                                                    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-cause-purple/20 transform scale-x-0 group-hover/amount:scale-x-100 transition-transform duration-300"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-[#f8fafc] border-t border-gray-100">
                                    <tr>
                                        <td colspan="4" class="px-8 py-8">
                                            <div class="flex items-center space-x-4">
                                                <div class="bg-cause-purple/10 p-3 rounded-2xl">
                                                    <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Allocated Budget</div>
                                                    <div class="text-sm font-medium text-gray-500">Sum of all item estimates</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-8 text-right">
                                            <div class="flex items-baseline justify-end space-x-2">
                                                <span class="text-lg font-bold text-gray-400">PKR</span>
                                                <span id="grandTotalDisplay" class="text-5xl font-black text-cause-purple tabular-nums tracking-tighter">
                                                    {{ number_format($event->grand_total, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
        
                <!-- Sidebar - Student Info & Actions -->
                <div class="space-y-6">
                    <!-- Student Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h4>
                        
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-cause-purple rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-lg">{{ substr($event->student->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $event->student->name }}</p>
                                <p class="text-sm text-gray-500">{{ $event->student->reg_id }}</p>
                                @if($event->student->current_semester)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                    {{ $event->student->current_semester }} Semester
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
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
                    </div>
        
                    <!-- Action Form -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Review Action</h4>

                        @if($event->status === 'pending_president')
                            <form action="{{ route('president.approve', $event->id) }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue (Decided by President) *</label>
                                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                        placeholder="e.g. Main Auditorium, Lab 1, etc.">
                                    @error('venue')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments (Optional)</label>
                                    <textarea name="comments" rows="4" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                                        placeholder="Add any comments or feedback..."></textarea>
                                </div>
                                
                                <div class="space-y-3">
                                    <button type="submit" name="action" value="approve"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve Event
                                    </button>
                                    
                                    <button type="submit" name="action" value="revision"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-4 rounded-lg transition flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Request Revision
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            This event has already been processed. Current status: <strong>{{ ucfirst(str_replace('_', ' ', $event->status)) }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($event->president_comments)
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Your Comments:</p>
                                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $event->president_comments }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
        </div>
    </div>
    <script>
        function updateRowTotal(input) {
            const row = input.closest('.item-row');
            const qty = parseFloat(row.dataset.qty) || 0;
            const rate = parseFloat(input.value) || 0;
            const total = qty * rate;
            
            row.querySelector('.total-amount').value = total.toFixed(2);
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total-amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grandTotalDisplay').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    </script>
@endsection
