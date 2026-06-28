@extends('layouts.dashboard')

@section('title', 'Manage HOD - CAUSE Smart Society')
@section('page-title', 'HOD Management')
@section('page-description', 'Assign and manage Head of Department for the current term')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Current Term Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-600 font-medium">Active Term</p>
                <p class="text-lg font-bold text-blue-800">{{ $activeTerm->term_name }}</p>
            </div>
        </div>
    </div>

    <!-- Current HOD Status -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Current HOD Assignment</h3>
        </div>
        <div class="p-6">
            @if($currentHodAssignment)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-2xl font-bold text-orange-600">{{ substr($currentHodAssignment->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-800">{{ $currentHodAssignment->user->name }}</p>
                            <p class="text-gray-600">{{ $currentHodAssignment->user->reg_id }}</p>
                            <p class="text-sm text-gray-500">{{ $currentHodAssignment->user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                        <p class="text-sm text-gray-500 mt-2">Assigned: {{ $currentHodAssignment->assigned_at->format('M d, Y') }}</p>
                    </div>
                </div>
            @else
                <!-- New Term Alert -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <p class="font-bold text-yellow-800">New Term Detected!</p>
                            <p class="text-yellow-700">No HOD has been assigned for this term yet. Please assign an HOD below.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Assignment Options -->
    @if(!$currentHodAssignment)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Continue with Previous HOD -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-semibold text-green-800">Continue with Previous HOD</h3>
            </div>
            <div class="p-6">
                @if($previousHodAssignment)
                    <div class="mb-4">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-lg font-bold text-gray-600">{{ substr($previousHodAssignment->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $previousHodAssignment->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $previousHodAssignment->user->reg_id }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Was HOD in: <span class="font-medium">{{ $previousHodAssignment->term->term_name }}</span>
                        </p>
                    </div>
                    <form action="{{ route('admin.continue-hod') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Continue with Previous HOD
                        </button>
                    </form>
                @else
                    <p class="text-gray-500 text-center py-4">No previous HOD found in the system.</p>
                @endif
            </div>
        </div>

        <!-- Appoint New HOD -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-semibold text-blue-800">Appoint New HOD</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.appoint-hod') }}" method="POST" id="appoint-hod-form">
                    @csrf
                    <input type="hidden" name="user_id" id="selected-user-id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search by Registration ID or Name</label>
                        <input type="text" id="user-search" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cause-purple focus:border-transparent"
                               placeholder="Enter Reg ID or Name...">
                    </div>
                    
                    <!-- Search Results -->
                    <div id="search-results" class="mb-4 hidden">
                        <div class="border rounded-lg max-h-48 overflow-y-auto"></div>
                    </div>
                    
                    <!-- Selected User -->
                    <div id="selected-user" class="mb-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-600 mb-1">Selected User:</p>
                            <p class="font-semibold text-blue-800" id="selected-user-name"></p>
                            <p class="text-sm text-blue-600" id="selected-user-reg"></p>
                        </div>
                    </div>
                    
                    <button type="submit" id="appoint-btn" disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Appoint as HOD
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Assignment History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">HOD Assignment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($hodHistory as $assignment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $assignment->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $assignment->user->reg_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $assignment->term->term_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $assignment->assigned_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No HOD assignment history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let searchTimeout;

document.getElementById('user-search')?.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    
    if (query.length < 2) {
        document.getElementById('search-results').classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('admin.search-user-hod') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                const resultsDiv = document.getElementById('search-results');
                const resultsContainer = resultsDiv.querySelector('div');
                
                if (users.length === 0) {
                    resultsContainer.innerHTML = '<p class="p-3 text-gray-500 text-center">No users found</p>';
                } else {
                    resultsContainer.innerHTML = users.map(user => `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0" onclick="selectUser(${user.id}, '${user.name}', '${user.reg_id}')">
                            <p class="font-medium text-gray-800">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.reg_id} - ${user.email}</p>
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">${user.role}</span>
                        </div>
                    `).join('');
                }
                
                resultsDiv.classList.remove('hidden');
            });
    }, 300);
});

function selectUser(id, name, regId) {
    document.getElementById('selected-user-id').value = id;
    document.getElementById('selected-user-name').textContent = name;
    document.getElementById('selected-user-reg').textContent = regId;
    document.getElementById('selected-user').classList.remove('hidden');
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('user-search').value = '';
    document.getElementById('appoint-btn').disabled = false;
}
</script>
@endpush
