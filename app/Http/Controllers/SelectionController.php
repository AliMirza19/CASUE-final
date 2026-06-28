<?php

namespace App\Http\Controllers;

use App\Models\CandidateApplication;
use App\Models\SelectionCommittee;
use App\Models\CommitteeMember;
use App\Models\CommitteeMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SelectionController extends Controller
{
    /**
     * Step 1: Patron Shortlisting View
     */
    public function patronView()
    {
        $finalizedCandidate = CandidateApplication::where('status', 'finalized_president')
            ->with('student')->latest()->first();
        
        $selectionCommittee = null;
        if ($finalizedCandidate) {
            $selectionCommittee = SelectionCommittee::where('is_active', false)
                ->with('members.user')->latest()->first();
        }

        $activeTerm = \App\Models\AcademicTerm::getActive();
        $candidates = CandidateApplication::whereIn('status', ['pending', 'patron_shortlisted'])
            ->with('student')->get();

        return view('selection.patron-view', compact('candidates', 'finalizedCandidate', 'selectionCommittee', 'activeTerm'));
    }

    public function shortlist(CandidateApplication $candidate)
    {
        $candidate->update(['status' => 'patron_shortlisted']);
        return back()->with('success', 'Candidate shortlisted and forwarded to HOD.');
    }

    /**
     * Step 2: HOD Committee Formation View
     */
    public function hodView()
    {
        $finalizedCandidate = CandidateApplication::where('status', 'finalized_president')
            ->with('student')->latest()->first();
        
        $selectionCommittee = null;
        if ($finalizedCandidate) {
            $selectionCommittee = SelectionCommittee::where('is_active', false)
                ->with('members.user')->latest()->first();
        }

        $shortlistedCandidates = CandidateApplication::where('status', 'patron_shortlisted')
            ->with('student')->get();
        
        $facultyMembers = User::where('role', 'faculty')->get();
        
        $activeCommittee = SelectionCommittee::where('is_active', true)->first();
        $activeTerm = \App\Models\AcademicTerm::getActive();
        
        return view('selection.hod-view', compact('shortlistedCandidates', 'facultyMembers', 'activeCommittee', 'finalizedCandidate', 'selectionCommittee', 'activeTerm'));
    }

    public function formCommittee(Request $request)
    {
        $request->validate([
            'faculty_ids' => 'required|array|size:3',
            'faculty_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            // Deactivate any existing committee
            SelectionCommittee::where('is_active', true)->update(['is_active' => false]);

            $committee = SelectionCommittee::create([
                'hod_id' => Auth::id(),
                'patron_id' => \App\Models\RoleAssignment::getCurrentPatron(Auth::user()->current_term_id)->user_id ?? Auth::id(), // Fallback if no patron
                'is_active' => true,
            ]);

            // Add HOD
            CommitteeMember::create(['committee_id' => $committee->id, 'faculty_user_id' => $committee->hod_id]);
            // Add Patron
            CommitteeMember::create(['committee_id' => $committee->id, 'faculty_user_id' => $committee->patron_id]);
            
            // Add 3 Faculty
            foreach ($request->faculty_ids as $fid) {
                CommitteeMember::create(['committee_id' => $committee->id, 'faculty_user_id' => $fid]);
            }
        });

        return redirect()->route('selection.discussion')->with('success', 'Selection Committee formed successfully.');
    }

    /**
     * Step 3: Committee Discussion Room
     */
    public function discussionRoom()
    {
        $committee = SelectionCommittee::where('is_active', true)
            ->whereHas('members', function($q) {
                $q->where('faculty_user_id', Auth::id());
            })->with(['members.user', 'messages.sender', 'hod', 'patron'])->first();

        if (!$committee) {
            return redirect('/')->with('error', 'You are not an active member of any selection committee.');
        }

        $candidates = CandidateApplication::where('status', 'patron_shortlisted')
            ->with('student')->get();

        return view('selection.discussion-room', compact('committee', 'candidates'));
    }

    public function sendMessage(Request $request, SelectionCommittee $committee)
    {
        $request->validate(['message' => 'required|string']);

        // Check if user is member
        $isMember = CommitteeMember::where('committee_id', $committee->id)
            ->where('faculty_user_id', Auth::id())->exists();

        if (!$isMember) abort(403);

        CommitteeMessage::create([
            'committee_id' => $committee->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return back();
    }

    /**
     * Step 4: Final Decision (HOD Only)
     */
    public function finalizePresident(Request $request, SelectionCommittee $committee, CandidateApplication $candidate)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('selection.discussion');
        }

        if (Auth::id() !== $committee->hod_id) {
            abort(403, 'Only the HOD can make the final decision.');
        }

        DB::transaction(function () use ($committee, $candidate) {
            // Update Student Role
            $student = $candidate->student;
            $student->update(['role' => 'president']);

            // Update Application Status
            $candidate->update(['status' => 'finalized_president']);

            // Close Committee
            $committee->update(['is_active' => false]);
            
            // Record in RoleAssignment
            \App\Models\RoleAssignment::assignRole($student->id, Auth::user()->current_term_id, 'president', Auth::id());
        });

        return redirect('/')->with('success', "Congratulations! {$candidate->student->name} has been finalized as the new Society President.");
    }
}
