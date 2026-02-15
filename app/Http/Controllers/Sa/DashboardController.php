<?php

namespace App\Http\Controllers\Sa;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventStatusUpdated;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active term instead of user's current_term_id
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Get current term
        $currentTerm = AcademicTerm::find($termId);
        
        // Pending events for SA final approval
        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_sa')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Stats
        $totalPending = $pendingEvents->count();
        $totalApproved = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->count();
        $totalEvents = Event::where('term_id', $termId)->count();
        
        // Total budget approved
        $totalBudgetApproved = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->sum('grand_total');
        
        // Recent approved events
        $recentApproved = Event::with('student')
            ->where('term_id', $termId)
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // All events for overview
        $allEvents = Event::with('student')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboards.sa', compact(
            'currentTerm',
            'pendingEvents',
            'totalPending',
            'totalApproved',
            'totalEvents',
            'totalBudgetApproved',
            'recentApproved',
            'allEvents'
        ));
    }
    
    public function reviewEvent($id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        
        return view('sa.review-event', compact('event'));
    }
    
    public function approveEvent(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000'
        ]);
        
        $event = Event::findOrFail($id);
        
        if ($request->action === 'approve') {
            // Get budget for this term
            $budget = \App\Models\Budget::where('term_id', $event->term_id)->first();
            
            if (!$budget) {
                return back()->with('error', 'No budget found for this term. Please contact HOD to set up budget.');
            }
            
            // Check if sufficient budget available
            if ($budget->remaining_amount < $event->grand_total) {
                return back()->with('error', 'Insufficient budget! Required: ' . number_format($event->grand_total, 2) . ', Available: ' . number_format($budget->remaining_amount, 2));
            }
            
            // Deduct from budget
            $budget->remaining_amount -= $event->grand_total;
            $budget->save();
            
            // Approve event
            $event->status = 'approved';
            $event->sa_comments = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Congratulations! Your event '{$event->title}' has been fully approved.",
                'success'
            ));
            
            return redirect()->route('sa.dashboard')
                ->with('success', 'Event approved! Budget deducted: ' . number_format($event->grand_total, 2) . '. Remaining budget: ' . number_format($budget->remaining_amount, 2));
        } else {
            $event->status = 'rejected';
            $event->sa_comments = $request->comments;
            $event->rejection_reason = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been rejected by Student Affairs.",
                'error'
            ));
            
            return redirect()->route('sa.dashboard')
                ->with('success', 'Event rejected.');
        }
    }
}
