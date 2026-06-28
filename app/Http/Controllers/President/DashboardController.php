<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventStatusUpdated;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->overview();
    }

    public function overview()
    {
        $user = Auth::user();
        
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_president')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $approvedEvents = Event::with('student')
            ->whereIn('status', ['president_approved', 'pending_patron', 'pending_hod', 'approved', 'completed'])
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $revisionEvents = Event::with('student')
            ->where('status', 'revision_needed')
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $fullyApprovedEvents = Event::with('student')
            ->where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $assignedVolunteers = \App\Models\Volunteer::with(['user', 'event'])
            ->whereHas('event', function($q) use ($termId) {
                $q->where('term_id', $termId)->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(3)->get();
        
        return view('president.overview', compact(
            'pendingEvents',
            'approvedEvents',
            'revisionEvents',
            'fullyApprovedEvents',
            'assignedVolunteers',
            'announcements'
        ));
    }
    
    public function review($id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        
        if (!$event->risk_assessment || 
            ($event->risk_assessment['risk_level'] ?? '') === 'N/A' || 
            ($event->risk_assessment['risk_level'] ?? '') === 'Error' ||
            str_contains(($event->risk_assessment['suggestions'] ?? ''), 'Update .env')) {
            
            $riskData = app(\App\Services\AiRiskService::class)->assessEventRisk($event);
            if ($riskData['risk_level'] !== 'Unknown') {
                $event->risk_assessment = $riskData;
                $event->save();
            }
        }

        return view('president.review', compact('event'));
    }
    
    public function approve(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,revision,reject',
            'comments' => 'nullable|string|max:1000',
            'venue' => 'required_if:action,approve|string|max:255',
            'items' => 'required_if:action,approve|array',
            'items.*.id' => 'required|exists:event_items,id',
            'items.*.unit_rate' => 'required_if:action,approve|numeric|min:0',
            'items.*.total_amount' => 'required_if:action,approve|numeric|min:0'
        ]);
        
        $event = Event::findOrFail($id);
        
        if ($request->action === 'approve') {
            $grandTotal = 0;
            foreach ($request->items as $itemId => $itemData) {
                $item = \App\Models\EventItem::findOrFail($itemData['id']);
                $item->unit_rate = $itemData['unit_rate'];
                $item->total_amount = $item->quantity * $item->unit_rate;
                $item->save();
                $grandTotal += $item->total_amount;
            }

            $event->status = 'pending_patron';
            $event->president_comments = $request->comments;
            $event->venue = $request->venue;
            $event->grand_total = $grandTotal;
            $event->save();
            
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been approved by the President and assigned to venue: {$event->venue}.",
                'success'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event approved and forwarded to Patron!');
        } elseif ($request->action === 'revision') {
            $event->status = 'revision_needed';
            $event->president_comments = $request->comments;
            $event->save();
            
            $event->student->notify(new EventStatusUpdated(
                $event,
                "The President has requested revisions for your event '{$event->title}'.",
                'warning'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event returned to student for revision.');
        } else {
            $event->status = 'rejected';
            $event->president_comments = $request->comments;
            $event->rejection_reason = $request->comments;
            $event->save();
            
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been rejected by the President.",
                'error'
            ));
            
            return redirect()->route('president.dashboard')
                ->with('success', 'Event rejected.');
        }
    }

    public function manageTeams()
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        $currentTerm = AcademicTerm::find($termId);
        
        if (!$currentTerm) {
            return redirect()->route('president.dashboard')
                ->with('error', 'No active term found.');
        }
        
        $roles = [
            'gd' => 'Graphic Designer',
            'photo' => 'Photographer',
            'video' => 'Videographer',
            'doc' => 'Documentation Director',
            'deco' => 'Decoration Director',
            'smt' => 'Social Media Director',
            'sa' => 'General Secretary',
        ];
        
        $assignments = [];
        foreach ($roles as $roleKey => $roleName) {
            $assignments[$roleKey] = [
                'name' => $roleName,
                'current' => RoleAssignment::getCurrentRole($termId, $roleKey),
                'previous' => RoleAssignment::getPreviousRole($termId, $roleKey)
            ];
        }
        
        return view('president.manage-teams', compact('currentTerm', 'roles', 'assignments'));
    }
    
    public function searchStudent(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) return response()->json([]);
        
        $users = User::where('role', 'student')
            ->where(function($q) use ($query) {
                $q->where('reg_id', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'reg_id', 'name', 'email', 'role']);
            
        return response()->json($users);
    }
    
    public function continueTeamLead(Request $request)
    {
        if ($request->isMethod('get')) return redirect()->route('president.manage-teams');
        $request->validate(['role' => 'required|string']);
        
        $role = $request->role;
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $previousAssignment = RoleAssignment::getPreviousRole($termId, $role);
        if (!$previousAssignment) return back()->with('error', 'No previous assignment found to continue.');
        
        RoleAssignment::assignRole($previousAssignment->user_id, $termId, $role, Auth::id());
        return redirect()->route('president.manage-teams')->with('success', 'Role continued successfully!');
    }
    
    public function appointTeamLead(Request $request)
    {
        if ($request->isMethod('get')) return redirect()->route('president.manage-teams');
        $request->validate(['user_id' => 'required|exists:users,id', 'role' => 'required|string']);
        
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        RoleAssignment::assignRole($request->user_id, $termId, $request->role, Auth::id());
        return redirect()->route('president.manage-teams')->with('success', "Role appointed successfully!");
    }

    public function viewTasks()
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        $tasks = \App\Models\Task::with(['event', 'assignedTo'])
            ->whereHas('event', function($q) use ($termId) {
                $q->where('term_id', $termId);
            })
            ->orWhere('assigned_by_user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('president.tasks.index', compact('tasks'));
    }

    public function assignTasks()
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        $fullyApprovedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->get();

        $assignedVolunteers = \App\Models\Volunteer::with(['user', 'event'])
            ->whereHas('event', function($q) use ($termId) {
                $q->where('term_id', $termId)->where('status', 'approved');
            })
            ->get();

        return view('president.tasks.assign', compact('fullyApprovedEvents', 'assignedVolunteers'));
    }

    public function reviewEvents(Request $request)
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        
        // Use term_id from request or default to active term
        $termId = $request->get('term_id', ($activeTerm ? $activeTerm->id : $user->current_term_id));
        $allTerms = AcademicTerm::orderBy('term_code', 'desc')->get();
        $selectedTerm = AcademicTerm::find($termId) ?? ($activeTerm ?? $allTerms->first());

        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_president')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();

        $revisionEvents = Event::with('student')
            ->where('status', 'revision_needed')
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $approvedEvents = Event::with('student')
            ->whereIn('status', ['president_approved', 'pending_patron', 'pending_hod', 'approved', 'completed'])
            ->where('term_id', $termId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('president.events.review-list', compact('pendingEvents', 'revisionEvents', 'approvedEvents', 'allTerms', 'selectedTerm'));
    }

    public function events()
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        $events = Event::with('student')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('president.events.index', compact('events'));
    }
}
