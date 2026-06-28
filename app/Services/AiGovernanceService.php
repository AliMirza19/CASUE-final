<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGovernanceService extends BaseAiService
{
    public function optimizeManifesto(string $draft): array
    {
        $prompt = "You are the CAUSE AI Governance Assistant. A student is running for Society President. Optimize the following manifesto draft to make it more professional, inspiring, and structured. Keep it realistic and focused on student welfare and society growth.\n\nDraft:\n$draft";

        return $this->callAi($prompt);
    }

    public function rankVolunteers(array $students, string $eventTitle, string $eventDescription): array
    {
        $studentList = "";
        foreach ($students as $student) {
            $studentList .= "- ID: {$student['id']}, Name: {$student['name']}, Skills: {$student['skills']}, Exp: {$student['experience']}\n";
        }

        $prompt = "You are the CAUSE AI Coordinator. Rank the following students based on their suitability for the event: '$eventTitle'. \nEvent Description: $eventDescription\n\nStudents:\n$studentList\n\nReturn a JSON array of student IDs ranked from best to least fit, with a brief 'match_reason' for each.\n\nFormat: [{\"id\": 1, \"score\": 95, \"reason\": \"...\"}, ...]";

        return $this->callAi($prompt, true);
    }
}
