@extends('layouts.dashboard')

@section('title', 'Manage Budget - CAUSE Smart Society')
@section('page-title', 'Manage Budget')
@section('page-description', 'Set and manage term budget allocation')

@section('sidebar')
    @include('partials.hod-sidebar')
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
        <!-- Budget Info (Read Only) -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Term Budget Status</h3>
            
            @if($currentTerm)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Academic Term</p>
                <p class="font-medium text-gray-800">{{ $currentTerm->name }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ \Carbon\Carbon::parse($currentTerm->start_date)->format('M d, Y') }} - 
                    {{ \Carbon\Carbon::parse($currentTerm->end_date)->format('M d, Y') }}
                </p>
            </div>
            @endif

            <div class="mb-8 p-6 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h4 class="text-gray-800 font-bold mb-1">Budget Assigned by Admin</h4>
                <p class="text-gray-500 text-sm">Financial limits for this term are set by the Administrator. You can approve events within this allocation.</p>
            </div>

            @if($budget)
                <div class="p-4 bg-purple-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-cause-purple uppercase tracking-wider">Total Allocation</p>
                        <p class="text-2xl font-black text-gray-800">PKR {{ number_format($budget->total_amount, 2) }}</p>
                    </div>
                    <div class="bg-white p-2 rounded-lg shadow-sm">
                        <svg class="w-6 h-6 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            @endif
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
