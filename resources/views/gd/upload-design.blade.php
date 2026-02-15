@extends('layouts.dashboard')

@section('title', 'Upload Design - CAUSE Smart Society')
@section('page-title', 'Upload Design')
@section('page-description', 'Upload a design for this event')

@section('sidebar')
    <a href="{{ route('gd.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('gd.dashboard') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Upload Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload Design</h3>
                
                <form action="{{ route('gd.upload.save', $event->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Design Category *</label>
                        <select name="design_category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                            <option value="">Select Category</option>
                            <option value="poster">Poster</option>
                            <option value="banner">Banner</option>
                            <option value="social_media">Social Media</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-cause-purple transition">
                            <input type="file" name="image" accept="image/*" class="hidden" id="imageInput" onchange="previewImage(this)">
                            <label for="imageInput" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600">Click to upload image</p>
                                <p class="text-sm text-gray-400 mt-1">Max 5MB (PNG, JPG, GIF)</p>
                            </label>
                            <div id="imagePreview" class="mt-4 hidden">
                                <img id="previewImg" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Or Provide Image Link</label>
                        <input type="url" name="image_link" placeholder="https://example.com/design.png"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple">
                        <p class="text-sm text-gray-500 mt-1">Provide a direct link to the image (Google Drive, Dropbox, etc.)</p>
                    </div>
                    
                    <button type="submit" class="w-full bg-cause-purple hover:bg-cause-purple-dark text-white font-medium py-3 px-4 rounded-lg">
                        Upload Design
                    </button>
                </form>
            </div>
        </div>

        <!-- Event Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Event Details</h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Event Name</p>
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($event->expected_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Venue</p>
                        <p class="font-medium text-gray-800">{{ $event->venue }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-gray-700 text-sm">{{ $event->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Existing Designs -->
            @if($event->graphics && $event->graphics->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Existing Designs</h4>
                <div class="space-y-2">
                    @foreach($event->graphics as $graphic)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $graphic->design_category)) }}</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $graphic->status === 'approved' ? 'bg-green-100 text-green-800' : ($graphic->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($graphic->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
