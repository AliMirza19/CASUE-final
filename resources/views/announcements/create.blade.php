@extends('layouts.dashboard')

@section('title', 'Post Announcement - CAUSE Smart Society')
@section('page-title', 'Post Announcement')
@section('page-description', 'Share important news and updates with the society')

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
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-10 text-white">
            <h3 class="text-3xl font-black mb-2">Create Announcement</h3>
            <p class="text-indigo-100 opacity-80">This will be broadcasted to the recent news feed.</p>
        </div>

        <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Announcement Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Annual Grand Dinner Approved" 
                           class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Description *</label>
                    <textarea name="description" rows="4" required placeholder="Provide details about the announcement..."
                              class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"></textarea>
                </div>

                <!-- PREMIUM UPLOAD ZONE -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-center">📸 Upload Banner Image</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- URL Option -->
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative bg-white p-4 rounded-2xl border border-gray-100">
                                <label class="block text-[10px] font-bold text-gray-400 mb-2">IMAGE URL</label>
                                <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." 
                                       class="w-full text-sm bg-gray-50 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- File Upload Option -->
                        <div class="relative group" onclick="document.getElementById('image_upload').click()">
                            <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative bg-white p-4 rounded-2xl border-2 border-dashed border-gray-200 hover:border-purple-500 cursor-pointer transition-colors text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span id="file_label" class="text-xs font-bold text-gray-500">CLICK TO UPLOAD</span>
                                </div>
                                <input type="file" id="image_upload" name="image" accept="image/*" class="hidden" onchange="document.getElementById('file_label').textContent = this.files[0].name; document.getElementById('file_label').classList.replace('text-gray-500', 'text-green-600')">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Link -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Action Link (Optional)</label>
                    <input type="url" name="link_url" placeholder="https://forms.gle/..." 
                           class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                </div>

                <!-- Target Role -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Target Role (Optional)</label>
                    <select name="target_role" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">Everyone</option>
                        <option value="student">Students Only</option>
                        <option value="faculty">Faculty Only</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ url()->previous() }}" class="px-8 py-4 text-gray-500 font-bold hover:text-gray-700 transition-colors">Cancel</a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition-all">
                    🚀 Post Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
