@extends('layouts.dashboard')

@section('title', 'Request Event - CAUSE Smart Society')
@section('page-title', 'Request New Event')
@section('page-description', 'Submit a new event proposal for approval')

@section('sidebar')
    <a href="{{ route('student.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('student.events.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        My Events
    </a>
    <a href="{{ route('student.events.create') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Request Event
    </a>
@endsection

@section('content')
    <form action="{{ route('student.events.store') }}" method="POST" id="eventForm">
        @csrf
        
        <!-- Event Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Event Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="Enter event title">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                              placeholder="Describe your event in detail">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue *</label>
                    <input type="text" name="venue" value="{{ old('venue') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="Event location">
                    @error('venue')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Guest Speaker & Faculty Mentor -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Speaker & Faculty Mentor</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Speaker Name</label>
                    <input type="text" name="guest_speaker_name" value="{{ old('guest_speaker_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="Enter guest speaker name">
                    @error('guest_speaker_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Speaker Designation</label>
                    <input type="text" name="guest_speaker_designation" value="{{ old('guest_speaker_designation') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="Enter guest speaker designation">
                    @error('guest_speaker_designation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                <p class="text-gray-500 text-sm mt-1">A notification message will be sent to the selected mentor</p>
                @error('faculty_mentor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Budget Items -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Budget Items *</h3>
                    <p class="text-gray-600 text-sm">Add all items required for your event</p>
                </div>
                <button type="button" onclick="addBudgetItem()" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
            </div>
            
            <div id="budgetItems">
                <!-- Budget items will be added here -->
            </div>
            
            <!-- Grand Total -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-end items-center">
                    <span class="text-lg font-semibold text-gray-800 mr-4">Grand Total:</span>
                    <span id="grandTotal" class="text-2xl font-bold text-cause-purple">PKR 0</span>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('student.events.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-cause-purple hover:bg-purple-700 text-white px-6 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Submit Event
            </button>
        </div>
    </form>

    <script>
        let itemCount = 0;
        
        function addBudgetItem() {
            itemCount++;
            const container = document.getElementById('budgetItems');
            
            const itemHtml = `
                <div class="budget-item bg-gray-50 rounded-lg p-4 mb-4" id="item-${itemCount}">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm font-medium text-gray-600">Item #${itemCount}</span>
                        <button type="button" onclick="removeBudgetItem(${itemCount})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                            <input type="text" name="items[${itemCount}][name]" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                                   placeholder="e.g., Refreshments">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" required
                                   onchange="calculateTotal()" oninput="calculateTotal()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple item-quantity">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Rate (PKR) *</label>
                            <input type="number" name="items[${itemCount}][unit_rate]" min="0" step="0.01" required
                                   onchange="calculateTotal()" oninput="calculateTotal()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple item-rate"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            calculateTotal();
        }
        
        function removeBudgetItem(id) {
            const item = document.getElementById(`item-${id}`);
            if (item) {
                item.remove();
                calculateTotal();
            }
        }
        
        function calculateTotal() {
            let total = 0;
            const items = document.querySelectorAll('.budget-item');
            
            items.forEach(item => {
                const quantity = parseFloat(item.querySelector('.item-quantity')?.value) || 0;
                const rate = parseFloat(item.querySelector('.item-rate')?.value) || 0;
                total += quantity * rate;
            });
            
            document.getElementById('grandTotal').textContent = 'PKR ' + total.toLocaleString('en-PK', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }
        
        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addBudgetItem();
        });
    </script>
@endsection
