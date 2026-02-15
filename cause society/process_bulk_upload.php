<?php
// Process Bulk Upload - CSV file process karke users register karna
// Roman Urdu comments ke saath
session_start();
require_once 'config/db.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied! Admin privileges required.";
    header("Location: index.php");
    exit();
}

// Check if file uploaded
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "File upload mein masla hua. Dobara try karein.";
    header("Location: admin_bulk_upload.php");
    exit();
}

$file = $_FILES['csv_file'];

// Validate file type
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($file_extension !== 'csv') {
    $_SESSION['error'] = "Sirf CSV files allowed hain!";
    header("Location: admin_bulk_upload.php");
    exit();
}

// Check file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['error'] = "File size 5MB se zyada nahi honi chahiye!";
    header("Location: admin_bulk_upload.php");
    exit();
}

// Skip header option
$skip_header = isset($_POST['skip_header']) && $_POST['skip_header'] == '1';

// Default password - Welcome123
$default_password = password_hash('Welcome123', PASSWORD_BCRYPT);

// Counters aur arrays for tracking
$students_created = 0;
$faculty_created = 0;
$skipped_duplicate = 0;
$skipped_invalid = 0;
$errors = [];
$success_records = [];

// Get current term ID
try {
    $stmt = $pdo->query("SELECT id FROM academic_terms WHERE status = 'active' LIMIT 1");
    $term = $stmt->fetch();
    $current_term_id = $term ? $term['id'] : 1;
} catch(PDOException $e) {
    $current_term_id = 1;
}

// Open and process CSV file
$handle = fopen($file['tmp_name'], 'r');

if ($handle === false) {
    $_SESSION['error'] = "File open nahi ho saki. Dobara try karein.";
    header("Location: admin_bulk_upload.php");
    exit();
}

$line_number = 0;
$batch_data = [];

// Prepare statements for better performance
$check_stmt = $pdo->prepare("SELECT id FROM users WHERE reg_id = ? OR email = ?");
$insert_stmt = $pdo->prepare("INSERT INTO users (reg_id, name, email, password, role, password_changed, current_term_id) 
                               VALUES (?, ?, ?, ?, ?, 0, ?)");

try {
    // Start transaction for batch insert
    $pdo->beginTransaction();
    
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        $line_number++;
        
        // Skip header row if option selected
        if ($skip_header && $line_number === 1) {
            continue;
        }
        
        // Check if row has enough columns
        if (count($data) < 3) {
            $errors[] = "Line {$line_number}: Incomplete data - kam se kam 3 columns chahiye (Reg ID, Name, Email)";
            $skipped_invalid++;
            continue;
        }
        
        // Extract and clean data
        $reg_id = trim($data[0]);
        $name = trim($data[1]);
        $email = trim($data[2]);
        
        // Skip empty rows
        if (empty($reg_id) || empty($name) || empty($email)) {
            $errors[] = "Line {$line_number}: Empty fields - Registration ID, Name aur Email required hain";
            $skipped_invalid++;
            continue;
        }
        
        // Validate Registration ID length (9 characters)
        if (strlen($reg_id) !== 9) {
            $errors[] = "Line {$line_number}: '{$reg_id}' - Registration ID ki length 9 characters honi chahiye (current: " . strlen($reg_id) . ")";
            $skipped_invalid++;
            continue;
        }
        
        // Determine role based on prefix
        $prefix = strtoupper(substr($reg_id, 0, 3));
        $role = null;
        
        if ($prefix === 'BSE') {
            $role = 'student';
        } elseif ($prefix === 'BFE') {
            $role = 'patron'; // Faculty as patron role
        } else {
            $errors[] = "Line {$line_number}: '{$reg_id}' - Invalid prefix. Sirf BSE (Student) ya BFE (Faculty) allowed hain";
            $skipped_invalid++;
            continue;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Line {$line_number}: '{$email}' - Invalid email format";
            $skipped_invalid++;
            continue;
        }
        
        // Check for duplicate in database
        $check_stmt->execute([$reg_id, $email]);
        if ($check_stmt->fetch()) {
            $errors[] = "Line {$line_number}: '{$reg_id}' ya '{$email}' - Already exists in database";
            $skipped_duplicate++;
            continue;
        }
        
        // Insert user
        try {
            $insert_stmt->execute([
                strtoupper($reg_id), // Uppercase reg_id
                $name,
                strtolower($email), // Lowercase email
                $default_password,
                $role,
                $current_term_id
            ]);
            
            if ($role === 'student') {
                $students_created++;
            } else {
                $faculty_created++;
            }
            
            $success_records[] = [
                'reg_id' => $reg_id,
                'name' => $name,
                'role' => $role
            ];
            
        } catch(PDOException $e) {
            $errors[] = "Line {$line_number}: Database error - " . $e->getMessage();
            $skipped_invalid++;
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Log activity
    $total_created = $students_created + $faculty_created;
    if ($total_created > 0) {
        $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, user_role, action_text) VALUES (?, 'admin', ?)");
        $log_stmt->execute([
            $_SESSION['user_id'],
            "Bulk upload: {$students_created} students aur {$faculty_created} faculty members register kiye"
        ]);
    }
    
} catch(PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: admin_bulk_upload.php");
    exit();
}

fclose($handle);

// Store results in session for display
$_SESSION['bulk_upload_results'] = [
    'students_created' => $students_created,
    'faculty_created' => $faculty_created,
    'skipped_duplicate' => $skipped_duplicate,
    'skipped_invalid' => $skipped_invalid,
    'errors' => $errors,
    'success_records' => $success_records,
    'total_processed' => $line_number - ($skip_header ? 1 : 0)
];

header("Location: bulk_upload_results.php");
exit();
?>
