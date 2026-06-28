@extends('layouts.dashboard')

@section('title', 'Manage Budget - CAUSE Smart Society')
@section('page-title', 'Budget Management')
@section('page-description', 'Set and lock term budgets for the society')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-medium">Back to Dashboard</span>
        </a>
    </div>

    <!-- Term Selector -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="{{ route('admin.budget') }}" method="GET" class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-shrink-0">
                <label class="block text-sm font-semibold text-gray-700 mb-1 md:mb-0">Select Academic Term:</label>
            </div>
            <div class="flex-grow max-w-md">
                <select name="term_id" onchange="this.form.submit()" 
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cause-purple/20 focus:border-cause-purple transition-all duration-200 outline-none">
                    @foreach($allTerms as $term)
                        <option value="{{ $term->id }}" {{ $selectedTermId == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} {{ $term->status === 'active' ? '(Active)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedTerm && $selectedTerm->status === 'active')
                <div class="flex items-center px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full uppercase tracking-wider">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Active Term
                </div>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Budget Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-xl font-bold text-gray-800">Assign Term Budget</h3>
                    <p class="text-sm text-gray-500 mt-1">Allocate funds for {{ $selectedTerm ? $selectedTerm->name : 'selected term' }}</p>
                </div>
                
                <div class="p-8">
                    @if($selectedTerm)
                        @if($budget && $budget->is_locked)
                            <div class="mb-8 p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-start">
                                <div class="bg-amber-100 p-2 rounded-lg mr-4">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-amber-800 font-bold">Budget is Immutable (Locked)</p>
                                    <p class="text-amber-700 text-sm mt-1">This budget has been finalized and locked for this term. It cannot be updated or modified to ensure financial consistency throughout the term.</p>
                                </div>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.budget.save') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="term_id" value="{{ $selectedTerm->id }}">
                            
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-cause-purple transition-colors">Total Allocation (PKR)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-medium">PKR</span>
                                    </div>
                                    <input type="number" name="total_amount" step="0.01" min="0"
                                        value="{{ $budget ? $budget->total_amount : '' }}"
                                        class="w-full pl-14 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-cause-purple/10 focus:border-cause-purple focus:bg-white transition-all duration-200 text-2xl font-bold text-gray-800 {{ $budget && $budget->is_locked ? 'opacity-60 cursor-not-allowed' : '' }}"
                                        placeholder="0.00" required {{ $budget && $budget->is_locked ? 'disabled' : '' }}>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-cause-purple" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    This budget will be visible to the HOD for event approvals.
                                </p>
                            </div>
                            
                            @if(!$budget || !$budget->is_locked)
                                <div class="pt-4">
                                    <button type="submit" 
                                        class="w-full bg-gradient-to-r from-cause-purple to-cause-purple-dark text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-cause-purple/20 hover:shadow-xl hover:shadow-cause-purple/30 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        {{ $budget ? 'Update & Lock Budget' : 'Assign & Lock Budget' }}
                                    </button>
                                </div>
                            @else
                                <div class="pt-4">
                                    <div class="w-full bg-gray-100 text-gray-400 font-bold py-4 px-6 rounded-2xl flex items-center justify-center border border-dashed border-gray-300">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Budget Finalized
                                    </div>
                                </div>
                            @endif
                        </form>
                    @else
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Please select a term to manage budget</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Budget Insights -->
        <div class="space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Current Status
                </h3>
                
                @if($budget)
                    <div class="space-y-6">
                        <div class="p-4 bg-gray-50 rounded-2xl">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Remaining Balance</p>
                            <p class="text-2xl font-black text-gray-800">PKR {{ number_format($budget->remaining_amount, 2) }}</p>
                        </div>
                        
                        <div class="p-4 bg-cause-purple/5 rounded-2xl">
                            <p class="text-xs font-bold text-cause-purple/60 uppercase tracking-widest mb-1">Total Allocation</p>
                            <p class="text-2xl font-black text-cause-purple">PKR {{ number_format($budget->total_amount, 2) }}</p>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
                                <span>UTILIZATION</span>
                                <span>{{ $budget->total_amount > 0 ? round((($budget->total_amount - $budget->remaining_amount) / $budget->total_amount) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-cause-purple to-cause-purple-dark h-full rounded-full transition-all duration-500 shadow-sm" 
                                     style="width: {{ $budget->total_amount > 0 ? (($budget->total_amount - $budget->remaining_amount) / $budget->total_amount) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-50">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Budget is currently live</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-400 italic">No budget information available for the selected term.</p>
                    </div>
                @endif
            </div>

            <!-- Admin Note -->
            <div class="bg-gray-900 rounded-2xl shadow-xl p-6 text-white overflow-hidden relative">
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5 transform rotate-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z" />
                </svg>
                <h4 class="text-sm font-black uppercase tracking-widest text-cause-purple mb-4">Financial Authority</h4>
                <p class="text-sm text-gray-300 leading-relaxed">
                    As an Administrator, you define the financial boundaries for each academic term. The HOD will manage approvals within these limits, but cannot exceed them or modify the core allocation.
                </p>
            </div>
        </div>
    </div>
@endsection
