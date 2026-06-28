@extends('layouts.dashboard')

@section('title', 'Assign Tasks - CAUSE Smart Society')
@section('page-title', 'Assign Team Tasks')
@section('page-description', 'Assign specific duties and instructions to society teams and volunteers')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Assign New Team Task -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden self-start">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-semibold text-gray-800">🎯 Assign Team Task</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Task Title</label>
                        <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., Finalize Event Poster">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Role</label>
                        <select name="assigned_to_role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="gd">Graphic Designer</option>
                            <option value="vc">Volunteer Coordinator</option>
                            <option value="smt">Social Media Director</option>
                            <option value="photo">Photography Director</option>
                            <option value="video">Videography Director</option>
                            <option value="doc">Documentation Director</option>
                            <option value="deco">Decoration Director</option>
                            <option value="sa">General Secretary</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Related Event (Optional)</label>
                        <select name="event_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">No specific event</option>
                            @foreach($fullyApprovedEvents as $event)
                                <option value="{{ $event->id }}">{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instructions</label>
                        <textarea name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="What needs to be done?"></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Assign Task
                    </button>
                </form>
            </div>
        </div>

        <!-- Assign Volunteer Task -->
        @if($assignedVolunteers->count() > 0)
        <div class="bg-white rounded-xl shadow-md overflow-hidden self-start">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-800">🤝 Assign Volunteer Task</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="assigned_to_role" value="student">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Task Title</label>
                        <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="e.g., Manage Registration Desk">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Volunteer</label>
                        <select name="assigned_to_user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                            <option value="">Choose a volunteer...</option>
                            @foreach($assignedVolunteers as $vol)
                                <option value="{{ $vol->user_id }}">
                                    {{ $vol->user->name }} ({{ $vol->role_description }}) - {{ $vol->event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="date" name="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instructions</label>
                        <textarea name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Specific duties for this volunteer..."></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                        Assign to Volunteer
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <p class="text-lg font-medium">No volunteers assigned to approved events yet.</p>
            <p class="mt-2">Volunteers must be assigned to events before you can give them individual tasks.</p>
        </div>
        @endif
    </div>


@endsection
