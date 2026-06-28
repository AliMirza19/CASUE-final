@extends('layouts.dashboard')

@section('title', 'Manage Leads - CAUSE Smart Society')
@section('page-title', 'Appoint Leads')
@section('page-description', 'Assign or continue leads for the current term')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('president.dashboard') }}" class="text-cause-purple hover:text-cause-purple-dark flex items-center">
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
                <p class="text-sm text-blue-600 font-medium">Current Term</p>
                <p class="text-lg font-bold text-blue-800">{{ $currentTerm->term_name }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($assignments as $roleKey => $roleData)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <span class="w-8 h-8 rounded bg-indigo-100 text-indigo-700 flex items-center justify-center mr-2 text-xs uppercase">{{ $roleKey }}</span>
                    {{ $roleData['name'] }}
                </h3>
                @if($roleData['current'])
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Pending Assignment</span>
                @endif
            </div>
            
            <div class="p-6">
                <!-- Current Assigned Info -->
                @if($roleData['current'])
                    <div class="mb-4 bg-green-50 p-4 rounded-lg border border-green-100">
                        <p class="text-sm text-green-600 font-semibold mb-2">Currently Assigned:</p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-3 shadow-sm border border-green-200">
                                <span class="text-lg font-bold text-green-600">{{ substr($roleData['current']->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $roleData['current']->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $roleData['current']->user->reg_id }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 italic">No one is currently assigned to this role for the active term.</p>
                    </div>
                @endif

                <!-- Assignment Actions -->
                @if(!$roleData['current'])
                <div class="mt-4 border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Appoint Lead</h4>
                    
                    @if($roleData['previous'])
                    <!-- Option 1: Continue Previous -->
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4">
                        <p class="text-xs text-gray-500 mb-2">Previous Term's Lead:</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-bold text-gray-600">{{ substr($roleData['previous']->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $roleData['previous']->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $roleData['previous']->user->reg_id }}</p>
                                </div>
                            </div>
                            <form action="{{ route('president.continue-team-lead') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $roleKey }}">
                                <button type="submit" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-1 px-3 rounded shadow-sm transition">
                                    Continue
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Option 2: Search and Assign New -->
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Search & Appoint New Student</label>
                        <input type="text" id="search-{{ $roleKey }}" onkeyup="searchStudent(this.value, '{{ $roleKey }}')"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-cause-purple focus:border-transparent"
                               placeholder="Enter name or Reg ID...">
                               
                        <!-- Search Results Dropdown -->
                        <div id="results-{{ $roleKey }}" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg hidden max-h-48 overflow-y-auto">
                        </div>
                    </div>
                    
                    <!-- Selected user form (hidden by default) -->
                    <form action="{{ route('president.appoint-team-lead') }}" method="POST" id="form-{{ $roleKey }}" class="mt-3 hidden">
                        @csrf
                        <input type="hidden" name="role" value="{{ $roleKey }}">
                        <input type="hidden" name="user_id" id="user_id-{{ $roleKey }}">
                        
                        <div class="bg-indigo-50 border border-indigo-200 rounded-md p-2 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-indigo-800 font-semibold">Selected:</p>
                                <p class="text-sm text-indigo-900 font-bold" id="selected-name-{{ $roleKey }}"></p>
                            </div>
                            <button type="submit" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-2 rounded">
                                Appoint Now
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
let searchTimeouts = {};

function searchStudent(query, roleKey) {
    clearTimeout(searchTimeouts[roleKey]);
    const resultsDiv = document.getElementById(`results-${roleKey}`);
    
    if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    searchTimeouts[roleKey] = setTimeout(() => {
        fetch(`{{ route('president.search-student') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                if (users.length === 0) {
                    resultsDiv.innerHTML = '<p class="p-2 text-xs text-gray-500 text-center">No regular students found</p>';
                } else {
                    resultsDiv.innerHTML = users.map(user => `
                        <div class="p-2 hover:bg-gray-50 cursor-pointer border-b last:border-b-0" onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${roleKey}')">
                            <p class="font-medium text-sm text-gray-800">${user.name}</p>
                            <p class="text-xs text-gray-500">${user.reg_id}</p>
                        </div>
                    `).join('');
                }
                resultsDiv.classList.remove('hidden');
            });
    }, 300);
}

function selectUser(id, name, roleKey) {
    document.getElementById(`user_id-${roleKey}`).value = id;
    document.getElementById(`selected-name-${roleKey}`).textContent = name;
    
    document.getElementById(`form-${roleKey}`).classList.remove('hidden');
    document.getElementById(`results-${roleKey}`).classList.add('hidden');
    document.getElementById(`search-${roleKey}`).value = '';
}
</script>
@endpush
