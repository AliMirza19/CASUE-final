<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AcademicTerm;
use App\Notifications\UserImportResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BulkUploadController extends Controller
{
    const DEFAULT_PASSWORD = 'Welcome@123';
    const BATCH_SIZE = 50;
    
    public function index()
    {
        return view('admin.bulk-upload');
    }
    
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120' // 5MB max
        ]);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Read file with proper encoding
        $content = file_get_contents($path);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $lines = explode("\n", $content);
        
        // Parse CSV
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $data[] = str_getcsv($line);
            }
        }
        
        if (count($data) < 2) {
            return redirect()->route('admin.bulk-upload')
                ->with('error', 'CSV file is empty or has no data rows.');
        }
        
        $header = array_shift($data); // Remove header row
        
        // Get active term
        $activeTerm = AcademicTerm::where('status', 'active')->first();
        $termId = $activeTerm ? $activeTerm->id : null;
        
        // Get existing reg_ids, emails, and cnics for duplicate check
        $existingRegIds = User::pluck('reg_id')->toArray();
        $existingEmails = User::pluck('email')->toArray();
        $existingCnics = User::pluck('cnic')->filter()->toArray(); // filter() removes nulls
        
        $results = [
            'total' => count($data),
            'students' => 0,
            'faculty' => 0,
            'skipped' => 0,
            'failed' => [],
            'duplicates' => [],
            'invalid_format' => [],
        ];
        
        $usersToInsert = [];
        $newRegIds = [];
        $newEmails = [];
        $newCnics = [];
        
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because of 0-index and header row
            
            // Check minimum columns (Name, Registration ID, Email)
            if (count($row) < 3) {
                $results['failed'][] = [
                    'row' => $rowNumber,
                    'reason' => 'Incomplete data - requires Name, Registration ID, Email'
                ];
                $results['skipped']++;
                continue;
            }
            
            $name = trim($row[0]);
            $regId = strtoupper(trim($row[1]));
            $email = strtolower(trim($row[2]));
            $cnic = isset($row[3]) ? trim($row[3]) : null;
            
            // Validate name
            if (empty($name)) {
                $results['failed'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => 'Name is empty'
                ];
                $results['skipped']++;
                continue;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results['failed'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => "Invalid email format: {$email}"
                ];
                $results['skipped']++;
                continue;
            }
            
            // Check for duplicate reg_id in database
            if (in_array($regId, $existingRegIds)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => 'Registration ID already exists in database'
                ];
                $results['skipped']++;
                continue;
            }
            
            // Check for duplicate reg_id in current batch
            if (in_array($regId, $newRegIds)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => 'Duplicate Registration ID in uploaded file'
                ];
                $results['skipped']++;
                continue;
            }
            
            // Check for duplicate email in database
            if (in_array($email, $existingEmails)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => "Email '{$email}' already exists in database"
                ];
                $results['skipped']++;
                continue;
            }
            
            // Check for duplicate email in current batch
            if (in_array($email, $newEmails)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => "Duplicate email '{$email}' in uploaded file"
                ];
                $results['skipped']++;
                continue;
            }

            // Check for duplicate CNIC in database (if CNIC is provided)
            if ($cnic && in_array($cnic, $existingCnics)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => "CNIC '{$cnic}' already exists in database"
                ];
                $results['skipped']++;
                continue;
            }

            // Check for duplicate CNIC in current batch
            if ($cnic && in_array($cnic, $newCnics)) {
                $results['duplicates'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => "Duplicate CNIC '{$cnic}' in uploaded file"
                ];
                $results['skipped']++;
                continue;
            }
            
            // Determine role based on Registration ID format
            $role = $this->determineRole($regId);
            
            if ($role === null) {
                $results['invalid_format'][] = [
                    'row' => $rowNumber,
                    'reg_id' => $regId,
                    'reason' => 'Invalid Registration ID format. Must be BSE + 6 digits (Student) or BFE + 6 digits (Faculty)'
                ];
                $results['skipped']++;
                continue;
            }
            
            // Add to batch
            $usersToInsert[] = [
                'name' => $name,
                'reg_id' => $regId,
                'email' => $email,
                'cnic' => $cnic,
                'contact_number' => $row[4] ?? null,
                'father_name' => $row[5] ?? null,
                'current_semester' => $row[6] ?? null,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => $role,
                'password_changed' => false,
                'current_term_id' => $termId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $newRegIds[] = $regId;
            $newEmails[] = $email;
            if ($cnic) {
                $newCnics[] = $cnic;
            }
            
            if ($role === 'student') {
                $results['students']++;
            } else {
                $results['faculty']++;
            }
        }
        
        // Batch insert users
        if (!empty($usersToInsert)) {
            try {
                $chunks = array_chunk($usersToInsert, self::BATCH_SIZE);
                
                DB::beginTransaction();
                
                foreach ($chunks as $chunk) {
                    User::insert($chunk);
                }
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                // Notify admin of complete failure
                Auth::user()->notify(new UserImportResult('bulk', 0, count($usersToInsert), 'bulk', null, [$e->getMessage()]));

                return redirect()->route('admin.bulk-upload')
                    ->with('error', 'Database error: ' . $e->getMessage());
            }
        }
        
        $results['success'] = $results['students'] + $results['faculty'];
        
        // Notify admin of bulk upload result
        $totalSuccess = $results['students'] + $results['faculty'];
        $totalFailed = $results['skipped'];
        Auth::user()->notify(new UserImportResult('bulk', $totalSuccess, $totalFailed, 'bulk'));
        
        return redirect()->route('admin.bulk-upload')
            ->with('results', $results);
    }
    
    /**
     * Determine user role based on Registration ID format.
     * BSE + 6 digits = Student
     * BFE + 6 digits = Faculty
     */
    private function determineRole(string $regId): ?string
    {
        // Must be exactly 9 characters
        if (strlen($regId) !== 9) {
            return null;
        }
        
        $prefix = substr($regId, 0, 3);
        $numbers = substr($regId, 3);
        
        // Check if remaining 6 characters are digits
        if (!ctype_digit($numbers)) {
            return null;
        }
        
        if ($prefix === 'BSE') {
            return 'student';
        }
        
        if ($prefix === 'BFE') {
            return 'faculty'; // Faculty role for BFE users
        }
        
        return null;
    }
    
    /**
     * Download sample CSV file.
     */
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sample_bulk_users.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['Name', 'Registration ID', 'Email', 'CNIC', 'Contact Number', 'Father Name', 'Current Semester']);
            
            // Exactly one sample record
            fputcsv($file, ['Ahmed Khan', 'BSE123456', 'ahmed.khan@student.edu.pk', '12345-1234567-1', '0300-1234567', 'Muhammad Khan', '6th']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
