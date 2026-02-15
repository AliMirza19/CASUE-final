<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\User;
use App\Models\Event;
use App\Models\Budget;
use App\Models\RoleAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected term or active term
        $selectedTermId = $request->get('term_id');
        $activeTerm = AcademicTerm::where('status', 'active')->first();
        
        if (!$selectedTermId && $activeTerm) {
            $selectedTermId = $activeTerm->id;
        }
        
        // Get all terms for dropdown
        $allTerms = AcademicTerm::orderBy('created_at', 'desc')->get();
        $selectedTerm = $selectedTermId ? AcademicTerm::find($selectedTermId) : null;
        
        // Stats
        $totalTerms = AcademicTerm::count();
        $totalUsers = User::count();
        
        // Term-specific stats
        $termEventsCount = 0;
        $termBudgetSpent = 0;
        $termPendingEvents = 0;
        $termApprovedEvents = 0;
        
        if ($selectedTermId) {
            $termEventsCount = Event::where('term_id', $selectedTermId)->count();
            $termBudgetSpent = Event::where('term_id', $selectedTermId)
                ->where('status', 'approved')
                ->sum('grand_total');
            $termPendingEvents = Event::where('term_id', $selectedTermId)
                ->where('status', 'like', 'pending%')
                ->count();
            $termApprovedEvents = Event::where('term_id', $selectedTermId)
                ->where('status', 'approved')
                ->count();
        }
        
        // Get current HOD assignment for active term
        $currentHodAssignment = null;
        $previousHodAssignment = null;
        $needsHodAssignment = false;
        
        if ($activeTerm) {
            $currentHodAssignment = RoleAssignment::getCurrentHod($activeTerm->id);
            $previousHodAssignment = RoleAssignment::getPreviousHod($activeTerm->id);
            $needsHodAssignment = !$currentHodAssignment;
        }
        
        // Get HOD user (fallback to old method)
        $hod = $currentHodAssignment ? $currentHodAssignment->user : User::where('role', 'hod')->first();
        
        // HOD assignment history
        $hodHistory = RoleAssignment::getHistory('hod', 5);
        
        // Check if active term is expired
        $activeTermExpired = false;
        if ($activeTerm && strtotime($activeTerm->end_date) < time()) {
            $activeTermExpired = true;
        }
        
        return view('dashboards.admin', compact(
            'allTerms',
            'selectedTerm',
            'selectedTermId',
            'activeTerm',
            'totalTerms',
            'totalUsers',
            'termEventsCount',
            'termBudgetSpent',
            'termPendingEvents',
            'termApprovedEvents',
            'hod',
            'activeTermExpired',
            'currentHodAssignment',
            'previousHodAssignment',
            'needsHodAssignment',
            'hodHistory'
        ));
    }
    
    /**
     * Show HOD management page.
     */
    public function manageHod()
    {
        $activeTerm = AcademicTerm::where('status', 'active')->first();
        
        if (!$activeTerm) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No active term found. Please activate a term first.');
        }
        
        $currentHodAssignment = RoleAssignment::getCurrentHod($activeTerm->id);
        $previousHodAssignment = RoleAssignment::getPreviousHod($activeTerm->id);
        $hodHistory = RoleAssignment::getHistory('hod', 10);
        
        // Get all users with HOD role for reference
        $hodUsers = User::where('role', 'hod')->get();
        
        return view('admin.manage-hod', compact(
            'activeTerm',
            'currentHodAssignment',
            'previousHodAssignment',
            'hodHistory',
            'hodUsers'
        ));
    }
    
    /**
     * Search users for HOD assignment.
     */
    public function searchUserForHod(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $users = User::where(function($q) use ($query) {
            $q->where('reg_id', 'like', "%{$query}%")
              ->orWhere('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'reg_id', 'name', 'email', 'role']);
        
        return response()->json($users);
    }
    
    /**
     * Continue with previous HOD.
     */
    public function continueHod(Request $request)
    {
        $activeTerm = AcademicTerm::where('status', 'active')->first();
        
        if (!$activeTerm) {
            return back()->with('error', 'No active term found.');
        }
        
        $previousHodAssignment = RoleAssignment::getPreviousHod($activeTerm->id);
        
        if (!$previousHodAssignment) {
            return back()->with('error', 'No previous HOD found to continue.');
        }
        
        // Assign the same user as HOD for new term
        RoleAssignment::assignHod(
            $previousHodAssignment->user_id,
            $activeTerm->id,
            Auth::id()
        );
        
        // Update user's current_term_id
        $previousHodAssignment->user->update(['current_term_id' => $activeTerm->id]);
        
        return redirect()->route('admin.manage-hod')
            ->with('success', 'HOD continued from previous term successfully!');
    }
    
    /**
     * Appoint new HOD.
     */
    public function appointHod(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $activeTerm = AcademicTerm::where('status', 'active')->first();
        
        if (!$activeTerm) {
            return back()->with('error', 'No active term found.');
        }
        
        $user = User::findOrFail($request->user_id);
        
        // Ensure user is faculty (no need to change role - it should already be faculty)
        if ($user->role !== 'faculty') {
            return back()->with('error', 'Only faculty members can be appointed as HOD.');
        }
        
        // Assign as HOD for this term
        RoleAssignment::assignHod(
            $user->id,
            $activeTerm->id,
            Auth::id()
        );
        
        // Update user's current_term_id
        $user->update(['current_term_id' => $activeTerm->id]);
        
        return redirect()->route('admin.manage-hod')
            ->with('success', "HOD appointed successfully! {$user->name} is now the HOD for {$activeTerm->term_name}.");
    }
}
