@extends('layouts.dashboard')

@section('title', 'Student Messages - CAUSE Smart Society')

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cause-purple to-purple-700 text-white p-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold">Student Messages</h1>
                        <p class="text-purple-100 text-sm">View and respond to student messages</p>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="p-6">
                @if($students->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-500">No messages yet</p>
                        <p class="text-sm text-gray-400 mt-2">Students will appear here when they message you</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($students as $student)
                            <a href="{{ route('president.student-messages.conversation', $student->id) }}" 
                               class="block bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-full bg-cause-purple flex items-center justify-center text-white text-lg font-semibold">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $student->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $student->email }}</p>
                                            @if($student->last_message)
                                                <p class="text-sm text-gray-500 mt-1 truncate max-w-md">
                                                    {{ Str::limit($student->last_message->message_text, 50) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        @if($student->last_message)
                                            <span class="text-xs text-gray-400">
                                                {{ $student->last_message->created_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        @if($student->unread_count > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 font-semibold">
                                                {{ $student->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
