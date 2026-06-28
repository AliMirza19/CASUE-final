<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FacultyDetail;
use App\Notifications\UserImportResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FacultyController extends Controller
{
    /**
     * Show the form for creating a new faculty member.
     */
    public function create()
    {
        return view('admin.faculty.create');
    }

    /**
     * Store a newly created faculty member in storage.
     */
    public function store(Request $request)
    {
        // Add sr_no to validation rules but also check for reg_id uniqueness
        $rules = $this->getValidationRules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate reg_id from sr_no for faculty
        $regId = 'FAC-' . $request->sr_no;

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('Faculty123'), // Default password
                'reg_id' => $regId,
                'cnic' => $request->cnic_passport,
                'contact_number' => $request->mobile_number,
                'role' => 'faculty',
            ]);

            FacultyDetail::create([
                'user_id' => $user->id,
                'sr_no' => $request->sr_no,
                'title' => $request->title,
                'gender' => $request->gender,
                'dob' => $this->parseDate($request->dob),
                'province' => $request->province,
                'city' => $request->city,
                'address' => $request->address,
                'contract_type' => $request->contract_type,
                'academic_rank' => $request->academic_rank,
                'joining_date' => $this->parseDate($request->joining_date),
                'leaving_date' => null,
                'degree_name' => $request->degree_name,
                'degree_type' => $request->degree_type,
                'field_of_study' => $request->field_of_study,
                'degree_awarding_country' => $request->degree_awarding_country,
                'university_name' => $request->university_name,
                'degree_start_date' => $this->parseDate($request->degree_start_date),
                'degree_end_date' => $this->parseDate($request->degree_end_date),
            ]);

            DB::commit();

            // Notify admin
            Auth::user()->notify(new UserImportResult('faculty', 1, 0, 'single', $request->name));

            return redirect()->route('admin.users.index')->with('success', 'Faculty member added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Notify admin of failure
            Auth::user()->notify(new UserImportResult('faculty', 0, 1, 'single', $request->name, [$e->getMessage()]));

            return back()->with('error', 'Error adding faculty: ' . $e->getMessage())->withInput();
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
            if (count($row) < 22) {
                $errorRows[] = "Row $rowNum: Incomplete data (expected 22 columns)";
                continue;
            }

            $row = array_map('trim', $row);

            $data = [
                'sr_no' => $row[0],
                'title' => $row[1],
                'name' => $row[2],
                'gender' => strtoupper($row[3]),
                'cnic_passport' => $row[4],
                'dob' => $row[5],
                'mobile_number' => $row[6],
                'email' => $row[7],
                'address' => $row[8],
                'province' => $row[9],
                'city' => $row[10],
                'contract_type' => $row[11],
                'academic_rank' => $row[12],
                'joining_date' => $row[13],
                'leaving_date' => $row[14],
                'degree_name' => $row[15],
                'degree_type' => $row[16],
                'field_of_study' => $row[17],
                'degree_awarding_country' => $row[18],
                'university_name' => $row[19],
                'degree_start_date' => $row[20],
                'degree_end_date' => $row[21],
            ];

            $validator = Validator::make($data, $this->getValidationRules());

            if ($validator->fails()) {
                $errorRows[] = "Row $rowNum: " . implode(', ', $validator->errors()->all());
                continue;
            }

            $regId = 'FAC-' . $data['sr_no'];

            if (User::where('email', $data['email'])->orWhere('reg_id', $regId)->exists()) {
                $errorRows[] = "Row $rowNum: Duplicate Email or Faculty ID";
                continue;
            }

            try {
                DB::beginTransaction();
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('Faculty123'),
                    'reg_id' => $regId,
                    'cnic' => $data['cnic_passport'],
                    'contact_number' => $data['mobile_number'],
                    'role' => 'faculty',
                ]);

                FacultyDetail::create([
                    'user_id' => $user->id,
                    'sr_no' => $data['sr_no'],
                    'title' => $data['title'],
                    'gender' => $data['gender'],
                    'dob' => $this->parseDate($data['dob']),
                    'province' => $data['province'],
                    'city' => $data['city'],
                    'address' => $data['address'],
                    'contract_type' => $data['contract_type'],
                    'academic_rank' => $data['academic_rank'],
                    'joining_date' => $this->parseDate($data['joining_date']),
                    'leaving_date' => null,
                    'degree_name' => $data['degree_name'],
                    'degree_type' => $data['degree_type'],
                    'field_of_study' => $data['field_of_study'],
                    'degree_awarding_country' => $data['degree_awarding_country'],
                    'university_name' => $data['university_name'],
                    'degree_start_date' => $this->parseDate($data['degree_start_date']),
                    'degree_end_date' => $this->parseDate($data['degree_end_date']),
                ]);
                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errorRows[] = "Row $rowNum: Error - " . $e->getMessage();
            }
        }
        fclose($handle);

        // Notify admin of bulk upload result
        Auth::user()->notify(new UserImportResult('faculty', $successCount, count($errorRows), 'bulk'));

        $message = "$successCount faculty members added successfully.";
        if (count($errorRows) > 0) {
            $message .= " " . count($errorRows) . " rows skipped.";
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
            "Content-Disposition" => "attachment; filename=faculty_template.csv",
        ];

        $columns = [
            'Sr.#', 'Title', 'Name', 'Gender', 'CNIC Number or Passport Number', 
            'DOB (DD/MM/YY)', 'Mobile Number', 'Email', 'Address', 'Province', 
            'City', 'Contract Type', 'Academic Rank', 'Joining Date (DD/MM/YY)', 
            'Leaving Date', 'Degree Name', 'Degree Type', 'Field of Study', 
            'Degree Awarding Country', 'University Name', 'Degree Start Date(DD/MM/YY)', 
            'Degree End date(DD/MM/YY)'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                '1', 'Dr.', 'Zahoor Alam', 'M', '35202-1234567-1', '15/05/85', 
                '03001234567', 'zahoor@cust.edu.pk', 'Street 5, Sector G-10', 'Punjab', 
                'Islamabad', 'Regular', 'Associate Professor', '10/09/20', '', 
                'PhD Computer Science', 'Doctorate', 'Artificial Intelligence', 'Pakistan', 
                'CUST', '01/09/15', '15/12/19'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getValidationRules()
    {
        return [
            'title' => 'required|string',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:M,F,Other',
            'cnic_passport' => 'required|string', // Generic since it can be CNIC or Passport
            'dob' => 'required|string|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
            'mobile_number' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'contract_type' => 'required|string',
            'academic_rank' => 'required|string',
            'joining_date' => 'required|string|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
            'degree_name' => 'required|string',
            'degree_type' => 'required|string',
            'field_of_study' => 'required|string',
            'degree_awarding_country' => 'required|string',
            'university_name' => 'required|string',
            'degree_start_date' => 'required|string|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
            'degree_end_date' => 'required|string|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
        ];
    }

    private function parseDate($dateStr)
    {
        if (!$dateStr) return null;
        
        $dateStr = trim($dateStr);
        
        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
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
