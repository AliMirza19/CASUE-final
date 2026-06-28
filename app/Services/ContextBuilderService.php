<?php

namespace App\Services;

use App\Models\AcademicTerm;
use App\Models\Event;
use App\Models\Budget;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ContextBuilderService
{
    /**
     * Builds real-time context for the Gemini Agent based on the user's role and system state.
     */
    public function buildAgentContext(string $role): string
    {
        $user = Auth::user();
        if (!$user) return '';

        $activeTerm = AcademicTerm::where('status', 'active')->first();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        $context = "\n\n--- REAL-TIME SYSTEM CONTEXT ---\n";
        $context .= "Current Date: " . now()->format('Y-m-d H:i:s') . "\n";
        $context .= "Active Term: " . ($activeTerm ? $activeTerm->term_name : 'None') . "\n";
        $context .= "Logged in User: {$user->name} (Reg ID: {$user->reg_id}, Role: {$role})\n";

        // Admin/HOD/Patron specific data
        if (in_array($role, ['admin', 'hod', 'patron'])) {
            $studentCount = \App\Models\StudentProfile::count();
            $facultyCount = \App\Models\FacultyProfile::count();
            $context .= "- Statistics: Total Students: {$studentCount}, Total Faculty: {$facultyCount}\n";
            
            $recentEvents = Event::latest()->take(5)->get();
            $context .= "- Recent Events:\n";
            foreach($recentEvents as $e) {
                $context .= "  * {$e->event_name} (Status: {$e->status}, Budget: {$e->total_budget})\n";
            }

            $budget = Budget::where('term_id', $termId)->first();
            if ($budget) {
                $context .= "- Financials: Total Budget: PKR {$budget->total_amount}, Remaining: PKR {$budget->remaining_amount}\n";
            }
        }
        
        // Student Assistant (SA) / Student specific data
        if ($role === 'sa' || $role === 'student') {
            $context .= "- Role Task: You can help draft event proposals, manifestos, and check student eligibility.\n";
            if ($user->studentProfile) {
                $context .= "- Student Profile Info: Roll No: {$user->studentProfile->roll_no}, Gender: {$user->studentProfile->gender}\n";
            }
        }

        // Graphic Designer (GD) specific data
        if ($role === 'gd') {
            $pendingGraphics = Event::where('status', 'approved_by_patron')->count();
            $context .= "- GD Task: There are {$pendingGraphics} events awaiting poster designs. Help the user by suggesting creative prompts.\n";
        }

        $context .= "----------------------------------\n";

        return $context;
    }
}
