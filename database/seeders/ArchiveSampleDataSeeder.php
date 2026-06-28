<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\AcademicTerm;
use App\Models\EventDocument;
use Illuminate\Support\Str;

class ArchiveSampleDataSeeder extends Seeder
{
    public function run()
    {
        $terms = AcademicTerm::all();
        
        $eventTypes = [
            'Coding Competition', 'Gaming Tournament', 'Tech Workshop', 
            'Guest Lecture', 'Hackathon', 'Seminar', 'Networking Night', 
            'Project Exhibition', 'Webinar', 'Robotics Showcase'
        ];

        foreach ($terms as $term) {
            // Create 2 events for each term
            for ($i = 1; $i <= 2; $i++) {
                $eventName = $eventTypes[array_rand($eventTypes)] . ' - ' . $term->term_name . ' (' . $i . ')';
                
                $event = Event::create([
                    'term_id' => $term->id,
                    'title' => $eventName,
                    'description' => 'A successful event held during ' . $term->term_name . ' focusing on community engagement and student learning.',
                    'expected_date' => now()->subYears(rand(0, 3))->subMonths(rand(1, 12)),
                    'status' => 'completed',
                    'grand_total' => rand(5000, 25000),
                    'venue' => 'Main Auditorium',
                    'student_id' => 1, // Admin
                ]);

                // Add sample documents
                $docs = [
                    ['type' => 'financial_report', 'name' => 'Financial_Summary.pdf'],
                    ['type' => 'approval_form', 'name' => 'Admin_Approval.pdf'],
                    ['type' => 'general_documentation', 'name' => 'Event_Report.docx'],
                    ['type' => 'poster_graphic', 'name' => 'Event_Poster.png'],
                ];

                foreach ($docs as $doc) {
                    EventDocument::create([
                        'event_id' => $event->id,
                        'doc_type' => $doc['type'],
                        'original_filename' => $doc['name'],
                        'file_path' => 'archives/sample/' . Str::random(10) . '_' . $doc['name'],
                        'visible_to_roles' => ['admin', 'hod', 'patron'],
                        'uploaded_by' => 1, // Admin
                    ]);
                }
            }
        }
    }
}
