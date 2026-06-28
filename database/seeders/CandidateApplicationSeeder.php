<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CandidateApplication;

class CandidateApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->take(2)->get();
        
        if ($students->count() >= 2) {
            CandidateApplication::create([
                'student_id' => $students[0]->id,
                'manifesto_text' => 'I promise to bring more hackathons and industrial tours to the society. Let\'s make CAUSE great again!',
                'status' => 'pending'
            ]);

            CandidateApplication::create([
                'student_id' => $students[1]->id,
                'manifesto_text' => 'I will focus on community building and soft skills development workshops. Unity is our strength.',
                'status' => 'pending'
            ]);
        }
    }
}
