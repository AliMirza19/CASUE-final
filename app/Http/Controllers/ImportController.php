<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function showStudentImport()
    {
        return view('admin.import.students');
    }

    public function showFacultyImport()
    {
        return view('admin.import.faculty');
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $activeTerm = AcademicTerm::where('status', 'active')->first();
        if (!$activeTerm) {
            return back()->withErrors(['term' => 'No active academic term found. Please create one first.']);
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 24) continue;

                $data = [
                    'roll_no' => $row[0],
                    'name' => $row[1],
                    'father_name' => $row[2],
                    'gender' => $row[3],
                    'admission_date' => $row[4],
                    'nationality' => $row[5],
                    'cnic' => $row[6],
                    'passport' => $row[7],
                    'dob' => $row[8],
                    'phone' => $row[9],
                    'email' => $row[10],
                    'district' => $row[11],
                    'province' => $row[12],
                    'address' => $row[13],
                    'city' => $row[14],
                    'ssc_degree' => $row[15],
                    'ssc_board' => $row[16],
                    'ssc_total' => $row[17],
                    'ssc_obt' => $row[18],
                    'hssc_degree' => $row[19],
                    'hssc_nom' => $row[20],
                    'hssc_board' => $row[21],
                    'hssc_total' => $row[22],
                    'hssc_obt' => $row[23],
                ];

                $validator = Validator::make($data, [
                    'roll_no' => 'required|unique:student_profiles,roll_no',
                    'email' => 'required|email|unique:users,email',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row for {$data['roll_no']} failed: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $user = User::create([
                    'reg_id' => $data['roll_no'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'password_changed' => true,
                    'current_term_id' => $activeTerm->id,
                ]);

                StudentProfile::create([
                    'user_id' => $user->id,
                    'roll_no' => $data['roll_no'],
                    'father_name' => $data['father_name'],
                    'gender' => $data['gender'],
                    'admission_date' => $data['admission_date'],
                    'nationality' => $data['nationality'],
                    'cnic_number' => $data['cnic'],
                    'passport_number' => $data['passport'],
                    'dob' => $data['dob'],
                    'phone_number' => $data['phone'],
                    'domicile_district' => $data['district'],
                    'domicile_province' => $data['province'],
                    'mailing_address' => $data['address'],
                    'city' => $data['city'],
                    'ssc_degree_name' => $data['ssc_degree'],
                    'ssc_board_name' => $data['ssc_board'],
                    'ssc_total_marks' => (int)$data['ssc_total'],
                    'ssc_obtained_marks' => (int)$data['ssc_obt'],
                    'hssc_degree_name' => $data['hssc_degree'],
                    'hssc_degree_nomenclature' => $data['hssc_nom'],
                    'hssc_board_name' => $data['hssc_board'],
                    'hssc_total_marks' => (int)$data['hssc_total'],
                    'hssc_obtained_marks' => (int)$data['hssc_obt'],
                ]);

                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['import' => 'Critical error during import: ' . $e->getMessage()]);
        }

        fclose($handle);
        return back()->with('success', "Successfully imported {$imported} students.")->withErrors($errors);
    }

    public function importFaculty(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $activeTerm = AcademicTerm::where('status', 'active')->first();
        if (!$activeTerm) {
            return back()->withErrors(['term' => 'No active academic term found.']);
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 20) continue;

                $data = [
                    'title' => $row[1],
                    'name' => $row[2],
                    'gender' => $row[3],
                    'cnic_passport' => $row[4],
                    'dob' => $row[5],
                    'mobile' => $row[6],
                    'email' => $row[7],
                    'address' => $row[8],
                    'province' => $row[9],
                    'city' => $row[10],
                    'contract_type' => $row[11],
                    'academic_rank' => $row[12],
                    'joining_date' => $row[13],
                    'deg_name' => $row[14],
                    'deg_type' => $row[15],
                    'field' => $row[16],
                    'country' => $row[17],
                    'uni' => $row[18],
                    'start_date' => $row[19],
                    'end_date' => $row[20] ?? null,
                ];

                $validator = Validator::make($data, [
                    'email' => 'required|email|unique:users,email',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row for {$data['name']} failed: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                $user = User::create([
                    'reg_id' => 'FAC-' . rand(1000, 9999),
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'faculty',
                    'password_changed' => true,
                    'current_term_id' => $activeTerm->id,
                ]);

                FacultyProfile::create([
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'gender' => $data['gender'],
                    'cnic_passport' => $data['cnic_passport'],
                    'dob' => $data['dob'],
                    'mobile_number' => $data['mobile'],
                    'address' => $data['address'],
                    'province' => $data['province'],
                    'city' => $data['city'],
                    'contract_type' => $data['contract_type'],
                    'academic_rank' => $data['academic_rank'],
                    'joining_date' => $data['joining_date'],
                    'highest_degree_name' => $data['deg_name'],
                    'highest_degree_type' => $data['deg_type'],
                    'field_of_study' => $data['field'],
                    'degree_country' => $data['country'],
                    'university_name' => $data['uni'],
                    'degree_start_date' => $data['start_date'],
                    'degree_end_date' => $data['end_date'],
                ]);

                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['import' => 'Critical error: ' . $e->getMessage()]);
        }

        fclose($handle);
        return back()->with('success', "Successfully imported {$imported} faculty members.")->withErrors($errors);
    }
}
