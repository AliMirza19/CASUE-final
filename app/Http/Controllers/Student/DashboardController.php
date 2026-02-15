<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ElectionSetting;
use App\Models\Vote;
use App\Models\CandidateProfile;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Event stats
        $totalEvents = Event::where('student_id', $user->id)
            ->where('term_id', $termId)
            ->count();
            
        $approvedEvents = Event::where('student_id', $user->id)
            ->where('term_id', $termId)
            ->where('status', 'approved')
            ->count();
            
        $pendingEvents = Event::where('student_id', $user->id)
            ->where('term_id', $termId)
            ->where('status', 'like', 'pending%')
            ->count();
        
        // Election settings
        $electionSettings = ElectionSetting::where('term_id', $termId)->first();
        $votingEnabled = $electionSettings && $electionSettings->voting_enabled;
        $currentTime = now();
        $votingPeriodActive = false;
        
        if ($electionSettings) {
            $votingPeriodActive = ($currentTime >= $electionSettings->voting_start_date && 
                                  $currentTime <= $electionSettings->voting_end_date);
        }
        
        // Check if student has voted
        $hasVoted = Vote::where('student_id', $user->id)
            ->where('term_id', $termId)
            ->exists();
        
        // Candidate profile
        $candidateProfile = CandidateProfile::where('student_id', $user->id)->first();
        
        // Recent activities
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orWhere('user_role', 'admin')
            ->orWhere('user_role', 'sa')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Check if system is active (budget set)
        $systemActive = true; // Will be implemented with budget check
        
        return view('dashboards.student', compact(
            'totalEvents',
            'approvedEvents',
            'pendingEvents',
            'votingEnabled',
            'votingPeriodActive',
            'hasVoted',
            'electionSettings',
            'candidateProfile',
            'recentActivities',
            'systemActive'
        ));
    }
}
