@extends('layouts.dashboard')

@section('title', 'Edit User - CAUSE Smart Society')
@section('page-title', 'User Management: ' . $user->name)
@section('page-description', 'Unified User Data Management')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Directory
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-cause-purple to-indigo-800 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold">User Information Template</h3>
                    <p class="text-purple-100 text-sm opacity-90">Synchronized with Institutional Data Structure</p>
                </div>
                <div class="flex gap-3">
                    <select name="role" id="roleSelector" required 
                        class="bg-white/10 border-white/20 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-white/50 focus:bg-white/20 transition-all cursor-pointer outline-none">
                        <option value="student" class="text-gray-800" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" class="text-gray-800" {{ old('role', $user->role) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="admin" class="text-gray-800" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="sa" class="text-gray-800" {{ old('role', $user->role) === 'sa' ? 'selected' : '' }}>General Secretary</option>
                        <option value="vc" class="text-gray-800" {{ old('role', $user->role) === 'vc' ? 'selected' : '' }}>Volunteer Coordinator</option>
                        <option value="gd" class="text-gray-800" {{ old('role', $user->role) === 'gd' ? 'selected' : '' }}>Graphic Designer</option>
                        <option value="photo" class="text-gray-800" {{ old('role', $user->role) === 'photo' ? 'selected' : '' }}>Photography</option>
                        <option value="video" class="text-gray-800" {{ old('role', $user->role) === 'video' ? 'selected' : '' }}>Videography</option>
                        <option value="smt" class="text-gray-800" {{ old('role', $user->role) === 'smt' ? 'selected' : '' }}>Social Media</option>
                        <option value="doc" class="text-gray-800" {{ old('role', $user->role) === 'doc' ? 'selected' : '' }}>Documentation</option>
                        <option value="deco" class="text-gray-800" {{ old('role', $user->role) === 'deco' ? 'selected' : '' }}>Decoration</option>
                    </select>
                </div>
            </div>

            <div class="p-8 space-y-10">
                <!-- Core Account Section -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <section class="space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-cause-purple">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Account Credentials</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Registration ID *</label>
                            <input type="text" name="reg_id" value="{{ old('reg_id', $user->reg_id) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cause-purple/20 focus:border-cause-purple transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cause-purple/20 focus:border-cause-purple transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cause-purple/20 focus:border-cause-purple transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Current Academic Term *</label>
                            <select name="current_term_id" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cause-purple/20 focus:border-cause-purple transition-all">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('current_term_id', $user->current_term_id) == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }} {{ $term->status === 'active' ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Student Profile Section (Merged) -->
                <section id="studentDetailsSection" class="{{ old('role', $user->role) === 'student' ? '' : 'hidden' }} space-y-8 animate-fadeIn">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Student Profile Data</h4>
                    </div>

                    @php 
                        $sp = $user->studentProfile ?? (object)[]; 
                        // Fallback to user table fields if profile is empty
                        $fatherName = $sp->father_name ?? $user->father_name ?? '';
                        $cnic = $sp->cnic_number ?? $user->cnic ?? '';
                        $phone = $sp->phone_number ?? $user->contact_number ?? '';
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Father Name</label>
                            <input type="text" name="student[father_name]" value="{{ old('student.father_name', $fatherName) }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gender</label>
                            <select name="student[gender]" class="w-full border-gray-300 rounded-xl shadow-sm">
                                <option value="M" {{ (old('student.gender', $sp->gender ?? '') == 'M') ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ (old('student.gender', $sp->gender ?? '') == 'F') ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">CNIC Number</label>
                            <input type="text" name="student[cnic_number]" value="{{ old('student.cnic_number', $cnic) }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone Number</label>
                            <input type="text" name="student[phone_number]" value="{{ old('student.phone_number', $phone) }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Roll Number</label>
                            <input type="text" name="student[roll_no]" value="{{ old('student.roll_no', $sp->roll_no ?? $user->reg_id ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">City</label>
                            <input type="text" name="student[city]" value="{{ old('student.city', $sp->city ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mailing Address</label>
                            <input type="text" name="student[mailing_address]" value="{{ old('student.mailing_address', $sp->mailing_address ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl space-y-6">
                        <h5 class="text-sm font-bold text-gray-700 border-b border-gray-200 pb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Academic Qualifications
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">SSC (Secondary School)</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500">Board Name</label>
                                        <input type="text" name="student[ssc_board_name]" value="{{ old('student.ssc_board_name', $sp->ssc_board_name ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Obtained</label>
                                        <input type="number" name="student[ssc_obtained_marks]" value="{{ old('student.ssc_obtained_marks', $sp->ssc_obtained_marks ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Total</label>
                                        <input type="number" name="student[ssc_total_marks]" value="{{ old('student.ssc_total_marks', $sp->ssc_total_marks ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">HSSC (Higher Secondary)</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500">Board Name</label>
                                        <input type="text" name="student[hssc_board_name]" value="{{ old('student.hssc_board_name', $sp->hssc_board_name ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Obtained</label>
                                        <input type="number" name="student[hssc_obtained_marks]" value="{{ old('student.hssc_obtained_marks', $sp->hssc_obtained_marks ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500">Total</label>
                                        <input type="number" name="student[hssc_total_marks]" value="{{ old('student.hssc_total_marks', $sp->hssc_total_marks ?? '') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Faculty Profile Section (Merged) -->
                <section id="facultyDetailsSection" class="{{ old('role', $user->role) === 'faculty' ? '' : 'hidden' }} space-y-8 animate-fadeIn">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">Faculty Professional Data</h4>
                    </div>

                    @php $fp = $user->facultyProfile ?? (object)[]; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Academic Title</label>
                            <input type="text" name="faculty[title]" value="{{ old('faculty.title', $fp->title ?? '') }}" placeholder="Dr. / Mr. / Ms." class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Academic Rank</label>
                            <input type="text" name="faculty[academic_rank]" value="{{ old('faculty.academic_rank', $fp->academic_rank ?? '') }}" placeholder="Assistant Professor" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contract Type</label>
                            <input type="text" name="faculty[contract_type]" value="{{ old('faculty.contract_type', $fp->contract_type ?? '') }}" placeholder="Regular / Visiting" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mobile Number</label>
                            <input type="text" name="faculty[mobile_number]" value="{{ old('faculty.mobile_number', $fp->mobile_number ?? $user->contact_number ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">City</label>
                            <input type="text" name="faculty[city]" value="{{ old('faculty.city', $fp->city ?? '') }}" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Joining Date</label>
                            <input type="text" name="faculty[joining_date]" value="{{ old('faculty.joining_date', $fp->joining_date ?? '') }}" placeholder="DD/MM/YY" class="w-full border-gray-300 rounded-xl shadow-sm">
                        </div>
                    </div>

                    <div class="border-2 border-blue-50 rounded-2xl p-6 space-y-6">
                        <h5 class="text-sm font-bold text-blue-800 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                            Highest Degree Details
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Degree Name</label>
                                <input type="text" name="faculty[highest_degree_name]" value="{{ old('faculty.highest_degree_name', $fp->highest_degree_name ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Field of Study</label>
                                <input type="text" name="faculty[field_of_study]" value="{{ old('faculty.field_of_study', $fp->field_of_study ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">University Name</label>
                                <input type="text" name="faculty[university_name]" value="{{ old('faculty.university_name', $fp->university_name ?? '') }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Status Messages for other roles -->
                <div id="noExtendedDetails" class="{{ in_array(old('role', $user->role), ['student', 'faculty']) ? 'hidden' : '' }} py-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">No additional profile fields required for this role.</p>
                </div>

                <!-- Action Bar -->
                <div class="pt-10 border-t border-gray-100 flex justify-between items-center">
                    <button type="button" onclick="window.history.back()" class="text-gray-500 hover:text-gray-700 font-semibold px-6 py-3 rounded-xl transition-all">
                        Discard Changes
                    </button>
                    <button type="submit" class="bg-cause-purple hover:bg-cause-purple-dark text-white font-black px-10 py-4 rounded-2xl shadow-xl shadow-purple-200 transition-all hover:-translate-y-1 active:translate-y-0 text-lg">
                        Update User Record
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('roleSelector').addEventListener('change', function() {
            const role = this.value;
            const studentSection = document.getElementById('studentDetailsSection');
            const facultySection = document.getElementById('facultyDetailsSection');
            const noneSection = document.getElementById('noExtendedDetails');

            // Reset visibility
            studentSection.classList.add('hidden');
            facultySection.classList.add('hidden');
            noneSection.classList.add('hidden');

            // Conditional display
            if (role === 'student') {
                studentSection.classList.remove('hidden');
            } else if (role === 'faculty') {
                facultySection.classList.remove('hidden');
            } else {
                noneSection.classList.remove('hidden');
            }
        });
    </script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection
