@extends('layouts.dashboard')

@section('title', 'Documentation Overview - CAUSE Smart Society')
@section('page-title', 'Documentation Overview')
@section('page-description', 'Summary of your reports and attendance documents')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    @include('partials.tasks-widget')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Total Documents</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalDocs }}</p></div>
            <div class="bg-blue-100 rounded-full p-3"><svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-cause-purple flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Financial Reports</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $financialCount }}</p></div>
            <div class="bg-purple-100 rounded-full p-3"><svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">General Docs</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $generalCount }}</p></div>
            <div class="bg-green-100 rounded-full p-3"><svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800">📤 Upload Document</h3>
            <p class="text-sm text-gray-500 mt-1">PDF, Word, Excel, PPT — max 20MB</p>
        </div>
        <div class="p-6">
            @if($approvedEvents->count() > 0)
            <form id="doc-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event *</label>
                        <select id="event_select" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">-- Choose Event --</option>
                            @foreach($approvedEvents as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                        <select name="doc_type" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="general_documentation">📄 General Documentation</option>
                            <option value="financial_report">💰 Financial Report</option>
                            <option value="approval_form">✍️ Approval Form</option>
                            <option value="poster_graphic">🎨 Poster/Graphic</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description of this document..." class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" onclick="document.getElementById('doc_file').click()">
                        <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-gray-500 text-sm">Click to select file</p>
                        <p class="text-gray-400 text-xs mt-1">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</p>
                        <p id="doc_name_label" class="text-blue-600 font-semibold text-sm mt-2 hidden"></p>
                    </div>
                    <input type="file" id="doc_file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" class="hidden" onchange="document.getElementById('doc_name_label').textContent='✓ '+this.files[0].name; document.getElementById('doc_name_label').classList.remove('hidden')">
                </div>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-6 rounded-lg transition-colors">📤 Upload Document</button>
            </form>
            @else
                <p class="text-gray-500 text-center py-6">No approved events available yet.</p>
            @endif
        </div>
    </div>

    @if($myDocs->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold text-gray-800">📂 My Uploaded Documents</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myDocs as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $doc->event->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ ucfirst($doc->doc_type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $doc->original_filename }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $doc->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('doc.download', $doc->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">⬇ Download</a>
                            <form method="POST" action="{{ route('doc.destroy', $doc->id) }}" class="inline" onsubmit="return confirm('Delete?')">
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
    const docForm = document.getElementById('doc-form');
    if (docForm) {
        docForm.addEventListener('submit', function(e) {
            const eventId = document.getElementById('event_select').value;
            if (!eventId) {
                e.preventDefault();
                alert('Please select an event.');
                return;
            }
            this.action = '/doc/events/' + eventId + '/upload';
        });
    }
});
</script>
@endpush
