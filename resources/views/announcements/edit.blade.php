@extends('layouts.dashboard')

@section('title', 'Edit Announcement - CAUSE Smart Society')
@section('page-title', 'Edit Announcement')

@section('sidebar')
    @if(auth()->user()->role === 'admin')
        @include('partials.admin-sidebar')
    @elseif(auth()->user()->isAppointedHod())
        @include('partials.hod-sidebar')
    @elseif(auth()->user()->isAppointedPatron())
        @include('partials.patron-sidebar')
    @elseif(auth()->user()->role === 'president')
        @include('partials.president-sidebar')
    @else
        @include('partials.team-sidebar')
    @endif
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-10 text-white">
            <h3 class="text-3xl font-black mb-2">Edit Announcement</h3>
            <p class="text-orange-100 opacity-80">Update the details of your broadcast.</p>
        </div>

        <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Announcement Title *</label>
                    <input type="text" name="title" value="{{ $announcement->title }}" required 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description *</label>
                    <textarea name="description" rows="4" required 
                              class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all">{{ $announcement->description }}</textarea>
                </div>

                <!-- Image URL -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Image URL</label>
                    <input type="url" name="image_url" value="{{ $announcement->image_url }}" 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Or Update Image</label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                </div>

                <!-- Action Link -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Action Link</label>
                    <input type="url" name="link_url" value="{{ $announcement->link_url }}" 
                           class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.dashboard') }}" class="px-8 py-4 text-gray-500 font-bold hover:text-gray-700 transition-colors">Cancel</a>
                <button type="submit" class="px-10 py-4 bg-amber-500 text-white font-black rounded-2xl shadow-xl shadow-amber-200 hover:bg-amber-600 hover:scale-105 transition-all">
                    ✨ Update Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
