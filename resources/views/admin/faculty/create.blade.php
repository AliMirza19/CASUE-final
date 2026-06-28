@extends('layouts.dashboard')

@section('title', 'Add Faculty - Admin Dashboard')
@section('page-title', 'Add New Faculty Member')
@section('page-description', 'Admin Data Template Structure')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Bulk Upload Section -->
    <div class="bg-blue-50 border-2 border-dashed border-blue-200 rounded-2xl p-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-blue-900 mb-2">📤 Bulk Upload Faculty</h3>
                <p class="text-blue-700 text-sm">Upload a CSV file matching the exact 22-column template structure.</p>
                @if(session('bulk_errors'))
                    <div class="mt-4 bg-red-100 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700 font-bold text-sm mb-2">Errors in File:</p>
                        <ul class="text-xs text-red-600 list-disc list-inside max-h-40 overflow-y-auto">
                            @foreach(session('bulk_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-3">
                <form action="{{ route('admin.faculty.bulk') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="bulk_file" accept=".csv" required class="block w-full text-sm text-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-semibold transition shadow-lg">Upload CSV</button>
                </form>
                <a href="{{ route('admin.faculty.sample') }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold flex items-center justify-center gap-1 underline">
                    Download Sample CSV (Dr. Zahoor Alam Template)
                </a>
            </div>
        </div>
    </div>

    <!-- Manual Entry Form -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-700 to-indigo-800 text-white">
            <h3 class="text-2xl font-bold">👨‍🏫 Faculty Member Registration</h3>
            <p class="text-blue-100 text-sm mt-1">Please ensure dates follow the DD/MM/YY format.</p>
        </div>

        <form action="{{ route('admin.faculty.store') }}" method="POST" class="p-8 space-y-8">
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

            <!-- Section 1: Basic Personal Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">1. Sr.#</label>
                    <input type="text" name="sr_no" value="{{ old('sr_no') }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">2. Title *</label>
                    <select name="title" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="Dr." @selected(old('title') == 'Dr.')>Dr.</option>
                        <option value="Mr." @selected(old('title') == 'Mr.')>Mr.</option>
                        <option value="Ms." @selected(old('title') == 'Ms.')>Ms.</option>
                        <option value="Engr." @selected(old('title') == 'Engr.')>Engr.</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">3. Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">4. Gender *</label>
                    <select name="gender" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="M" @selected(old('gender') == 'M')>M</option>
                        <option value="F" @selected(old('gender') == 'F')>F</option>
                        <option value="Other" @selected(old('gender') == 'Other')>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">5. CNIC / Passport *</label>
                    <input type="text" name="cnic_passport" value="{{ old('cnic_passport') }}" placeholder="XXXXX-XXXXXXX-X" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">6. DOB (DD/MM/YY) *</label>
                    <input type="text" name="dob" value="{{ old('dob') }}" placeholder="15/05/85" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">7. Mobile Number *</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">8. Email (University email) *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">9. Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">10. Province *</label>
                    <input type="text" name="province" value="{{ old('province') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">11. City *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
            </div>

            <!-- Section 2: Professional Details -->
            <div class="bg-gray-50 p-6 rounded-xl space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">12. Contract Type *</label>
                        <input type="text" name="contract_type" value="{{ old('contract_type') }}" placeholder="Regular / Visiting" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">13. Academic Rank *</label>
                        <input type="text" name="academic_rank" value="{{ old('academic_rank') }}" placeholder="Lecturer / Assistant Prof" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">14. Joining Date *</label>
                        <input type="text" name="joining_date" value="{{ old('joining_date') }}" placeholder="DD/MM/YY" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">15. Leaving Date</label>
                        <input type="text" name="leaving_date" disabled placeholder="Keep Empty" class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 3: Highest Degree Details (Merged Header Concept) -->
            <div class="border-2 border-blue-100 rounded-xl overflow-hidden">
                <div class="bg-blue-50 px-6 py-3 border-b-2 border-blue-100 flex items-center justify-between">
                    <h4 class="text-blue-800 font-bold uppercase text-sm tracking-wider">🎓 Highest Degree Details (Columns 16-22)</h4>
                    <span class="text-blue-600 text-xs font-medium">As per template Section 2</span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">16. Degree Name *</label>
                        <input type="text" name="degree_name" value="{{ old('degree_name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">17. Degree Type *</label>
                        <input type="text" name="degree_type" value="{{ old('degree_type') }}" placeholder="MS / PhD" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">18. Field of Study *</label>
                        <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">19. Country *</label>
                        <input type="text" name="degree_awarding_country" value="{{ old('degree_awarding_country') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">20. University Name *</label>
                        <input type="text" name="university_name" value="{{ old('university_name') }}" required class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">21. Start (DD/MM/YY)</label>
                            <input type="text" name="degree_start_date" value="{{ old('degree_start_date') }}" placeholder="DD/MM/YY" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">22. End (DD/MM/YY)</label>
                            <input type="text" name="degree_end_date" value="{{ old('degree_end_date') }}" placeholder="DD/MM/YY" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Clear</button>
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition">Save Faculty Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
