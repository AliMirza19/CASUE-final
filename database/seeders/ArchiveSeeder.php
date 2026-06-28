<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\Event;
use App\Models\EventDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ArchiveSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure a student exists for event ownership
        $student = User::where('role', 'student')->first() ?? User::create([
            'name' => 'Archive Student',
            'email' => 'archive@cust.edu.pk',
            'password' => Hash::make('password'),
            'role' => 'student',
            'reg_id' => 'FA24-BSE-000',
        ]);

        // 2. Create Terms
        $terms = [
            ['term_name' => 'Spring 2025', 'term_code' => '251', 'status' => 'inactive', 'start_date' => '2025-02-01', 'end_date' => '2025-06-30'],
            ['term_name' => 'Fall 2025', 'term_code' => '253', 'status' => 'inactive', 'start_date' => '2025-09-01', 'end_date' => '2026-01-15'],
            ['term_name' => 'Spring 2026', 'term_code' => '261', 'status' => 'active', 'start_date' => '2026-02-01', 'end_date' => '2026-06-30'],
        ];

        foreach ($terms as $termData) {
            $term = AcademicTerm::updateOrCreate(['term_code' => $termData['term_code']], $termData);

            // Create ONE completed event for past terms
            if ($termData['term_name'] !== 'Spring 2026') {
                $eventName = $termData['term_name'] === 'Spring 2025' ? 'Spring Tech Fest' : 'Fall Coding Bootcamp';
                
                $event = Event::create([
                    'title' => $eventName,
                    'description' => "This was the major event for {$termData['term_name']}. It involved over 500 students and multiple industry partners.",
                    'student_id' => $student->id,
                    'term_id' => $term->id,
                    'expected_date' => $term->start_date->addMonths(2),
                    'venue' => 'Main Auditorium',
                    'grand_total' => 25000.00,
                    'status' => 'completed',
                ]);

                // Create Dummy Documents for EACH type
                $docTypes = [
                    'financial_report' => 'financial_report.pdf',
                    'approval_form' => 'final_approval.pdf',
                    'general_documentation' => 'event_summary.docx',
                    'poster_graphic' => 'event_poster.jpg',
                ];

                $termFolder = strtolower(str_replace(' ', '', $termData['term_name']));
                $eventFolder = strtolower(str_replace(' ', '', $eventName));

                foreach ($docTypes as $type => $filename) {
                    EventDocument::create([
                        'event_id' => $event->id,
                        'uploaded_by' => $student->id,
                        'doc_type' => $type,
                        'file_path' => "archives/{$termFolder}/{$eventFolder}/{$filename}",
                        'original_filename' => $filename,
                        'description' => "Archived " . str_replace('_', ' ', $type) . " for {$eventName}",
                        'visible_to_roles' => ["president", "hod", "patron", "faculty", "admin"],
                    ]);
                }
            }
        }
    }
}
