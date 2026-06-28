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
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.student', compact('announcements'));
    }

    public function overview()
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
        
        return view('student.overview', compact(
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

    public function profile()
    
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'cnic' => 'nullable|string|max:15',
            'contact_number' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'current_semester' => 'nullable|string|max:20',
            'skills' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|max:1000',
        ]);

        $user->update($request->all());

        return back()->with('success', 'Profile updated successfully!');
    }

    public function faq()
    {
        return view('student.faq');
    }

    public function joinVolunteerPool(Request $request)
    {
        $user = Auth::user();
        
        if ($user->is_volunteer_pool) {
            $user->update(['is_volunteer_pool' => false]);
            return back()->with('success', 'You have left the Volunteer Pool.');
        } else {
            $user->update(['is_volunteer_pool' => true]);
            
            // Notify Student
            $user->notify(new \App\Notifications\VolunteerPoolNotification(
                $user,
                "You have successfully joined the Volunteer Pool! Your profile is now visible to the Volunteer Coordinator.",
                'success'
            ));
            
            // Find and Notify Volunteer Coordinator(s)
            $vcs = \App\Models\User::where('role', 'vc')->get();
            foreach ($vcs as $vc) {
                $vc->notify(new \App\Notifications\VolunteerPoolNotification(
                    $user,
                    "Student {$user->name} ({$user->reg_id}) has joined the Volunteer Pool and is ready for selection.",
                    'info'
                ));
            }
            
            return back()->with('success', 'You have successfully joined the Volunteer Pool! The Volunteer Coordinator has been notified.');
        }
    }
}
