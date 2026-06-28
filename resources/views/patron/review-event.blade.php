@extends('layouts.dashboard')

@section('title', 'Review Event - CAUSE Smart Society')
@section('page-title', 'Review Event')
@section('page-description', 'Review event details and budget items')

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('patron.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <form action="{{ route('patron.approve-event', $event->id) }}" method="POST" enctype="multipart/form-data">
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
            </div>

            <!-- Premium Budget Review Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-cause-purple-dark flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-white tracking-tight">Budget Estimates Review</h4>
                    </div>
                    <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full backdrop-blur-sm uppercase tracking-wider">Patron Verification</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-0" id="budget-table">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Item Name</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Qty</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Unit Rate (PKR)</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Total Amount</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 text-center">Action</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">Review Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($event->items as $index => $item)
                                <tr class="item-row group hover:bg-purple-50/50 transition-all duration-200" data-index="{{ $index }}">
                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-gray-800 text-base">{{ $item->item_name }}</div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" step="1" min="1"
                                            class="qty-input w-20 text-center px-3 py-2 bg-gray-50 border-2 border-transparent rounded-xl focus:border-cause-purple focus:bg-white focus:ring-0 transition-all duration-200 font-bold text-gray-700 shadow-sm">
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <input type="number" name="items[{{ $item->id }}][unit_rate]" value="{{ $item->unit_rate }}" step="0.01" min="0"
                                            class="rate-input w-28 text-center px-3 py-2 bg-gray-50 border-2 border-transparent rounded-xl focus:border-cause-purple focus:bg-white focus:ring-0 transition-all duration-200 font-bold text-gray-700 shadow-sm">
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="relative group/amount">
                                            <input type="number" name="items[{{ $item->id }}][total_amount]" value="{{ (int)$item->total_amount }}" step="0.01" min="0" readonly
                                                class="amount-input w-32 text-center px-3 py-2 border-none bg-transparent focus:ring-0 outline-none transition-all tabular-nums text-lg font-black text-cause-purple">
                                            <div class="absolute inset-x-0 bottom-0 h-0.5 bg-cause-purple/20 transform scale-x-0 group-hover/amount:scale-x-100 transition-transform duration-300"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <label class="relative flex items-center cursor-pointer group/radio">
                                                <input type="radio" name="items[{{ $item->id }}][is_approved]" value="1" 
                                                    {{ $item->is_approved_by_patron ? 'checked' : '' }}
                                                    class="approve-radio sr-only peer">
                                                <div class="px-3 py-1.5 rounded-full border-2 border-gray-200 text-gray-400 text-[10px] font-black uppercase tracking-wider peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:text-green-600 hover:border-green-300 transition-all duration-200">
                                                    Appr
                                                </div>
                                            </label>
                                            <label class="relative flex items-center cursor-pointer group/radio">
                                                <input type="radio" name="items[{{ $item->id }}][is_approved]" value="0" 
                                                    {{ !$item->is_approved_by_patron ? 'checked' : '' }}
                                                    class="reject-radio sr-only peer">
                                                <div class="px-3 py-1.5 rounded-full border-2 border-gray-200 text-gray-400 text-[10px] font-black uppercase tracking-wider peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-600 hover:border-red-300 transition-all duration-200">
                                                    Rej
                                                </div>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <textarea name="items[{{ $item->id }}][comment]" rows="1"
                                            class="w-full text-sm px-4 py-2 bg-gray-50 border-2 border-transparent rounded-xl focus:border-cause-purple focus:bg-white focus:ring-0 transition-all duration-200 outline-none resize-none" 
                                            placeholder="Add note...">{{ $item->patron_comment }}</textarea>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V15m3.187-6.75l1.413-1.413m-10.788 0l1.413 1.413m10.788 10.788l1.413 1.413m-10.788 0l1.413-1.413"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-gray-400 uppercase tracking-widest">Final Estimated Budget</div>
                                            <div class="text-sm font-medium text-gray-500">Authorized expenditure for this event</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="px-8 py-8 text-right">
                                    <div class="flex items-baseline justify-end space-x-2">
                                        <span class="text-lg font-bold text-gray-400">PKR</span>
                                        <span id="grand-total" class="text-5xl font-black text-cause-purple tabular-nums tracking-tighter">
                                            {{ (int)$event->grand_total }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const table = document.getElementById('budget-table');
                    const qtyInputs = table.querySelectorAll('.qty-input');
                    const rateInputs = table.querySelectorAll('.rate-input');
                    const amountInputs = table.querySelectorAll('.amount-input');
                    const actionRadios = table.querySelectorAll('.approve-radio, .reject-radio');
                    const grandTotalSpan = document.getElementById('grand-total');

                    function calculateTotals() {
                        let grandTotal = 0;
                        const rows = table.querySelectorAll('.item-row');
                        
                        rows.forEach(row => {
                            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                            const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
                            const amount = qty * rate;
                            
                            // Update the amount input for this row
                            row.querySelector('.amount-input').value = amount.toFixed(0);
                            
                            const approved = row.querySelector('.approve-radio').checked;
                            
                            if (approved) {
                                grandTotal += amount;
                                row.querySelector('.amount-input').classList.remove('text-red-500', 'line-through');
                                row.querySelector('.amount-input').classList.add('text-cause-purple');
                            } else {
                                row.querySelector('.amount-input').classList.add('text-red-500', 'line-through');
                                row.querySelector('.amount-input').classList.remove('text-cause-purple');
                            }
                        });
                        
                        grandTotalSpan.textContent = Math.round(grandTotal).toLocaleString();
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
                        <p class="text-sm text-gray-500">{{ $event->student->current_semester }} Semester</p>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-2 text-sm border-t border-gray-100 pt-3">
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

                    
                    <!-- Signature -->
                    <div class="mb-4">
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Digital Signature</label>
                        <div class="relative group">
                            <div class="w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center bg-gray-50 group-hover:border-cause-purple transition-colors cursor-pointer overflow-hidden">
                                <input type="file" name="digital_signature" id="signature-input" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" {{ Auth::user()->digital_signature ? '' : 'required' }}>
                                @if(Auth::user()->digital_signature)
                                    <img src="{{ asset('storage/' . Auth::user()->digital_signature) }}" class="max-h-full object-contain p-4" id="sig-prev">
                                @else
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-xs text-gray-400 font-bold">UPLOAD SIGNATURE</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Recommended: Transparent PNG</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                        <textarea name="comments" rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="Add comments..."></textarea>
                    </div>
                    
                    <div class="space-y-3">
                        <button type="submit" name="action" value="approve"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve & Forward to HOD
                        </button>
                        
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
    <script>
        document.getElementById('signature-input').onchange = evt => {
            const [file] = evt.target.files;
            if (file) {
                const prev = document.getElementById('sig-prev');
                if (prev) {
                    prev.src = URL.createObjectURL(file);
                } else {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'max-h-full object-contain p-4';
                    img.id = 'sig-prev';
                    
                    const container = evt.target.parentElement;
                    const svg = container.querySelector('svg');
                    const span = container.querySelector('span');
                    if(svg) svg.style.display = 'none';
                    if(span) span.style.display = 'none';
                    
                    container.appendChild(img);
                }
            }
        }
    </script>
@endsection
