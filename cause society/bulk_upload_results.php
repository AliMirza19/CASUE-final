<?php
// Bulk Upload Results - Upload ke baad summary dikhana
// Roman Urdu comments ke saath
session_start();
require_once 'config/db.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if results exist
if (!isset($_SESSION['bulk_upload_results'])) {
    header("Location: admin_bulk_upload.php");
    exit();
}

$results = $_SESSION['bulk_upload_results'];
unset($_SESSION['bulk_upload_results']); // Clear after reading

$page_title = "Bulk Upload Results";
require_once 'includes/admin_header.php';

$total_created = $results['students_created'] + $results['faculty_created'];
$total_errors = $results['skipped_duplicate'] + $results['skipped_invalid'];
$success_rate = $results['total_processed'] > 0 ? round(($total_created / $results['total_processed']) * 100, 1) : 0;
?>

<!-- Success/Error Summary Banner -->
<?php if ($total_created > 0): ?>
<div class="bg-green-100 border border-green-400 rounded-lg p-6 mb-8">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-xl font-bold text-green-800">Bulk Upload Successful!</h3>
            <p class="text-green-700 mt-1">
                <strong><?php echo $results['students_created']; ?> Students</strong> aur 
                <strong><?php echo $results['faculty_created']; ?> Faculty members</strong> 
                kamyabi se register ho chuke hain.
            </p>
        </div>
    </div>
</div>
<?php else: ?>
<div class="bg-red-100 border border-red-400 rounded-lg p-6 mb-8">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-xl font-bold text-red-800">Upload Failed!</h3>
            <p class="text-red-700 mt-1">Koi bhi user register nahi ho saka. Neeche errors check karein.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Processed -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo $results['total_processed']; ?></p>
                <p class="text-gray-600">Total Rows Processed</p>
            </div>
        </div>
    </div>
    
    <!-- Students Created -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo $results['students_created']; ?></p>
                <p class="text-gray-600">Students Created</p>
            </div>
        </div>
    </div>
    
    <!-- Faculty Created -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo $results['faculty_created']; ?></p>
                <p class="text-gray-600">Faculty Created</p>
            </div>
        </div>
    </div>
    
    <!-- Errors/Skipped -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_errors; ?></p>
                <p class="text-gray-600">Skipped/Errors</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Rate Progress Bar -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex items-center justify-between mb-2">
        <h4 class="text-lg font-semibold text-gray-800">Success Rate</h4>
        <span class="text-2xl font-bold <?php echo $success_rate >= 80 ? 'text-green-600' : ($success_rate >= 50 ? 'text-yellow-600' : 'text-red-600'); ?>">
            <?php echo $success_rate; ?>%
        </span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-4">
        <div class="h-4 rounded-full transition-all duration-500 <?php echo $success_rate >= 80 ? 'bg-green-500' : ($success_rate >= 50 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
             style="width: <?php echo $success_rate; ?>%"></div>
    </div>
    <div class="flex justify-between mt-2 text-sm text-gray-600">
        <span><?php echo $total_created; ?> successful</span>
        <span><?php echo $total_errors; ?> failed</span>
    </div>
</div>

<!-- Successfully Created Users -->
<?php if (!empty($results['success_records'])): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
        <h4 class="text-lg font-semibold text-green-800">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Successfully Created Users (<?php echo count($results['success_records']); ?>)
        </h4>
    </div>
    
    <div class="overflow-x-auto max-h-96">
        <table class="w-full">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($results['success_records'] as $index => $record): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-600"><?php echo $index + 1; ?></td>
                        <td class="px-6 py-3 font-medium text-gray-800"><?php echo htmlspecialchars($record['reg_id']); ?></td>
                        <td class="px-6 py-3 text-gray-800"><?php echo htmlspecialchars($record['name']); ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                         <?php echo $record['role'] === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'; ?>">
                                <?php echo ucfirst($record['role']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Errors List -->
<?php if (!empty($results['errors'])): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
        <h4 class="text-lg font-semibold text-red-800">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Errors & Skipped Records (<?php echo count($results['errors']); ?>)
        </h4>
        <p class="text-red-600 text-sm mt-1">Ye records process nahi ho sake. Details neeche hain:</p>
    </div>
    
    <div class="p-6 max-h-96 overflow-y-auto">
        <div class="space-y-2">
            <?php foreach ($results['errors'] as $error): ?>
                <div class="flex items-start p-3 bg-red-50 rounded-lg border border-red-100">
                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Error Summary by Type -->
<?php if ($results['skipped_duplicate'] > 0 || $results['skipped_invalid'] > 0): ?>
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Error Summary</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if ($results['skipped_duplicate'] > 0): ?>
        <div class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <svg class="w-8 h-8 text-yellow-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <div>
                <p class="text-2xl font-bold text-yellow-800"><?php echo $results['skipped_duplicate']; ?></p>
                <p class="text-yellow-700">Duplicate Records (Already Exist)</p>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($results['skipped_invalid'] > 0): ?>
        <div class="flex items-center p-4 bg-red-50 rounded-lg border border-red-200">
            <svg class="w-8 h-8 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="text-2xl font-bold text-red-800"><?php echo $results['skipped_invalid']; ?></p>
                <p class="text-red-700">Invalid Records (Format Issues)</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Important Notice -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="text-lg font-semibold text-blue-800 mb-2">Important Information</h4>
            <ul class="text-blue-700 space-y-1 text-sm">
                <li>• Naye users ka default password: <strong>Welcome123</strong></li>
                <li>• Users ko first login par password change karna hoga</li>
                <li>• Students apna dashboard <code class="bg-blue-100 px-1 rounded">student_dashboard.php</code> par access kar sakte hain</li>
                <li>• Faculty members patron role ke saath register hue hain</li>
            </ul>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-between">
    <a href="admin_bulk_upload.php" class="bg-cause-purple hover:bg-cause-purple-dark text-white px-6 py-3 rounded-lg font-semibold transition">
        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
        </svg>
        Upload Another File
    </a>
    
    <div class="flex space-x-4">
        <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Report
        </button>
        
        <a href="admin_dashboard.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
            Back to Dashboard
        </a>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .print\\:hidden { display: none !important; }
    body { font-size: 12px; }
    .bg-white { background: white !important; }
    .shadow-md { box-shadow: none !important; }
    .rounded-lg { border-radius: 0 !important; }
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>
