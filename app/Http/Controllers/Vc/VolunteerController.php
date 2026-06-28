<?php

namespace App\Http\Controllers\Vc;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    /**
     * Search for students by Registration ID.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->input('query');

        $students = User::where('role', 'student')
            ->where(function($q) use ($query) {
                $q->where('reg_id', 'LIKE', "%{$query}%")
                  ->orWhere('name', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'reg_id')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    /**
     * Assign a student to an event.
     */
    public function assign(Request $request, $eventId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_description' => 'required|string|max:255',
        ]);

        $event = Event::findOrFail($eventId);

        // Ensure the event is approved
        if (!$event->isApproved()) {
            return back()->with('error', 'You can only assign volunteers to approved events.');
        }

        $userId = $request->input('user_id');

        // Check if student is already assigned
        $exists = Volunteer::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This student is already assigned to this event.');
        }

        Volunteer::create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'status' => 'assigned',
            'role_description' => $request->input('role_description'),
            'assigned_by' => Auth::id(),
        ]);

        return back()->with('success', 'Volunteer assigned successfully!');
    }

    /**
     * Remove an assigned volunteer from an event.
     */
    public function remove(Request $request, $eventId, $volunteerId)
    {
        $volunteer = Volunteer::where('event_id', $eventId)
            ->findOrFail($volunteerId);

        $volunteer->delete();

        return back()->with('success', 'Volunteer removed successfully!');
    }

    public function suggest(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $students = User::where('role', 'student')
            ->select('id', 'name', 'skills', 'experience')
            ->get()
            ->toArray();

        $aiService = app(\App\Services\AiGovernanceService::class);
        $result = $aiService->rankVolunteers($students, $event->title, $event->description);

        return response()->json($result);
    }
}
