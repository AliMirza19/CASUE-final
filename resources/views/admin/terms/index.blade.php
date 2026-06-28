@extends('layouts.dashboard')

@section('title', 'Manage Terms - CAUSE Smart Society')
@section('page-title', 'Manage Academic Terms')
@section('page-description', 'Create and manage academic terms')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">All Academic Terms</h3>
        <a href="{{ route('admin.terms.create') }}" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-2 px-4 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create New Term
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($terms as $term)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-cause-purple">{{ $term->term_code }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $term->term_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($term->start_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($term->end_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($term->status === 'active')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($term->status === 'active')
                                <form action="{{ route('admin.terms.deactivate', $term->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Deactivate</button>
                                </form>
                            @else
                                <form action="{{ route('admin.terms.activate', $term->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">Activate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No terms found. Create your first term.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
