@extends('layouts.dashboard')

@section('title', 'Video Overview - CAUSE Smart Society')
@section('page-title', 'Video Overview')
@section('page-description', 'Summary of your video uploads and highlight reels')

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
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Full Recordings</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalVideos }}</p></div>
            <div class="bg-red-100 rounded-full p-3"><svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Highlights / Reels</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalHighlights }}</p></div>
            <div class="bg-yellow-100 rounded-full p-3"><svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-red-50 to-orange-50">
            <h3 class="text-lg font-semibold text-gray-800">🎬 Upload Video</h3>
            <p class="text-sm text-gray-500 mt-1">MP4, MOV, AVI — max 500MB</p>
        </div>
        <div class="p-6">
            @if($approvedEvents->count() > 0)
            <form id="video-upload-form" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video Type *</label>
                        <select name="media_type" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="video">📹 Full Recording</option>
                            <option value="highlight">⭐ Highlight / Reel</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tagged Reg #</label>
                        <input type="text" name="tagged_reg_number" placeholder="Optional" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                        <input type="text" name="caption" placeholder="Optional caption" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video File *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-red-400 transition-colors cursor-pointer" onclick="document.getElementById('video_file').click()">
                        <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500 text-sm">Click to select video</p>
                        <p id="video_name_label" class="text-red-500 font-semibold text-sm mt-2 hidden"></p>
                    </div>
                    <input type="file" id="video_file" name="video" accept="video/*" class="hidden" onchange="document.getElementById('video_name_label').textContent='✓ '+this.files[0].name; document.getElementById('video_name_label').classList.remove('hidden')">
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">🎬 Upload Video</button>
            </form>
            @else
                <p class="text-gray-500 text-center py-6">No approved events available yet.</p>
            @endif
        </div>
    </div>

    @if($myVideos->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold text-gray-800">📂 My Uploaded Videos</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caption</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myVideos as $video)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $video->event->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($video->media_type === 'highlight')
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">⭐ Highlight</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">📹 Full</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $video->caption ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $video->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ Storage::url($video->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">▶ Play</a>
                            <form method="POST" action="{{ route('video.destroy', $video->id) }}" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    const videoForm = document.getElementById('video-upload-form');
    if (videoForm) {
        videoForm.addEventListener('submit', function(e) {
            const eventId = document.getElementById('event_select').value;
            if (!eventId) {
                e.preventDefault();
                alert('Please select an event first.');
                return;
            }
            this.action = '/video/events/' + eventId + '/upload';
        });
    }
});
</script>
@endpush
