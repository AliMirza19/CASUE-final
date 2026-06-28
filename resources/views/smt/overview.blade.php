@extends('layouts.dashboard')

@section('title', 'SMT Overview - CAUSE Smart Society')
@section('page-title', 'SMT Overview')
@section('page-description', 'Summary of your social media activities and scheduled posts')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    @include('partials.tasks-widget')



    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-pink-50 to-purple-50">
            <h3 class="text-lg font-semibold text-gray-800">➕ Add Social Media Link</h3>
        </div>
        <div class="p-6">
            @if($approvedEvents->count() > 0)
            <form id="link-form" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event *</label>
                        <select id="event_select" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">-- Choose Event --</option>
                            @foreach($approvedEvents as $event)
                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform *</label>
                        <select name="platform" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="instagram">📸 Instagram</option>
                            <option value="linkedin">💼 LinkedIn</option>
                            <option value="facebook">📘 Facebook</option>
                            <option value="twitter">🐦 Twitter / X</option>
                            <option value="youtube">🎥 YouTube</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="published">✅ Published</option>
                            <option value="scheduled">📅 Scheduled</option>
                            <option value="draft">📝 Draft</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Post URL *</label>
                    <input type="url" name="post_url" placeholder="https://..." class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="2" placeholder="Engagement stats, reach, etc." class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                </div>
                <button type="button" onclick="submitLinkForm()" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">📎 Add Link</button>
            </form>
            @else
                <p class="text-gray-500 text-center py-6">No approved events available yet.</p>
            @endif
        </div>
    </div>

    @if($myLinks->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold text-gray-800">🔗 My Social Media Links</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myLinks as $link)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $link->event->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4 capitalize">
                            {{ $link->platform_icon }} {{ ucfirst($link->platform) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($link->status === 'published')
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✅ Published</span>
                            @elseif($link->status === 'scheduled')
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">📅 Scheduled</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">📝 Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $link->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ $link->post_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">🔗 View</a>
                            <form method="POST" action="{{ route('smt.destroy', $link->id) }}" class="inline" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
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
function submitLinkForm() {
    const eventId = document.getElementById('event_select').value;
    if (!eventId) { alert('Please select an event.'); return; }
    document.getElementById('link-form').action = '/smt/events/' + eventId + '/link';
    document.getElementById('link-form').submit();
}
</script>
@endpush
