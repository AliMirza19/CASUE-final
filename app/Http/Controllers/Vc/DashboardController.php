<?php

namespace App\Http\Controllers\Vc;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventVolunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        // Approved events that need volunteers
        $approvedEvents = Event::with('student')
            ->where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();
        
        // Events with volunteers assigned by this VC
        $myAssignments = EventVolunteer::with('event')
            ->where('vc_id', $user->id)
            ->get()
            ->groupBy('event_id');
        
        // Stats
        $totalApprovedEvents = $approvedEvents->count();
        $totalVolunteersAssigned = EventVolunteer::where('vc_id', $user->id)->count();
        $eventsWithVolunteers = $myAssignments->count();
        
        // Upcoming events (next 30 days)
        $upcomingEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->where('expected_date', '>=', now())
            ->where('expected_date', '<=', now()->addDays(30))
            ->orderBy('expected_date', 'asc')
            ->get();
        
        return view('dashboards.vc', compact(
            'approvedEvents',
            'myAssignments',
            'totalApprovedEvents',
            'totalVolunteersAssigned',
            'eventsWithVolunteers',
            'upcomingEvents'
        ));
    }
    
    public function assignVolunteers($eventId)
    {
        $event = Event::with('volunteers')->findOrFail($eventId);
        
        return view('vc.assign-volunteers', compact('event'));
    }
    
    public function saveVolunteers(Request $request, $eventId)
    {
        $request->validate([
            'volunteers' => 'required|array|min:1',
            'volunteers.*.name' => 'required|string|max:255',
            'volunteers.*.contact' => 'nullable|string|max:50',
            'volunteers.*.role' => 'required|string|max:255'
        ]);
        
        $user = Auth::user();
        $event = Event::findOrFail($eventId);
        
        // Delete existing volunteers for this event by this VC
        EventVolunteer::where('event_id', $eventId)
            ->where('vc_id', $user->id)
            ->delete();
        
        // Add new volunteers
        foreach ($request->volunteers as $volunteer) {
            EventVolunteer::create([
                'event_id' => $eventId,
                'vc_id' => $user->id,
                'volunteer_name' => $volunteer['name'],
                'volunteer_contact' => $volunteer['contact'] ?? null,
                'role_description' => $volunteer['role']
            ]);
        }
        
        return redirect()->route('vc.dashboard')
            ->with('success', 'Volunteers assigned successfully!');
    }
}
