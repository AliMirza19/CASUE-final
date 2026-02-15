@extends('layouts.dashboard')

@section('title', 'Assign Volunteers - CAUSE Smart Society')
@section('page-title', 'Assign Volunteers')
@section('page-description', 'Assign volunteers to this event')

@section('sidebar')
    <a href="{{ route('vc.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('vc.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Volunteer Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Assign Volunteers</h3>
                
                <form action="{{ route('vc.assign.save', $event->id) }}" method="POST" id="volunteerForm">
                    @csrf
                    
                    <div id="volunteersContainer">
                        @if($event->volunteers && $event->volunteers->count() > 0)
                            @foreach($event->volunteers as $index => $volunteer)
                                <div class="volunteer-row border border-gray-200 rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-medium text-gray-700">Volunteer {{ $index + 1 }}</span>
                                        <button type="button" onclick="removeVolunteer(this)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                            <input type="text" name="volunteers[{{ $index }}][name]" value="{{ $volunteer->volunteer_name }}" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                                            <input type="text" name="volunteers[{{ $index }}][contact]" value="{{ $volunteer->volunteer_contact }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                            <input type="text" name="volunteers[{{ $index }}][role]" value="{{ $volunteer->role_description }}" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="volunteer-row border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="font-medium text-gray-700">Volunteer 1</span>
                                    <button type="button" onclick="removeVolunteer(this)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                        <input type="text" name="volunteers[0][name]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                                        <input type="text" name="volunteers[0][contact]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                        <input type="text" name="volunteers[0][role]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" onclick="addVolunteer()" 
                        class="w-full border-2 border-dashed border-gray-300 rounded-lg py-3 text-gray-500 hover:border-cause-purple hover:text-cause-purple transition mb-4">
                        + Add Another Volunteer
                    </button>
                    
                    <button type="submit" class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-4 rounded-lg">
                        Save Volunteers
                    </button>
                </form>
            </div>
        </div>

        <!-- Event Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Event Details</h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Event Name</p>
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($event->expected_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Venue</p>
                        <p class="font-medium text-gray-800">{{ $event->venue }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let volunteerIndex = {{ $event->volunteers ? $event->volunteers->count() : 1 }};

function addVolunteer() {
    const container = document.getElementById('volunteersContainer');
    const html = `
        <div class="volunteer-row border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-3">
                <span class="font-medium text-gray-700">Volunteer ${volunteerIndex + 1}</span>
                <button type="button" onclick="removeVolunteer(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="volunteers[${volunteerIndex}][name]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                    <input type="text" name="volunteers[${volunteerIndex}][contact]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <input type="text" name="volunteers[${volunteerIndex}][role]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    volunteerIndex++;
}

function removeVolunteer(button) {
    const rows = document.querySelectorAll('.volunteer-row');
    if (rows.length > 1) {
        button.closest('.volunteer-row').remove();
    }
}
</script>
@endpush
