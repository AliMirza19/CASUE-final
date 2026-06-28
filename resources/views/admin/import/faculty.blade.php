@extends('layouts.dashboard')

@section('title', 'Import Faculty - CAUSE')
@section('page-title', 'Bulk Faculty Import')
@section('page-description', 'Onboard multiple faculty members efficiently using CSV data.')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8 bg-gradient-to-br from-indigo-600 to-indigo-900 text-white flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-bold">Faculty CSV Upload</h3>
                <p class="opacity-80 text-sm mt-2">Automate the registration of academic and engineering staff.</p>
            </div>
            <a href="/samples/faculty_sample.csv" download class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-2 px-4 rounded-lg text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Sample
            </a>
        </div>
        
        <div class="p-8">
            <form action="{{ route('admin.import.faculty') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-10 flex flex-col items-center justify-center hover:border-indigo-500 transition-colors cursor-pointer group" onclick="document.getElementById('file-input').click()">
                    <svg class="w-12 h-12 text-gray-400 group-hover:text-indigo-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-gray-600 font-medium">Click to select or drag and drop your CSV file</p>
                    <p class="text-gray-400 text-xs mt-1">Maximum file size: 10MB</p>
                    <input id="file-input" type="file" name="file" class="hidden" accept=".csv" onchange="updateFileName(this)">
                    <p id="file-name" class="mt-4 text-indigo-600 font-bold hidden"></p>
                </div>

                <div class="bg-blue-50 rounded-lg p-5 border border-blue-100">
                    <h4 class="text-blue-800 font-bold text-sm mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        Required CSV Column Order (21 Columns):
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px] text-blue-600 font-mono">
                        <div>1. Sr.#</div>
                        <div>2. Title</div>
                        <div>3. Name</div>
                        <div>4. Gender</div>
                        <div>5. CNIC/Passport</div>
                        <div>6. DOB</div>
                        <div>7. Mobile</div>
                        <div>8. Email</div>
                        <div>9. Address</div>
                        <div>10. Province</div>
                        <div>11. City</div>
                        <div>12. ContractType</div>
                        <div>13. Rank</div>
                        <div>14. JoiningDate</div>
                        <div>15. DegName</div>
                        <div>16. DegType</div>
                        <div>17. Field</div>
                        <div>18. Country</div>
                        <div>19. Uni</div>
                        <div>20. StartDate</div>
                        <div>21. EndDate</div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition hover:-translate-y-1 flex items-center">
                        <span>Execute Import</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : '';
    const nameEl = document.getElementById('file-name');
    if (fileName) {
        nameEl.textContent = 'Selected: ' + fileName;
        nameEl.classList.remove('hidden');
    } else {
        nameEl.classList.add('hidden');
    }
}
</script>
@endsection
