@extends('layouts.dashboard')

@section('title', 'Create Event - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <a href="{{ route('patron.my-events.index') }}" class="text-gray-600 hover:text-gray-900 mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Create New Event</h1>
            </div>
            <p class="text-gray-600 ml-9">Submit a new event proposal (will be sent to HOD for final approval)</p>
        </div>

        <form action="{{ route('patron.my-events.store') }}" method="POST" id="eventForm">
            @csrf
            
            <!-- Event Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Event Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                               placeholder="Enter event title">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                  placeholder="Describe your event in detail">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Date *</label>
                            <input type="date" name="expected_date" value="{{ old('expected_date') }}" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
                            @error('expected_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue (Optional)</label>
                            <input type="text" name="venue" value="{{ old('venue') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                   placeholder="Assign venue directly (e.g. Main Auditorium)">
                            @error('venue')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Guest Speaker & Faculty Mentor -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Guest Speaker & Faculty Mentor
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guest Speaker Name</label>
                            <input type="text" name="guest_speaker_name" value="{{ old('guest_speaker_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                   placeholder="Enter guest speaker name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guest Speaker Designation</label>
                            <input type="text" name="guest_speaker_designation" value="{{ old('guest_speaker_designation') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                   placeholder="Enter designation">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Faculty Mentor</label>
                        <select name="faculty_mentor_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple">
                            <option value="">-- Select a Faculty Mentor --</option>
                            @foreach($facultyMembers as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty_mentor_id') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }} ({{ $faculty->reg_id }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-500 text-sm mt-1">A notification will be sent to the selected mentor</p>
                    </div>
                </div>
            </div>
            
            <!-- Budget Items -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Budget Items
                </h3>
                
                <div id="items-container" class="space-y-3">
                    <!-- Item template will be added here -->
                </div>
                
                <button type="button" onclick="addItem()" 
                        class="mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Grand Total:</span>
                        <span class="text-cause-purple">Rs. <span id="grand-total">0.00</span></span>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('patron.my-events.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="bg-cause-purple hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Submit Event</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 0;

function addItem() {
    itemCount++;
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200" data-item="${itemCount}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                    <input type="text" name="items[${itemCount}][name]" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple"
                           placeholder="e.g., Refreshments">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="items[${itemCount}][quantity]" required min="1" value="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple"
                           onchange="calculateItemTotal(${itemCount})">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Rate *</label>
                    <input type="number" name="items[${itemCount}][unit_rate]" required min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple"
                           onchange="calculateItemTotal(${itemCount})">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                    <input type="number" name="items[${itemCount}][total_amount]" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                           id="item-total-${itemCount}">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" onclick="removeItem(${itemCount})"
                            class="w-full bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    calculateGrandTotal();
}

function removeItem(itemId) {
    const item = document.querySelector(`[data-item="${itemId}"]`);
    if (item) {
        item.remove();
        calculateGrandTotal();
    }
}

function calculateItemTotal(itemId) {
    const row = document.querySelector(`[data-item="${itemId}"]`);
    const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const unitRate = parseFloat(row.querySelector('input[name*="[unit_rate]"]').value) || 0;
    const total = quantity * unitRate;
    
    row.querySelector(`#item-total-${itemId}`).value = total.toFixed(2);
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('[id^="item-total-"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('grand-total').textContent = total.toFixed(2);
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
    
    // Prevent double submission
    const form = document.getElementById('eventForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if(form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            const span = submitBtn.querySelector('span');
            if(span) span.textContent = 'Submitting...';
        });
    }
});
</script>
@endsection
