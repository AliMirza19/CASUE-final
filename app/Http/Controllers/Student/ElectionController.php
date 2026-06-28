<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\ElectionSetting;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElectionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $settings = $activeTerm ? $activeTerm->electionSetting : null;
        
        $hasRegistered = CandidateProfile::where('student_id', $user->id)->exists();
        $hasVoted = Vote::where('student_id', $user->id)
            ->where('term_id', $activeTerm ? $activeTerm->id : 0)
            ->exists();
        
        $approvedCandidates = CandidateProfile::with('student')
            ->where('status', 'approved')
            ->get();
        
        return view('student.election.index', compact(
            'settings',
            'hasRegistered',
            'hasVoted',
            'approvedCandidates'
        ));
    }
    
    public function register()
    {
        $user = Auth::user();
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $settings = $activeTerm ? $activeTerm->electionSetting : null;
        
        // Check if registration is open
        if (!$settings || !$settings->isRegistrationOpen()) {
            return redirect()->route('student.election')
                ->with('error', 'Candidate registration is currently closed.');
        }
        
        // Check if already registered
        $existingProfile = CandidateProfile::where('student_id', $user->id)->first();
        if ($existingProfile) {
            return redirect()->route('student.election')
                ->with('info', 'You have already registered as a candidate.');
        }
        
        return view('student.election.register');
    }
    
    public function submitRegistration(Request $request)
    {
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $settings = $activeTerm ? $activeTerm->electionSetting : null;

        if (!$settings || !$settings->isRegistrationOpen()) {
            return redirect()->route('student.election')
                ->with('error', 'Candidate registration is currently closed.');
        }

        $request->validate([
            'vp_name' => 'required|string|max:255',
            'vp_reg_id' => 'required|string|max:20',
            'manifesto' => 'required|string|max:2000',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        $user = Auth::user();
        
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }
        
        CandidateProfile::create([
            'student_id' => $user->id,
            'vp_name' => $request->vp_name,
            'vp_reg_id' => $request->vp_reg_id,
            'manifesto' => $request->manifesto,
            'photo_path' => $photoPath,
            'status' => 'pending_patron',
        ]);
        
        return redirect()->route('student.election')
            ->with('success', 'Your candidacy has been submitted for approval!');
    }
    
    public function vote()
    {
        $user = Auth::user();
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $settings = $activeTerm ? $activeTerm->electionSetting : null;
        
        // Check if voting is open
        if (!$settings || !$settings->isVotingActive()) {
            return redirect()->route('student.election')
                ->with('error', 'Voting is currently closed.');
        }
        
        // Check if already voted
        if (Vote::where('student_id', $user->id)->where('term_id', $activeTerm->id)->exists()) {
            return redirect()->route('student.election')
                ->with('info', 'You have already cast your vote.');
        }
        
        $candidates = CandidateProfile::with('student')
            ->where('status', 'approved')
            ->get();
        
        return view('student.election.vote', compact('candidates'));
    }
    
    public function castVote(Request $request)
    {
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $settings = $activeTerm ? $activeTerm->electionSetting : null;

        if (!$settings || !$settings->isVotingActive()) {
            return redirect()->route('student.election')
                ->with('error', 'Voting is currently closed.');
        }

        $request->validate([
            'candidate_id' => 'required|exists:candidate_profiles,id',
        ]);
        
        $user = Auth::user();
        
        // Check if already voted
        if (Vote::where('student_id', $user->id)->where('term_id', $activeTerm->id)->exists()) {
            return redirect()->route('student.election')
                ->with('error', 'You have already cast your vote.');
        }
        
        Vote::create([
            'student_id' => $user->id,
            'candidate_id' => $request->candidate_id,
            'term_id' => $activeTerm->id,
            'voted_at' => now(),
        ]);
        
        return redirect()->route('student.election')
            ->with('success', 'Your vote has been cast successfully!');
    }

    public function optimizeManifesto(Request $request)
    {
        $request->validate(['draft' => 'required|string|max:2000']);
        
        $aiService = app(\App\Services\AiGovernanceService::class);
        return response()->json($aiService->optimizeManifesto($request->draft));
    }
}
