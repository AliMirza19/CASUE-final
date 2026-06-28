<?php

namespace App\Http\Controllers\Vc;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->overview();
    }

    public function overview()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        // Approved events with new user-linked volunteers
        $approvedEvents = Event::with(['student', 'assignedVolunteers.user'])
            ->where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();
        
        // Events with volunteers assigned
        $myAssignments = Volunteer::where('assigned_by', $user->id)
            ->get()
            ->groupBy('event_id');
        
        // Stats
        $totalApprovedEvents = $approvedEvents->count();
        $totalVolunteersAssigned = Volunteer::where('assigned_by', $user->id)->count();
        $eventsWithVolunteers = $myAssignments->count();
        
        // Upcoming events (next 30 days)
        $upcomingEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->where('expected_date', '>=', now())
            ->where('expected_date', '<=', now()->addDays(30))
            ->orderBy('expected_date', 'asc')
            ->get();
        
        // Volunteer Pool (All students who joined)
        $volunteerPool = \App\Models\User::where('role', 'student')
            ->where('is_volunteer_pool', true)
            ->get();
        
        return view('vc.overview', compact(
            'approvedEvents',
            'myAssignments',
            'totalApprovedEvents',
            'totalVolunteersAssigned',
            'eventsWithVolunteers',
            'upcomingEvents',
            'volunteerPool'
        ));
    }
    public function searchStudents(Request $request)
    {
        $query = $request->get('query');
        if (strlen($query) < 3) return response()->json([]);
        
        $students = \App\Models\User::where('role', 'student')
            ->where('is_volunteer_pool', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('reg_id', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'reg_id']);
            
        return response()->json($students);
    }

    public function assignVolunteer(Request $request, $eventId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_description' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'venue' => 'nullable|string|max:255'
        ]);
        
        $event = Event::findOrFail($eventId);
        
        // Prevent duplicate assignment
        $exists = Volunteer::where('event_id', $eventId)
            ->where('user_id', $request->user_id)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Student is already assigned to this event.');
        }
        
        Volunteer::create([
            'event_id' => $eventId,
            'user_id' => $request->user_id,
            'role_description' => $request->role_description,
            'instructions' => $request->instructions,
            'venue' => $request->venue,
            'assigned_by' => Auth::id(),
            'status' => 'assigned'
        ]);
        
        // Notify President
        $presidentAssignment = \App\Models\RoleAssignment::where('term_id', $event->term_id)
            ->where('role', 'president')
            ->where('is_active', true)
            ->first();
        $president = $presidentAssignment ? $presidentAssignment->user : \App\Models\User::where('role', 'president')->first();
        if ($president) {
            $user = \App\Models\User::find($request->user_id);
            $msg = "Volunteer {$user->name} has been assigned to '{$event->title}' for role: {$request->role_description}.";
            if ($request->venue) $msg .= " Venue: {$request->venue}.";
            if ($request->instructions) $msg .= " Instructions: {$request->instructions}.";
            
            $president->notify(new \App\Notifications\EventStatusUpdated(
                $event,
                $msg,
                'info'
            ));
        }
        
        return back()->with('success', 'Volunteer assigned successfully!');
    }

    public function removeVolunteer($eventId, $volunteerId)
    {
        Volunteer::where('event_id', $eventId)->where('id', $volunteerId)->delete();
        return back()->with('success', 'Volunteer removed successfully!');
    }

    public function suggestVolunteers($eventId)
    {
        // Simple mock AI suggestion matching based on volunteer pool
        $pool = \App\Models\User::where('role', 'student')
            ->where('is_volunteer_pool', true)
            ->limit(5)
            ->get();
            
        $suggestions = $pool->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'score' => rand(70, 98),
                'reason' => 'Strong match based on past experience and current semester.'
            ];
        })->sortByDesc('score')->values();
        
        return response()->json(['success' => true, 'data' => $suggestions]);
    }
}

