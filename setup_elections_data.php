<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CandidateApplication;
use App\Models\CandidateProfile;
use App\Models\ElectionSetting;
use App\Models\AcademicTerm;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$activeTerm = AcademicTerm::getActive();
if (!$activeTerm) {
    echo "No active term found. Creating one...\n";
    $activeTerm = AcademicTerm::create([
        'name' => 'Spring 2026',
        'is_active' => true,
        'start_date' => now()->startOfYear(),
        'end_date' => now()->endOfYear(),
    ]);
}

// 1. Turn on Election
echo "Turning on Election Settings...\n";
ElectionSetting::updateOrCreate(
    ['term_id' => $activeTerm->id],
    [
        'registration_start' => now()->subDays(1),
        'registration_end' => now()->addDays(7),
        'voting_start' => now()->addDays(8),
        'voting_end' => now()->addDays(10),
        'is_active' => true,
    ]
);

// 2. Add Sample Candidates for Selection Workflow (Patron -> HOD)
echo "Adding sample candidates for Selection Workflow...\n";
$selectionCandidates = [
    [
        'id' => 32,
        'manifesto' => 'I aim to enhance the technical skills of students through workshops and hackathons. My vision is to bridge the gap between academia and industry.'
    ],
    [
        'id' => 33,
        'manifesto' => 'I believe in inclusivity and diversity. I will ensure every student has a voice in our society and we will host cultural exchange events.'
    ],
    [
        'id' => 53,
        'manifesto' => 'Innovation is key. I want to implement AI-driven event management and automate the mundane tasks to focus on creativity.'
    ],
    [
        'id' => 54,
        'manifesto' => 'Sustainability and ethics. I will promote green events and ensure our society operates with the highest moral standards.'
    ]
];

foreach ($selectionCandidates as $data) {
    CandidateApplication::updateOrCreate(
        ['student_id' => $data['id']],
        [
            'manifesto_text' => $data['manifesto'],
            'status' => 'pending'
        ]
    );
    echo "Added Selection Candidate: " . User::find($data['id'])->name . "\n";
}

// 3. Also add some for Voting Workflow (just in case)
echo "Adding sample candidates for Voting Workflow...\n";
foreach ($selectionCandidates as $data) {
    CandidateProfile::updateOrCreate(
        ['student_id' => $data['id']],
        [
            'manifesto' => $data['manifesto'],
            'vp_name' => 'John Doe VP',
            'status' => 'pending_patron'
        ]
    );
}

echo "Done!\n";
