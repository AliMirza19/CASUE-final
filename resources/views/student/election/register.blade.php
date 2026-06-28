@extends('layouts.dashboard')

@section('title', 'Register as Candidate - CAUSE Smart Society')
@section('page-title', 'Candidacy Registration')
@section('page-description', 'Submit your profile to run in the upcoming elections')

@section('sidebar')
    @include('student.partials.sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <h3 class="text-xl font-bold">Registration Form</h3>
            <p class="text-blue-100 text-sm mt-1">Please provide accurate information for your campaign.</p>
        </div>
        
        <form action="{{ route('student.election.submit') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- VP Details -->
                <div class="col-span-1">
                    <label for="vp_name" class="block text-sm font-semibold text-gray-700 mb-1 font-bold">Vice President Name <span class="text-red-500">*</span></label>
                    <input type="text" id="vp_name" name="vp_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('vp_name') border-red-500 @enderror"
                           placeholder="Enter your VP partner's name"
                           value="{{ old('vp_name') }}">
                    @error('vp_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="col-span-1">
                    <label for="vp_reg_id" class="block text-sm font-semibold text-gray-700 mb-1 font-bold">VP Reg ID <span class="text-red-500">*</span></label>
                    <input type="text" id="vp_reg_id" name="vp_reg_id" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('vp_reg_id') border-red-500 @enderror"
                           placeholder="e.g. 2021-CS-123"
                           value="{{ old('vp_reg_id') }}">
                    @error('vp_reg_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-1">
                    <label for="manifesto" class="block text-sm font-semibold text-gray-700 font-bold">Your Manifesto / Vision <span class="text-red-500">*</span></label>
                    <button type="button" onclick="optimizeManifesto()" id="btnOptimize"
                            class="text-xs bg-cause-purple hover:bg-cause-purple-dark text-white font-bold py-1 px-3 rounded flex items-center transition shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        AI Optimize
                    </button>
                </div>
                <textarea id="manifesto" name="manifesto" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('manifesto') border-red-500 @enderror"
                          placeholder="Describe your goals, vision, and why students should vote for you...">{{ old('manifesto') }}</textarea>
                
                <div id="aiOptimizationResult" class="hidden mt-3 p-4 bg-indigo-50 border border-indigo-100 rounded-lg text-sm text-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-indigo-800">AI Suggested Version:</span>
                        <button type="button" onclick="applyAiVersion()" class="text-xs bg-indigo-200 hover:bg-indigo-300 text-indigo-800 px-2 py-1 rounded font-bold">Apply This</button>
                    </div>
                    <div id="aiVersionText" class="whitespace-pre-line text-sm italic"></div>
                </div>

                <div class="flex justify-between mt-1">
                    <p class="text-xs text-gray-500 italic">This will be visible to all students in the Voting Hall.</p>
                    <p class="text-xs text-gray-500" id="char-count">0 / 2000 characters</p>
                </div>
                @error('manifesto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8 p-6 bg-blue-50 rounded-xl border-2 border-dashed border-blue-200">
                <label class="block text-sm font-bold text-blue-800 mb-3">Upload Campaign Poster / Photo</label>
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <div id="image-preview" class="w-24 h-24 rounded-lg bg-white border-2 border-gray-200 overflow-hidden flex items-center justify-center text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="photo" class="cursor-pointer bg-white hover:bg-gray-50 text-blue-600 font-semibold py-2 px-4 border border-blue-600 rounded shadow-sm transition-colors inline-block">
                            Choose Image
                        </label>
                        <p class="text-xs text-blue-600 mt-2 italic font-medium">Recommended size: Square (e.g. 500x500px). Max 2MB.</p>
                    </div>
                </div>
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                <a href="{{ route('student.election') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Center
                </a>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-3 px-10 rounded-lg transition-all shadow-lg transform hover:-translate-y-px">
                    Submit Candidacy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    const textarea = document.getElementById('manifesto');
    const charCount = document.getElementById('char-count');
    textarea.addEventListener('input', () => {
        const length = textarea.value.length;
        charCount.textContent = `${length} / 2000 characters`;
        charCount.className = length > 2000 ? 'text-xs text-red-500 font-bold' : 'text-xs text-gray-500';
    });

    function optimizeManifesto() {
        const manifesto = textarea.value.trim();
        if (manifesto.length < 50) {
            alert('Please write at least 50 characters before optimizing.');
            return;
        }

        const btn = document.getElementById('btnOptimize');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Analyzing...';
        btn.disabled = true;

        fetch('{{ route("student.election.optimize-manifesto") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ draft: manifesto })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('aiVersionText').innerText = data.result;
                document.getElementById('aiOptimizationResult').classList.remove('hidden');
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function applyAiVersion() {
        const aiText = document.getElementById('aiVersionText').innerText;
        textarea.value = aiText;
        textarea.dispatchEvent(new Event('input'));
        document.getElementById('aiOptimizationResult').classList.add('hidden');
    }
</script>
@endsection
