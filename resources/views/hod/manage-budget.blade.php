@extends('layouts.dashboard')

@section('title', 'Manage Budget - CAUSE Smart Society')
@section('page-title', 'Manage Budget')
@section('page-description', 'Set and manage term budget allocation')

@section('sidebar')
    <a href="{{ route('hod.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('hod.budget') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Manage Budget
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('hod.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Budget Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Set Term Budget</h3>
            
            @if($currentTerm)
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Current Term</p>
                <p class="font-medium text-gray-800">{{ $currentTerm->name }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ \Carbon\Carbon::parse($currentTerm->start_date)->format('M d, Y') }} - 
                    {{ \Carbon\Carbon::parse($currentTerm->end_date)->format('M d, Y') }}
                </p>
            </div>
            @endif

            @if($budget && $budget->is_locked)
                <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This budget is locked and cannot be modified.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('hod.budget.save') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Budget Amount (PKR)</label>
                    <input type="number" name="total_amount" step="0.01" min="0"
                        value="{{ $budget ? $budget->total_amount : '' }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple text-lg disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="Enter budget amount" required
                        {{ ($budget && $budget->is_locked) ? 'disabled' : '' }}>
                </div>
                
                @if(!($budget && $budget->is_locked))
                <div class="mb-4 text-sm text-gray-500 italic">
                    <span class="font-bold text-red-500">Note:</span> Once set, the budget will be locked and cannot be modified.
                </div>
                @endif

                <button type="submit" 
                    class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ ($budget && $budget->is_locked) ? 'disabled' : '' }}>
                    {{ $budget ? ($budget->is_locked ? 'Budget Locked' : 'Update Budget') : 'Set & Lock Budget' }}
                </button>
            </form>
        </div>

        <!-- Budget Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Budget Summary</h3>
            
            @if($budget)
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                    <span class="text-gray-600">Total Budget</span>
                    <span class="font-bold text-cause-purple text-xl">PKR {{ number_format($budget->total_amount, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                    <span class="text-gray-600">Spent</span>
                    <span class="font-bold text-red-600 text-xl">PKR {{ number_format($budget->total_amount - $budget->remaining_amount, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <span class="text-gray-600">Remaining</span>
                    <span class="font-bold text-green-600 text-xl">PKR {{ number_format($budget->remaining_amount, 2) }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Budget Utilization</span>
                        <span>{{ $budget->total_amount > 0 ? round((($budget->total_amount - $budget->remaining_amount) / $budget->total_amount) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-cause-purple h-3 rounded-full" 
                             style="width: {{ $budget->total_amount > 0 ? (($budget->total_amount - $budget->remaining_amount) / $budget->total_amount) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">No budget set for this term</p>
                <p class="text-gray-400 text-sm mt-1">Set a budget to enable event approvals</p>
            </div>
            @endif
        </div>
    </div>
@endsection
