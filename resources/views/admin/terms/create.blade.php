@extends('layouts.dashboard')

@section('title', 'Create Term - CAUSE Smart Society')
@section('page-title', 'Create Academic Term')
@section('page-description', 'Add a new academic term')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.terms.index') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Terms
        </a>
    </div>

    <div class="max-w-xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">New Academic Term</h3>
            
            <form action="{{ route('admin.terms.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Term Name *</label>
                        <input type="text" name="term_name" value="{{ old('term_name', $suggestion['term_name']) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="e.g., 261 - Spring 2026">
                        @error('term_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Term Code *</label>
                        <input type="text" name="term_code" value="{{ old('term_code', $suggestion['term_code']) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple"
                            placeholder="e.g., 261">
                        @error('term_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $suggestion['start_date']) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $suggestion['end_date']) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-4 rounded-lg">
                    Create Term
                </button>
            </form>
        </div>
    </div>
@endsection
