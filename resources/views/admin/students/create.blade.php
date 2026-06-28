@extends('layouts.dashboard')

@section('title', 'Add Student - Admin Dashboard')
@section('page-title', 'Add New Student Data')
@section('page-description', 'Strictly following F25 AI-SE Template structure')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    
    <!-- Bulk Upload Section -->
    <div class="bg-indigo-50 border-2 border-dashed border-indigo-200 rounded-2xl p-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-indigo-900 mb-2">📤 Bulk Upload Students</h3>
                <p class="text-indigo-700 text-sm">Upload a CSV file matching the exact template structure to add multiple students at once.</p>
                @if(session('bulk_errors'))
                    <div class="mt-4 bg-red-100 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700 font-bold text-sm mb-2">Skipped Rows (Errors):</p>
                        <ul class="text-xs text-red-600 list-disc list-inside max-h-40 overflow-y-auto">
                            @foreach(session('bulk_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-3">
                <form action="{{ route('admin.students.bulk') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="bulk_file" accept=".csv" required class="block w-full text-sm text-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full font-semibold transition shadow-lg">Upload CSV</button>
                </form>
                <a href="{{ route('admin.students.sample') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Sample CSV Template
                </a>
            </div>
        </div>
    </div>

    <!-- Manual Entry Form -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-cause-purple-dark text-white">
            <h3 class="text-2xl font-bold">📄 Manual Student Entry</h3>
            <p class="text-purple-100 text-sm mt-1">Please fill in all compulsory fields correctly.</p>
        </div>

        <form action="{{ route('admin.students.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-red-700 font-bold mb-1">Validation Errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Personal Information Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
                    <span class="bg-purple-100 text-purple-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <h4 class="text-lg font-bold text-gray-800">Personal Information</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">1. Student Name *</label>
                        <input type="text" name="student_name" value="{{ old('student_name') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">2. Father Name *</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">3. Gender *</label>
                        <select name="gender" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                            <option value="">Select Gender</option>
                            <option value="M" @selected(old('gender') == 'M')>M (Male)</option>
                            <option value="F" @selected(old('gender') == 'F')>F (Female)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">4. Roll No *</label>
                        <input type="text" name="roll_no" value="{{ old('roll_no') }}" placeholder="e.g. BCS-F25-001" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">5. Admission Date *</label>
                        <input type="date" name="admission_date" value="{{ old('admission_date') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">6. Nationality *</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">7. CNIC Number *</label>
                        <input type="text" name="cnic_number" value="{{ old('cnic_number') }}" placeholder="12345-1234567-1" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">8. Passport Number (Optional)</label>
                        <input type="text" name="passport_number" value="{{ old('passport_number') }}" class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">9. Date of Birth *</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Contact & Address Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
                    <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <h4 class="text-lg font-bold text-gray-800">Contact & Address</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">10. Phone Number *</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">11. Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">12. Domicile District *</label>
                        <input type="text" name="domicile_district" value="{{ old('domicile_district') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">13. Domicile Province *</label>
                        <input type="text" name="domicile_province" value="{{ old('domicile_province') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">14. Mailing Address *</label>
                        <textarea name="mailing_address" required rows="2" class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">{{ old('mailing_address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">15. City (Mailing Address) *</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="w-full border-gray-300 rounded-lg focus:ring-cause-purple focus:border-cause-purple shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Academic Qualifications Section -->
            <div class="space-y-6">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <h4 class="text-lg font-bold text-gray-800">Academic Qualifications</h4>
                </div>

                <!-- SSC -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-4 bg-gray-50 rounded-xl">
                    <div class="md:col-span-4 font-bold text-gray-700 text-sm border-b border-gray-200 pb-1 mb-2">SSC (Secondary School)</div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">16. Degree Name *</label>
                        <input type="text" name="ssc_degree_name" value="{{ old('ssc_degree_name', 'Matric') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">17. Board Name *</label>
                        <input type="text" name="ssc_board_name" value="{{ old('ssc_board_name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">18. Total Marks *</label>
                        <input type="number" name="ssc_total_marks" value="{{ old('ssc_total_marks') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">19. Obtained Marks *</label>
                        <input type="number" name="ssc_obtained_marks" value="{{ old('ssc_obtained_marks') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                </div>

                <!-- HSSC -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-4 bg-gray-50 rounded-xl">
                    <div class="md:col-span-4 font-bold text-gray-700 text-sm border-b border-gray-200 pb-1 mb-2">HSSC (Higher Secondary)</div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">20. Degree Name *</label>
                        <input type="text" name="hssc_degree_name" value="{{ old('hssc_degree_name', 'FSc') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">21. Nomenclature *</label>
                        <select name="hssc_nomenclature" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="">Select Code</option>
                            <option value="1" @selected(old('hssc_nomenclature') == '1')>1 (Int-Math)</option>
                            <option value="2" @selected(old('hssc_nomenclature') == '2')>2 (A-Math)</option>
                            <option value="3" @selected(old('hssc_nomenclature') == '3')>3 (Pre-Med)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">22. Board Name *</label>
                        <input type="text" name="hssc_board_name" value="{{ old('hssc_board_name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">23. Total *</label>
                            <input type="number" name="hssc_total_marks" value="{{ old('hssc_total_marks') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">24. Obtained *</label>
                            <input type="number" name="hssc_obtained_marks" value="{{ old('hssc_obtained_marks') }}" required class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Clear Form</button>
                <button type="submit" class="px-8 py-2 bg-cause-purple hover:bg-cause-purple-dark text-white rounded-lg font-bold shadow-lg transition">Save Student Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
