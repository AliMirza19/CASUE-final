@extends('layouts.dashboard')

@section('title', 'Bulk User Import - CAUSE Smart Society')
@section('page-title', 'Bulk User Import')
@section('page-description', 'Import multiple users from CSV file')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('admin.manage-hod') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Manage HOD
    </a>
    <a href="{{ route('admin.terms.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Manage Terms
    </a>
    <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Manage Users
    </a>
    <a href="{{ route('admin.bulk-upload') }}" class="sidebar-link active flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
        </svg>
        Bulk Import
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Results Section -->
    @if(session('results'))
    @php $results = session('results'); @endphp
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-green-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Import Complete
            </h3>
        </div>
        <div class="p-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $results['total'] }}</p>
                    <p class="text-sm text-gray-600">Total Records</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $results['success'] }}</p>
                    <p class="text-sm text-green-700">Successfully Imported</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ $results['students'] }}</p>
                    <p class="text-sm text-blue-700">Students (BSE)</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600">{{ $results['faculty'] }}</p>
                    <p class="text-sm text-purple-700">Faculty (BFE)</p>
                </div>
            </div>
            
            @if($results['skipped'] > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="font-semibold text-yellow-800 mb-2">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    {{ $results['skipped'] }} Records Skipped
                </p>
            </div>
            @endif
            
            <!-- Detailed Errors -->
            @if(!empty($results['duplicates']))
            <div class="mb-4">
                <h4 class="font-semibold text-red-700 mb-2">Duplicate Records ({{ count($results['duplicates']) }})</h4>
                <div class="bg-red-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    @foreach($results['duplicates'] as $error)
                    <p class="text-sm text-red-600 mb-1">
                        Row {{ $error['row'] }}: <span class="font-mono">{{ $error['reg_id'] }}</span> - {{ $error['reason'] }}
                    </p>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if(!empty($results['invalid_format']))
            <div class="mb-4">
                <h4 class="font-semibold text-orange-700 mb-2">Invalid Format ({{ count($results['invalid_format']) }})</h4>
                <div class="bg-orange-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    @foreach($results['invalid_format'] as $error)
                    <p class="text-sm text-orange-600 mb-1">
                        Row {{ $error['row'] }}: <span class="font-mono">{{ $error['reg_id'] }}</span> - {{ $error['reason'] }}
                    </p>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if(!empty($results['failed']))
            <div class="mb-4">
                <h4 class="font-semibold text-gray-700 mb-2">Other Errors ({{ count($results['failed']) }})</h4>
                <div class="bg-gray-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    @foreach($results['failed'] as $error)
                    <p class="text-sm text-gray-600 mb-1">
                        Row {{ $error['row'] }}: {{ $error['reason'] }}
                    </p>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Upload CSV File</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.bulk-upload.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                
                <!-- Drag & Drop Area -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-cause-purple transition cursor-pointer" 
                     id="drop-zone"
                     onclick="document.getElementById('csv_file').click()">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-700 mb-2">Drop your CSV file here</p>
                    <p class="text-gray-500 mb-4">or click to browse</p>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" class="hidden" required>
                    <p class="text-sm text-gray-400">Accepted format: .csv (Max 5MB)</p>
                </div>
                
                <!-- Selected File Display -->
                <div id="file-info" class="hidden mt-4 p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-green-800" id="file-name"></p>
                                <p class="text-sm text-green-600" id="file-size"></p>
                            </div>
                        </div>
                        <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                @error('csv_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <button type="submit" id="upload-btn" disabled
                        class="mt-6 w-full bg-cause-purple hover:bg-cause-purple-dark disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span id="btn-text">Import Users</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions & Sample -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- File Format Instructions -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-semibold text-blue-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    File Format Requirements
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Your CSV file must have the following columns in order:</p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-gray-700">Column</th>
                                <th class="text-left py-2 text-gray-700">Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="py-2 font-medium">Name</td>
                                <td class="py-2 text-gray-600">Ahmed Khan</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 font-medium">Registration ID</td>
                                <td class="py-2 font-mono text-gray-600">BSE123456</td>
                            </tr>
                            <tr>
                                <td class="py-2 font-medium">Email</td>
                                <td class="py-2 text-gray-600">ahmed@student.edu.pk</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="space-y-2 text-sm">
                    <p class="flex items-start">
                        <span class="w-2 h-2 bg-green-500 rounded-full mt-1.5 mr-2"></span>
                        <span><strong>BSE</strong> + 6 digits = Student role</span>
                    </p>
                    <p class="flex items-start">
                        <span class="w-2 h-2 bg-teal-500 rounded-full mt-1.5 mr-2"></span>
                        <span><strong>BFE</strong> + 6 digits = Faculty role</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Sample Download & Info -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                <h3 class="text-lg font-semibold text-green-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Sample File & Info
                </h3>
            </div>
            <div class="p-6">
                <a href="{{ route('admin.bulk-upload.sample') }}" 
                   class="flex items-center justify-center w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Sample CSV
                </a>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">Important Notes:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Default password: <code class="bg-yellow-100 px-1 rounded">Welcome@123</code></li>
                        <li>• Users must change password on first login</li>
                        <li>• Duplicate IDs/Emails will be skipped</li>
                        <li>• Invalid formats will be reported</li>
                        <li>• Max file size: 5MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Expected CSV Format Preview</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auto Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4">Ahmed Khan</td>
                        <td class="px-6 py-4 font-mono">BSE123456</td>
                        <td class="px-6 py-4">ahmed@student.edu.pk</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Student</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('csv_file');
const fileInfo = document.getElementById('file-info');
const fileName = document.getElementById('file-name');
const fileSize = document.getElementById('file-size');
const uploadBtn = document.getElementById('upload-btn');
const btnText = document.getElementById('btn-text');
const uploadForm = document.getElementById('upload-form');

// Drag and drop handlers
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-cause-purple', 'bg-purple-50');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-cause-purple', 'bg-purple-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-cause-purple', 'bg-purple-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0 && files[0].name.endsWith('.csv')) {
        fileInput.files = files;
        showFileInfo(files[0]);
    } else {
        alert('Please upload a CSV file only.');
    }
});

// File input change handler
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showFileInfo(e.target.files[0]);
    }
});

function showFileInfo(file) {
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    fileInfo.classList.remove('hidden');
    dropZone.classList.add('hidden');
    uploadBtn.disabled = false;
}

function clearFile() {
    fileInput.value = '';
    fileInfo.classList.add('hidden');
    dropZone.classList.remove('hidden');
    uploadBtn.disabled = true;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submit handler
uploadForm.addEventListener('submit', function(e) {
    uploadBtn.disabled = true;
    btnText.textContent = 'Importing...';
    uploadBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Importing...';
});
</script>
@endpush
