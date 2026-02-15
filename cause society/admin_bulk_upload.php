<?php
// Admin Bulk Upload - CSV se users register karna
// Roman Urdu comments ke saath
session_start();
require_once 'config/db.php';

$page_title = "Bulk Upload Users";
require_once 'includes/admin_header.php';
?>

<!-- Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-800">Bulk User Registration</h3>
    <p class="text-gray-600 mt-1">CSV file upload karke multiple users ek saath register karein</p>
</div>

<!-- Instructions Card -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <h4 class="text-lg font-semibold text-blue-800 mb-2">CSV File Instructions</h4>
            <ul class="text-blue-700 space-y-2 text-sm">
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Excel file ko <strong>"CSV (MS-DOS)"</strong> format mein save karke upload karein</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>CSV file mein <strong>3 columns</strong> honi chahiye: <code class="bg-blue-100 px-1 rounded">Registration ID, Name, Email</code></span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Registration ID ki length <strong>lazmi 9 characters</strong> honi chahiye</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span><strong>BSE</strong> se shuru hone wali IDs = Student role</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span><strong>BFE</strong> se shuru hone wali IDs = Faculty/Patron role</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Default password: <strong>Welcome123</strong> (users ko first login par change karna hoga)</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Sample CSV Format -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Sample CSV Format</h4>
    <div class="bg-gray-50 rounded-lg p-4 font-mono text-sm overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-600 border-b border-gray-300">
                    <th class="pb-2 pr-8">Registration ID</th>
                    <th class="pb-2 pr-8">Name</th>
                    <th class="pb-2">Email</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <tr class="border-b border-gray-200">
                    <td class="py-2 pr-8">BSE123456</td>
                    <td class="py-2 pr-8">Ali Hassan</td>
                    <td class="py-2">ali.hassan@cause.edu.pk</td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="py-2 pr-8">BSE234567</td>
                    <td class="py-2 pr-8">Sara Ahmed</td>
                    <td class="py-2">sara.ahmed@cause.edu.pk</td>
                </tr>
                <tr>
                    <td class="py-2 pr-8">BFE345678</td>
                    <td class="py-2 pr-8">Dr. Muhammad Khan</td>
                    <td class="py-2">m.khan@cause.edu.pk</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Download Sample Button -->
    <div class="mt-4">
        <a href="download_sample_csv.php" class="inline-flex items-center text-cause-purple hover:text-cause-purple-dark font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Sample CSV Template
        </a>
    </div>
</div>

<!-- Upload Form -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Upload CSV File</h4>
    
    <form action="process_bulk_upload.php" method="POST" enctype="multipart/form-data" id="uploadForm">
        <!-- File Input -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Select CSV File</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-cause-purple transition" id="dropZone">
                <input type="file" name="csv_file" id="csvFile" accept=".csv" class="hidden" required>
                <div id="uploadPlaceholder">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-gray-600 mb-2">CSV file yahan drag karein ya</p>
                    <button type="button" onclick="document.getElementById('csvFile').click()" 
                            class="bg-cause-purple hover:bg-cause-purple-dark text-white px-4 py-2 rounded-lg font-medium transition">
                        Browse Files
                    </button>
                    <p class="text-gray-500 text-sm mt-2">Sirf .csv files allowed hain</p>
                </div>
                <div id="fileSelected" class="hidden">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-800 font-medium" id="fileName"></p>
                    <p class="text-gray-500 text-sm" id="fileSize"></p>
                    <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700 text-sm mt-2">
                        Remove File
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Options -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <label class="flex items-center">
                <input type="checkbox" name="skip_header" value="1" checked 
                       class="w-4 h-4 text-cause-purple border-gray-300 rounded focus:ring-cause-purple">
                <span class="ml-2 text-gray-700">First row ko header samajh kar skip karein</span>
            </label>
        </div>
        
        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <button type="submit" id="submitBtn" disabled
                    class="bg-cause-purple hover:bg-cause-purple-dark text-white px-6 py-3 rounded-lg font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Upload & Process
            </button>
            
            <a href="admin_dashboard.php" class="text-gray-600 hover:text-gray-800 font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Recent Uploads History -->
<?php
try {
    // Recent bulk uploads fetch karo (activity logs se)
    $stmt = $pdo->prepare("SELECT action_text, created_at FROM activity_logs 
                           WHERE user_role = 'admin' AND action_text LIKE '%bulk%'
                           ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_uploads = $stmt->fetchAll();
} catch(PDOException $e) {
    $recent_uploads = [];
}

if (!empty($recent_uploads)):
?>
<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Bulk Uploads</h4>
    <div class="space-y-3">
        <?php foreach ($recent_uploads as $upload): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-700"><?php echo htmlspecialchars($upload['action_text']); ?></span>
                </div>
                <span class="text-gray-500 text-sm"><?php echo date('M d, Y g:i A', strtotime($upload['created_at'])); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- JavaScript for file handling -->
<script>
const dropZone = document.getElementById('dropZone');
const csvFile = document.getElementById('csvFile');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const fileSelected = document.getElementById('fileSelected');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const submitBtn = document.getElementById('submitBtn');

// File input change handler
csvFile.addEventListener('change', function(e) {
    handleFile(e.target.files[0]);
});

// Drag and drop handlers
dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropZone.classList.add('border-cause-purple', 'bg-purple-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-cause-purple', 'bg-purple-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.classList.remove('border-cause-purple', 'bg-purple-50');
    
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.csv')) {
        csvFile.files = e.dataTransfer.files;
        handleFile(file);
    } else {
        alert('Sirf CSV files allowed hain!');
    }
});

function handleFile(file) {
    if (file) {
        uploadPlaceholder.classList.add('hidden');
        fileSelected.classList.remove('hidden');
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        submitBtn.disabled = false;
    }
}

function clearFile() {
    csvFile.value = '';
    uploadPlaceholder.classList.remove('hidden');
    fileSelected.classList.add('hidden');
    submitBtn.disabled = true;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submit loading state
document.getElementById('uploadForm').addEventListener('submit', function() {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
