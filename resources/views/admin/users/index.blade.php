@extends('layouts.dashboard')

@section('title', 'Manage Users - CAUSE Smart Society')
@section('page-title', 'Manage Users')
@section('page-description', 'Create and manage system users')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('admin.terms.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Manage Terms
    </a>
    <a href="{{ route('admin.users.index') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Manage Users
    </a>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center space-x-2">
                <select name="role" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Roles</option>
                    <option value="hod" {{ request('role') === 'hod' ? 'selected' : '' }}>HOD</option>
                    <option value="patron" {{ request('role') === 'patron' ? 'selected' : '' }}>Patron</option>
                    <option value="president" {{ request('role') === 'president' ? 'selected' : '' }}>President</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                    <option value="faculty" {{ request('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                    <option value="sa" {{ request('role') === 'sa' ? 'selected' : '' }}>SA</option>
                    <option value="vc" {{ request('role') === 'vc' ? 'selected' : '' }}>VC</option>
                    <option value="gd" {{ request('role') === 'gd' ? 'selected' : '' }}>GD</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm">Search</button>
            </form>
        </div>

    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reg ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-cause-purple rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-medium">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->reg_id }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                @switch($user->role)
                                    @case('admin') bg-red-100 text-red-800 @break
                                    @case('hod') bg-orange-100 text-orange-800 @break
                                    @case('patron') bg-purple-100 text-purple-800 @break
                                    @case('president') bg-blue-100 text-blue-800 @break
                                    @case('student') bg-green-100 text-green-800 @break
                                    @case('faculty') bg-teal-100 text-teal-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">{{ strtoupper($user->role) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>

                                @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection
