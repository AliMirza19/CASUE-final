@extends('layouts.dashboard')

@section('title', 'Request Event - CAUSE Smart Society')
@section('page-title', 'Request New Event')
@section('page-description', 'Submit a new event proposal for approval')

@section('sidebar')
    @include('partials.student-sidebar')
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
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Speaker Profile Link (LinkedIn/Website)</label>
                    <input type="url" name="guest_speaker_profile_link" value="{{ old('guest_speaker_profile_link') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="https://linkedin.com/in/username">
                    @error('guest_speaker_profile_link')
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
                    <h3 class="text-lg font-semibold text-gray-800">Requirements *</h3>
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
            
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Item Name</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 w-32">Quantity</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700 w-20">Action</th>
                    </tr>
                </thead>
                <tbody id="budgetItems">
                    <!-- Budget items will be added here -->
                </tbody>
            </table>
        </div>
        
        <!-- Submit Buttons -->
        <div class="bg-white rounded-lg shadow-md p-6 flex justify-between items-center">
            <span class="text-gray-600 text-sm italic">Estimated budget will be reviewed by administration.</span>
            <button type="submit" 
                    class="bg-cause-purple hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-purple-200">
                Submit Event Proposal
            </button>
        </div>
    </form>

    <script>
        let itemCount = 0;
        
        function addBudgetItem() {
            itemCount++;
            const container = document.getElementById('budgetItems');
            
            const row = document.createElement('tr');
            row.id = `item-${itemCount}`;
            row.className = 'budget-item border-b';
            row.innerHTML = `
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][name]" required
                           class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-cause-purple focus:border-cause-purple"
                           placeholder="e.g. Refreshments">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemCount}][quantity]" required min="1"
                           class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-cause-purple focus:border-cause-purple"
                           value="1">
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            `;
            
            container.appendChild(row);
        }
        
        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addBudgetItem();
        });

        // Prevent double submission
        document.getElementById('eventForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
        });
    </script>
@endsection
