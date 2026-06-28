@extends('layouts.dashboard')

@section('title', 'Manage Users - CAUSE Smart Society')
@section('page-title', 'Manage Users')
@section('page-description', 'Create and manage system users')

@section('sidebar')
    @include('partials.admin-sidebar')
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
                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 mr-3 shadow-sm">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                                         alt="{{ $user->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <span class="block font-bold text-gray-800">{{ $user->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->reg_id }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full shadow-sm {{ $user->getDisplayRoleColor() }}">
                                {{ $user->getDisplayRole() }}
                            </span>
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
