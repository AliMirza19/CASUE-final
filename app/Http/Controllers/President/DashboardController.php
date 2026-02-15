<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventStatusUpdated;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active term instead of user's current_term_id
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Pending events for president review
        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_president')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Events approved by president (waiting for student to forward)
        $approvedEvents = Event::with('student')
            ->where('status', 'president_approved')
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Events sent for revision
        $revisionEvents = Event::with('student')
            ->where('status', 'revision_needed')
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('dashboards.president', compact(
            'pendingEvents',
            'approvedEvents',
            'revisionEvents'
        ));
    }
    
    public function review($id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        
        return view('president.review', compact('event'));
    }
    
    public function approve(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,revision,reject', // Added 'reject' to validation
            'comments' => 'nullable|string|max:1000'
        ]);
        
        $event = Event::findOrFail($id);
        
        if ($request->action === 'approve') {
            $event->status = 'pending_patron';
            $event->president_comments = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been approved by the President.",
                'success'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event approved and forwarded to Patron!');
        } elseif ($request->action === 'revision') {
            $event->status = 'revision_needed';
            $event->president_comments = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "The President has requested revisions for your event '{$event->title}'.",
                'warning'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event returned to student for revision.');
        } else { // This handles 'reject' action
            $event->status = 'rejected';
            $event->president_comments = $request->comments;
            $event->rejection_reason = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been rejected by the President.",
                'error'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event rejected.');
        }
    }
}
