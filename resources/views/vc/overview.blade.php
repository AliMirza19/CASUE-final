@extends('layouts.dashboard')

@section('title', (request()->routeIs('vc.dashboard') ? 'VC Dashboard' : 'Volunteer Overview') . ' - CAUSE Smart Society')
@section('page-title', request()->routeIs('vc.dashboard') ? 'Volunteer Coordinator Dashboard' : 'Volunteer Overview')
@section('page-description', request()->routeIs('vc.dashboard') ? 'Welcome to your management portal' : 'Summary of approved events and volunteer assignments')

@section('sidebar')
    @include('partials.vc-sidebar')
@endsection

@section('content')
    @include('partials.tasks-widget')

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalApprovedEvents }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Volunteers Assigned</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalVolunteersAssigned }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-cause-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Events Staffed</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $eventsWithVolunteers }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Events -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Approved Events</h3>
            <p class="text-gray-600 text-sm mt-1">Assign volunteers to these events</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Volunteers</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($approvedEvents as $event)
                        @php
                            $volunteerCount = $event->assignedVolunteers->count();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $event->title }}</div>
                                <div class="text-sm text-gray-500">{{ $event->student->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-800">
                                {{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->venue }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($volunteerCount > 0)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $volunteerCount }} assigned
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        None
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openVolunteerModal({{ $event->id }})" class="bg-cause-purple hover:bg-cause-purple-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                    {{ $volunteerCount > 0 ? 'Manage' : 'Assign' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No approved events available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Volunteer Pool / Assign Task Section -->
    <div id="volunteer-pool" class="bg-white rounded-lg shadow-md overflow-hidden mb-8 scroll-mt-20">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Volunteer Pool (Students who Joined)</h3>
                <p class="text-gray-600 text-sm mt-1">Assign tasks or events to students from the society pool</p>
            </div>
            <span class="px-3 py-1 bg-cause-purple/10 text-cause-purple text-xs font-bold rounded-full border border-cause-purple/20">
                {{ $volunteerPool->count() }} Students Joined
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skills</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($volunteerPool as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-gray-600 font-mono text-sm">{{ $student->reg_id }}</td>
                            <td class="px-6 py-4">
                                @if($student->skills)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(explode(',', $student->skills) as $skill)
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] rounded border border-indigo-100">{{ trim($skill) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">No skills listed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openAssignTaskModal({{ $student->id }}, '{{ addslashes($student->name) }}')" 
                                        class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-xs font-bold bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 transition-all">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Assign Task
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p>No students have joined the volunteer pool yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Upcoming Events -->
    @if($upcomingEvents->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Events (Next 30 Days)</h3>
        <div class="space-y-3">
            @foreach($upcomingEvents as $event)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                        <p class="text-sm text-gray-500">{{ $event->venue }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-cause-purple">{{ \Carbon\Carbon::parse($event->expected_date)->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->expected_date)->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Assign Task Modal -->
    <div id="assignTaskModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeAssignTaskModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="assignTaskForm" action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="assigned_to_user_id" id="task_student_id">
                    <input type="hidden" name="assigned_to_role" value="volunteer">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center mb-4">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Assign Task</h3>
                                <p class="text-sm text-gray-500">Assigning task to <span id="task_student_name" class="font-bold text-indigo-600"></span></p>
                            </div>
                        </div>

                        <div class="space-y-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Task Title</label>
                                <input type="text" name="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border py-2 px-3" placeholder="e.g. Stage Management Help">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description / Instructions</label>
                                <textarea name="description" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border py-2 px-3" placeholder="Explain what the student needs to do..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Related Event (Optional)</label>
                                <select name="event_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border py-2 px-3">
                                    <option value="">No specific event</option>
                                    @foreach($approvedEvents as $event)
                                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border py-2 px-3">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Assign Task
                        </button>
                        <button type="button" onclick="closeAssignTaskModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Volunteer Management Modal -->
    <div id="volunteerModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeVolunteerModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 text-gray-800 border border-gray-100">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" onclick="closeVolunteerModal()" class="text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="modalEventTitle">Manage Volunteers</h3>
                <p class="text-sm text-gray-500 mb-6" id="modalEventDetails"></p>
                
                <!-- Assigned Volunteers List -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-cause-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Current Assigned Volunteers
                    </h4>
                    <div id="assignedVolunteersList" class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[100px] max-h-60 overflow-y-auto shadow-inner">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Assign New Volunteer Form -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Assign New Volunteer
                        </h4>
                        
                        <button type="button" onclick="getAiSuggestions()" id="btnAiSuggest"
                                class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold py-1 px-3 rounded flex items-center transition">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            AI Suggestions
                        </button>
                    </div>

                    <div id="aiSuggestionsResult" class="hidden mb-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                        <p class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider mb-2">AI Ranked Suggestions:</p>
                        <div id="aiSuggestionsList" class="space-y-2 max-h-40 overflow-y-auto">
                            <!-- suggestions here -->
                        </div>
                    </div>
                    
                    <form id="assignVolunteerForm" method="POST" action="">
                        @csrf
                        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                            
                            <div class="sm:col-span-6">
                                <label for="student_search" class="block text-sm font-medium text-gray-700">Search Student by Reg ID / Name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="student_search" class="focus:ring-cause-purple focus:border-cause-purple block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="Type REG-ID or Name...">
                                    
                                    <!-- Search Results Dropdown -->
                                    <div id="searchResults" class="hidden absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200 py-1 max-h-48 overflow-auto">
                                        <!-- populated dynamically -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selected Student Info -->
                            <div id="selectedStudentInfo" class="sm:col-span-6 flex items-center justify-between p-3 bg-indigo-50 border border-indigo-100 rounded-md hidden">
                                <div>
                                    <p class="text-sm font-medium text-indigo-900" id="selectedStudentName">Name</p>
                                    <p class="text-xs text-indigo-700" id="selectedStudentRegId">ID</p>
                                </div>
                                <button type="button" onclick="clearSelection()" class="text-indigo-500 hover:text-indigo-700 p-1">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <input type="hidden" name="user_id" id="selected_user_id">

                            <div class="sm:col-span-6">
                                <label for="role_description" class="block text-sm font-medium text-gray-700">Role/Team Assignment</label>
                                <div class="mt-1">
                                    <input type="text" name="role_description" id="role_description" required class="shadow-sm focus:ring-cause-purple focus:border-cause-purple block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="e.g. Stage Setup, Usher, Media Team">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:flex sm:flex-row-reverse">
                            <button type="submit" id="assignBtn" disabled class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cause-purple text-base font-medium text-white hover:bg-cause-purple-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cause-purple sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Assign Student
                            </button>
                            <button type="button" onclick="closeVolunteerModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cause-purple sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const eventsData = @json($approvedEvents->keyBy('id'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    
    // Elements
    const modal = document.getElementById('volunteerModal');
    const searchInput = document.getElementById('student_search');
    const searchResults = document.getElementById('searchResults');
    const selectedUserIdInput = document.getElementById('selected_user_id');
    const assignBtn = document.getElementById('assignBtn');
    const selectedStudentInfo = document.getElementById('selectedStudentInfo');
    const form = document.getElementById('assignVolunteerForm');
    
    let searchTimeout = null;
    let currentEventId = null;

    function openVolunteerModal(eventId) {
        currentEventId = eventId;
        const event = eventsData[eventId];
        if(!event) return;
        
        document.getElementById('modalEventTitle').textContent = `Manage Volunteers: ${event.title}`;
        document.getElementById('modalEventDetails').innerHTML = `<strong>Date:</strong> ${new Date(event.expected_date).toLocaleDateString()} &nbsp;|&nbsp; <strong>Venue:</strong> ${event.venue}`;
        
        // Populate existing volunteers
        const listDiv = document.getElementById('assignedVolunteersList');
        listDiv.innerHTML = '';
        
        if (event.assigned_volunteers && event.assigned_volunteers.length > 0) {
            event.assigned_volunteers.forEach(v => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm';
                item.innerHTML = `
                    <div>
                        <p class="text-sm font-medium text-gray-800">${v.user.name}</p>
                        <p class="text-xs text-gray-500">${v.user.reg_id || v.user.cnic} <span class="mx-1">•</span> <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-[10px] uppercase">${v.role_description}</span></p>
                    </div>
                    <div>
                        <form method="POST" action="/vc/events/${eventId}/remove-volunteer/${v.id}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-full transition-colors tooltip" title="Remove Volunteer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                `;
                listDiv.appendChild(item);
            });
        } else {
            listDiv.innerHTML = '<p class="text-sm text-gray-500 p-2 italic">No volunteers assigned yet.</p>';
        }
        
        // Set form action
        form.action = `/vc/events/${eventId}/assign-volunteer`;
        
        // Reset form
        clearSelection();
        document.getElementById('role_description').value = '';
        document.getElementById('instructions').value = '';
        document.getElementById('venue').value = '';
        
        modal.classList.remove('hidden');
    }

    function closeVolunteerModal() {
        modal.classList.add('hidden');
        document.getElementById('aiSuggestionsResult').classList.add('hidden');
    }

    function getAiSuggestions() {
        if (!currentEventId) return;
        
        const btn = document.getElementById('btnAiSuggest');
        const listDiv = document.getElementById('aiSuggestionsList');
        const container = document.getElementById('aiSuggestionsResult');
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = 'Analyzing...';
        btn.disabled = true;
        
        fetch(`/vc/api/suggest-volunteers/${currentEventId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                listDiv.innerHTML = '';
                data.data.forEach(sug => {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-start bg-white p-2 rounded border border-indigo-100 hover:bg-indigo-50 transition cursor-pointer mb-2';
                    row.onclick = () => selectSuggestedVolunteer(sug.id, sug.name);
                    row.innerHTML = `
                        <div class="flex-1">
                            <div class="flex items-center">
                                <span class="text-xs font-bold text-gray-800">${sug.name}</span>
                                <span class="ml-2 px-1 bg-green-100 text-green-700 text-[9px] font-bold rounded">${sug.score}% Match</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-tight mt-1">${sug.reason}</p>
                        </div>
                    `;
                    listDiv.appendChild(row);
                });
                container.classList.remove('hidden');
            } else {
                alert(data.message || 'Failed to get suggestions');
            }
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function selectSuggestedVolunteer(userId, name) {
        selectStudent({id: userId, name: name, reg_id: ''});
        document.getElementById('role_description').focus();
    }

    // Search Logic
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`/vc/search-students?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No students found</div>';
                    } else {
                        data.forEach(student => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-0';
                            div.innerHTML = `<span class="font-medium text-gray-800">${student.name}</span> <span class="text-gray-500 text-xs ml-2">${student.reg_id}</span>`;
                            div.onclick = () => selectStudent(student);
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    function selectStudent(student) {
        selectedUserIdInput.value = student.id;
        document.getElementById('selectedStudentName').textContent = student.name;
        document.getElementById('selectedStudentRegId').textContent = student.reg_id || student.cnic || 'N/A';
        
        searchInput.value = '';
        searchResults.classList.add('hidden');
        searchInput.parentElement.classList.add('hidden'); // hide search input
        
        selectedStudentInfo.classList.remove('hidden');
        assignBtn.disabled = false;
    }

    function clearSelection() {
        selectedUserIdInput.value = '';
        selectedStudentInfo.classList.add('hidden');
        searchInput.parentElement.classList.remove('hidden');
        searchInput.value = '';
        searchInput.focus();
        assignBtn.disabled = true;
    }

    // Close dropdown on click outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add('hidden');
        }
    });
    function openAssignTaskModal(studentId, studentName) {
        document.getElementById('task_student_id').value = studentId;
        document.getElementById('task_student_name').textContent = studentName;
        document.getElementById('assignTaskModal').classList.remove('hidden');
    }

    function closeAssignTaskModal() {
        document.getElementById('assignTaskModal').classList.add('hidden');
    }
</script>
@endpush
