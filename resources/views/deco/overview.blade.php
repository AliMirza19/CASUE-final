@extends('layouts.dashboard')

@section('title', 'Decoration Overview - CAUSE Smart Society')
@section('page-title', 'Decoration Overview')
@section('page-description', 'Summary of your decoration plans and budgets')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    @include('partials.tasks-widget')

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-pink-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Total Plans</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPlans }}</p></div>
            <div class="bg-pink-100 rounded-full p-3">🎀</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Completed</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $donePlans }}</p></div>
            <div class="bg-green-100 rounded-full p-3"><svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">In Progress</p><p class="text-3xl font-bold text-gray-800 mt-1">{{ $inProgress }}</p></div>
            <div class="bg-yellow-100 rounded-full p-3"><svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-cause-purple flex items-center justify-between">
            <div><p class="text-gray-500 text-sm font-medium">Total Budget Est.</p><p class="text-3xl font-bold text-gray-800 mt-1">Rs. {{ number_format($totalBudget) }}</p></div>
            <div class="bg-purple-100 rounded-full p-3"><svg class="w-7 h-7 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        </div>
    </div>

    {{-- Create Plan Form --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-pink-50 to-rose-50">
            <h3 class="text-lg font-semibold text-gray-800">🎨 Create Decoration Plan</h3>
        </div>
        <div class="p-6">
            @if($approvedEvents->count() > 0)
            <form id="deco-form" method="POST" enctype="multipart/form-data" class="space-y-5">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="not_started">⏳ Not Started</option>
                            <option value="in_progress">🔨 In Progress</option>
                            <option value="done">✅ Done</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Decoration Plan Description</label>
                    <textarea name="plan_description" rows="3" placeholder="Describe the decoration theme, layout, colours..." class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Budget (Rs.)</label>
                    <input type="number" name="estimated_budget" placeholder="0" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                {{-- Material List --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Material List</label>
                    <div id="material-list" class="space-y-2">
                        <div class="flex gap-2 items-center material-row">
                            <input type="text" name="materials[0][item]" placeholder="Item name" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <input type="number" name="materials[0][qty]" placeholder="Qty" min="1" class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <input type="number" name="materials[0][cost]" placeholder="Cost" min="0" class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>
                    <button type="button" onclick="addMaterialRow()" class="mt-2 text-cause-purple hover:text-cause-purple-dark text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setup Photos (optional)</label>
                    <input type="file" name="setup_photos[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Any additional notes..." class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                </div>

                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">🎀 Create Plan</button>
            </form>
            @else
                <p class="text-gray-500 text-center py-6">No approved events available yet.</p>
            @endif
        </div>
    </div>

    {{-- My Plans --}}
    @if($myPlans->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-lg font-semibold text-gray-800">📋 My Decoration Plans</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget Est.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materials</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Update Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myPlans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $plan->event->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($plan->status === 'done')
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">✅ Done</span>
                            @elseif($plan->status === 'in_progress')
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">🔨 In Progress</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">⏳ Not Started</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">Rs. {{ number_format($plan->estimated_budget) }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ count($plan->material_list ?? []) }} item(s)</td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('deco.update-status', $plan->id) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-cause-purple">
                                    <option value="not_started" @selected($plan->status === 'not_started')>⏳ Not Started</option>
                                    <option value="in_progress" @selected($plan->status === 'in_progress')>🔨 In Progress</option>
                                    <option value="done" @selected($plan->status === 'done')>✅ Done</option>
                                </select>
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
let materialIndex = 1;
function addMaterialRow() {
    const container = document.getElementById('material-list');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center material-row';
    row.innerHTML = `
        <input type="text" name="materials[${materialIndex}][item]" placeholder="Item name" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <input type="number" name="materials[${materialIndex}][qty]" placeholder="Qty" min="1" class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <input type="number" name="materials[${materialIndex}][cost]" placeholder="Cost" min="0" class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg leading-none">×</button>
    `;
    container.appendChild(row);
    materialIndex++;
}
document.addEventListener('DOMContentLoaded', function() {
    const decoForm = document.getElementById('deco-form');
    if (decoForm) {
        decoForm.addEventListener('submit', function(e) {
            const eventId = document.getElementById('event_select').value;
            if (!eventId) {
                e.preventDefault();
                alert('Please select an event.');
                return;
            }
            this.action = '/deco/events/' + eventId + '/plan';
        });
    }
});
</script>
@endpush
