@extends('layouts.dashboard')

@section('title', 'Photography Overview - CAUSE Smart Society')
@section('page-title', 'Photography Overview')
@section('page-description', 'Summary of your photo uploads and event coverage')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    @include('partials.tasks-widget')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Approved Events</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalEvents }}</p></div>
            <div class="bg-blue-100 rounded-full p-3"><svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Total Photos</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPhotos }}</p></div>
            <div class="bg-indigo-100 rounded-full p-3"><svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">This Month</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $thisMonth }}</p></div>
            <div class="bg-green-100 rounded-full p-3"><svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800">📸 Upload Photos</h3>
            <p class="text-sm text-gray-500 mt-1">JPG, PNG, WEBP — max 10MB per file</p>
        </div>
        <div class="p-6">
            @if($approvedEvents->count() > 0)
            <form id="photo-upload-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Event *</label>
                        <select id="event_select" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">-- Choose Event --</option>
                            @foreach($approvedEvents as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                        <input type="text" name="caption" placeholder="Optional caption" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagged Reg #</label>
                        <input type="text" name="tagged_reg_number" placeholder="Optional" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagged Role</label>
                        <input type="text" name="tagged_role" placeholder="Optional" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photos *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" onclick="document.getElementById('photo_files').click()">
                        <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500 text-sm">Click to select photos</p>
                        <p id="photo_count_label" class="text-blue-600 font-semibold text-sm mt-2 hidden"></p>
                    </div>
                    <input type="file" id="photo_files" name="photos[]" multiple accept="image/*" class="hidden" onchange="document.getElementById('photo_count_label').textContent='✓ '+this.files.length+' photo(s) selected'; document.getElementById('photo_count_label').classList.remove('hidden')">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">📸 Upload Photos</button>
            </form>
            @else
                <p class="text-gray-500 text-center py-6">No approved events available yet.</p>
            @endif
        </div>
    </div>

    @if($myPhotos->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold text-gray-800">📂 My Uploaded Photos</h3></div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
            @foreach($myPhotos as $photo)
            <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border">
                <img src="{{ Storage::url($photo->file_path) }}" alt="Photo" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center space-y-2 p-2">
                    <p class="text-white text-[10px] text-center line-clamp-2">{{ $photo->event->title ?? 'N/A' }}</p>
                    <div class="flex space-x-2">
                        <a href="{{ Storage::url($photo->file_path) }}" target="_blank" class="p-1 bg-white rounded-full text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('photo.destroy', $photo->id) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 bg-white rounded-full text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-8">
        @include('partials.team-chat')
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoForm = document.getElementById('photo-upload-form');
    if (photoForm) {
        photoForm.addEventListener('submit', function(e) {
            const eventId = document.getElementById('event_select').value;
            if (!eventId) {
                e.preventDefault();
                alert('Please select an event first.');
                return;
            }
            this.action = '/photo/events/' + eventId + '/upload';
        });
    }
});
</script>
@endpush
