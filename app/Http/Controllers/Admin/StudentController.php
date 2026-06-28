<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDetail;
use App\Notifications\UserImportResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $rules = $this->getValidationRules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->student_name,
                'email' => $request->email,
                'password' => Hash::make('Student123'), // Default password
                'reg_id' => $request->roll_no,
                'cnic' => $request->cnic_number,
                'father_name' => $request->father_name,
                'contact_number' => $request->phone_number,
                'role' => 'student',
            ]);

            StudentDetail::create([
                'user_id' => $user->id,
                'gender' => $request->gender,
                'admission_date' => $this->parseDate($request->admission_date),
                'nationality' => $request->nationality,
                'passport_number' => $request->passport_number,
                'dob' => $this->parseDate($request->date_of_birth),
                'domicile_district' => $request->domicile_district,
                'domicile_province' => $request->domicile_province,
                'mailing_address' => $request->mailing_address,
                'city' => $request->city,
                'ssc_degree_name' => $request->ssc_degree_name,
                'ssc_board_name' => $request->ssc_board_name,
                'ssc_total_marks' => $request->ssc_total_marks,
                'ssc_obtained_marks' => $request->ssc_obtained_marks,
                'hssc_degree_name' => $request->hssc_degree_name,
                'hssc_nomenclature' => $request->hssc_nomenclature,
                'hssc_board_name' => $request->hssc_board_name,
                'hssc_total_marks' => $request->hssc_total_marks,
                'hssc_obtained_marks' => $request->hssc_obtained_marks,
            ]);

            DB::commit();

            // Notify admin
            Auth::user()->notify(new UserImportResult('student', 1, 0, 'single', $request->student_name));

            return redirect()->route('admin.users.index')->with('success', 'Student added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Notify admin of failure
            Auth::user()->notify(new UserImportResult('student', 0, 1, 'single', $request->student_name, [$e->getMessage()]));

            return back()->with('error', 'Error adding student: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle bulk upload via CSV.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('bulk_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Skip header

        $successCount = 0;
        $errorRows = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== FALSE) {
            $rowNum++;
            if (count($row) < 24) {
                $errorRows[] = "Row $rowNum: Incomplete data (expected 24 columns)";
                continue;
            }

            // Trim all fields
            $row = array_map('trim', $row);

            // Map row to named array for validation, parsing dates first
            $data = [
                'student_name' => $row[0],
                'father_name' => $row[1],
                'gender' => strtoupper($row[2]),
                'roll_no' => $row[3],
                'admission_date' => $this->parseDate($row[4]),
                'nationality' => $row[5],
                'cnic_number' => $row[6],
                'passport_number' => $row[7],
                'date_of_birth' => $this->parseDate($row[8]),
                'phone_number' => $row[9],
                'email' => $row[10],
                'domicile_district' => $row[11],
                'domicile_province' => $row[12],
                'mailing_address' => $row[13],
                'city' => $row[14],
                'ssc_degree_name' => $row[15],
                'ssc_board_name' => $row[16],
                'ssc_total_marks' => $row[17],
                'ssc_obtained_marks' => $row[18],
                'hssc_degree_name' => $row[19],
                'hssc_nomenclature' => $row[20],
                'hssc_board_name' => $row[21],
                'hssc_total_marks' => $row[22],
                'hssc_obtained_marks' => $row[23],
            ];

            $validator = Validator::make($data, $this->getValidationRules());

            if ($validator->fails()) {
                $errorRows[] = "Row $rowNum: " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Check for duplicate Email or Roll No
            if (User::where('email', $data['email'])->orWhere('reg_id', $data['roll_no'])->exists()) {
                $errorRows[] = "Row $rowNum: Duplicate Email or Roll No";
                continue;
            }

            try {
                DB::beginTransaction();
                $user = User::create([
                    'name' => $data['student_name'],
                    'email' => $data['email'],
                    'password' => Hash::make('Student123'),
                    'reg_id' => $data['roll_no'],
                    'cnic' => $data['cnic_number'],
                    'father_name' => $data['father_name'],
                    'contact_number' => $data['phone_number'],
                    'role' => 'student',
                ]);

                StudentDetail::create([
                    'user_id' => $user->id,
                    'gender' => $data['gender'],
                    'admission_date' => $data['admission_date'],
                    'nationality' => $data['nationality'],
                    'passport_number' => $data['passport_number'],
                    'dob' => $data['date_of_birth'],
                    'domicile_district' => $data['domicile_district'],
                    'domicile_province' => $data['domicile_province'],
                    'mailing_address' => $data['mailing_address'],
                    'city' => $data['city'],
                    'ssc_degree_name' => $data['ssc_degree_name'],
                    'ssc_board_name' => $data['ssc_board_name'],
                    'ssc_total_marks' => $data['ssc_total_marks'],
                    'ssc_obtained_marks' => $data['ssc_obtained_marks'],
                    'hssc_degree_name' => $data['hssc_degree_name'],
                    'hssc_nomenclature' => $data['hssc_nomenclature'],
                    'hssc_board_name' => $data['hssc_board_name'],
                    'hssc_total_marks' => $data['hssc_total_marks'],
                    'hssc_obtained_marks' => $data['hssc_obtained_marks'],
                ]);
                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errorRows[] = "Row $rowNum: " . $e->getMessage();
            }
        }
        fclose($handle);

        // Notify admin of bulk upload result
        Auth::user()->notify(new UserImportResult('student', $successCount, count($errorRows), 'bulk'));

        $message = "$successCount students added successfully.";
        if (count($errorRows) > 0) {
            $message .= " " . count($errorRows) . " rows skipped due to errors.";
            return back()->with('success', $message)->with('bulk_errors', $errorRows);
        }

        return back()->with('success', $message);
    }

    /**
     * Download sample CSV.
     */
    public function downloadSample()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=student_template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Student Name', 'Father Name', 'Gender (M/F)', 'Roll No', 'Admission Date (YYYY-MM-DD)', 
            'Nationality', 'CNIC Number (12345-1234567-1)', 'Passport Number (Optional)', 
            'Date of Birth (YYYY-MM-DD)', 'Phone Number', 'Email', 'Domicile District', 
            'Domicile Province', 'Mailing Address', 'City', 'SSC Degree Name', 'SSC Board Name', 
            'SSC Total Marks', 'SSC Obtained Marks', 'HSSC Degree Name', 
            'HSSC Nomenclature (1=Math, 2=A-Math, 3=Pre-Med)', 'HSSC Board Name', 
            'HSSC Total Marks', 'HSSC Obtained Marks'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Sample data row
            fputcsv($file, [
                'Ali Khan', 'Ahmed Khan', 'M', 'BCS-F25-001', '2025-09-01', 'Pakistani', 
                '35202-1234567-1', '', '2005-05-15', '03001234567', 'ali@example.com', 
                'Lahore', 'Punjab', 'Street 1, Model Town', 'Lahore', 'Matric', 'BISE Lahore', 
                '1100', '950', 'FSc Pre-Engineering', '1', 'BISE Lahore', '1100', '980'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getValidationRules()
    {
        return [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'gender' => 'required|in:M,F,m,f',
            'roll_no' => 'required|string|unique:users,reg_id',
            'admission_date' => 'required|date',
            'nationality' => 'required|string',
            'cnic_number' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'passport_number' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'phone_number' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'domicile_district' => 'required|string',
            'domicile_province' => 'required|string',
            'mailing_address' => 'required|string',
            'city' => 'required|string',
            'ssc_degree_name' => 'required|string',
            'ssc_board_name' => 'required|string',
            'ssc_total_marks' => 'required|numeric',
            'ssc_obtained_marks' => 'required|numeric|lte:ssc_total_marks',
            'hssc_degree_name' => 'required|string',
            'hssc_nomenclature' => 'required|in:1,2,3',
            'hssc_board_name' => 'required|string',
            'hssc_total_marks' => 'required|numeric',
            'hssc_obtained_marks' => 'required|numeric|lte:hssc_total_marks',
        ];
    }

    /**
     * Flexible date parser that handles multiple formats cleanly.
     */
    private function parseDate($dateStr)
    {
        if (!$dateStr) return null;
        
        $dateStr = trim($dateStr);
        
        try {
            // Carbon::parse handles common English date formats (e.g. 9/5/2025, 5/15/2005) automatically
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            // Fallbacks for specific standard local formats
            $formats = ['d/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y', 'd/m/y', 'm/d/y', 'd-m-y', 'm-d-y'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $dateStr)->format('Y-m-d');
                } catch (\Exception $ex) {
                    continue;
                }
            }
        }
        
        return null;
    }
}
