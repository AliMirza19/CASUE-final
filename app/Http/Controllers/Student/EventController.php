<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\ActivityLog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $events = Event::where('student_id', $user->id)
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.events.index', compact('events'));
    }
    
    public function create()
    {
        $facultyMembers = User::where('role', 'faculty')->orderBy('name')->get();
        return view('student.events.create', compact('facultyMembers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expected_date' => 'required|date|after:today',
            'guest_speaker_name' => 'nullable|string|max:255',
            'guest_speaker_designation' => 'nullable|string|max:255',
            'faculty_mentor_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        
        // Get active term
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        // Prevent duplicate submissions within last 30 seconds
        $existing = Event::where('student_id', $user->id)
            ->where('title', $request->title)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();
            
        if ($existing) {
            return redirect()->route('student.events.index')
                ->with('warning', 'This event was already submitted a few moments ago.');
        }
        
        DB::beginTransaction();
        try {
            // Calculate grand total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['total_amount'] ?? 0;
            }
            
            // Create event
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'student_id' => $user->id,
                'created_by_role' => $user->role, // Track who created it
                'term_id' => $termId,  // Use active term
                'expected_date' => $request->expected_date,
                'grand_total' => $grandTotal,
                'guest_speaker_name' => $request->guest_speaker_name,
                'guest_speaker_designation' => $request->guest_speaker_designation,
                'faculty_mentor_id' => $request->faculty_mentor_id,
                'status' => Event::getInitialStatus($user->role) // Dynamic initial status
            ]);
            
            // Create event items
            foreach ($request->items as $item) {
                EventItem::create([
                    'event_id' => $event->id,
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'total_amount' => $item['total_amount'] ?? 0
                ]);
            }
            
            // Log activity
            ActivityLog::logActivity($user, "Submitted new event: {$event->title}");
            
            // Proactive AI Risk Assessment
            try {
                $aiRiskService = app(\App\Services\AiRiskService::class);
                $assessment = $aiRiskService->assessEventRisk($event);
                $event->update(['risk_assessment' => $assessment]);
            } catch (\Exception $e) {
                Log::warning('Initial AI Risk Assessment failed', ['error' => $e->getMessage()]);
            }
            
            // Send message to faculty mentor if selected
            if ($request->faculty_mentor_id) {
                Message::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $request->faculty_mentor_id,
                    'message_text' => "You have been selected as Faculty Mentor for the event: \"{$event->title}\". Event Date: {$event->expected_date->format('M d, Y')}.",
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('student.events.index')
                ->with('success', 'Event submitted successfully! Awaiting President review.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit event. Please try again.');
        }
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $event = Event::with('items')
            ->where('student_id', $user->id)
            ->findOrFail($id);
        
        return view('student.events.show', compact('event'));
    }
    
    public function edit($id)
    {
        $user = Auth::user();
        $event = Event::with('items')
            ->where('student_id', $user->id)
            ->whereIn('status', ['revision_needed', 'pending_president'])
            ->findOrFail($id);
        
        return view('student.events.edit', compact('event'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expected_date' => 'required|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        $event = Event::where('student_id', $user->id)
            ->whereIn('status', ['revision_needed', 'pending_president'])
            ->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Calculate grand total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['total_amount'] ?? 0;
            }
            
            // Update event
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'expected_date' => $request->expected_date,
                'grand_total' => $grandTotal,
                'status' => 'pending_president'
            ]);
            
            // Delete old items and create new ones
            $event->items()->delete();
            
            foreach ($request->items as $item) {
                EventItem::create([
                    'event_id' => $event->id,
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'total_amount' => $item['total_amount'] ?? 0
                ]);
            }
            
            // Log activity
            ActivityLog::logActivity($user, "Updated event: {$event->title}");
            
            DB::commit();
            
            return redirect()->route('student.events.index')
                ->with('success', 'Event updated and resubmitted!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update event. Please try again.');
        }
    }
    
    public function forwardToPatron($id)
    {
        $user = Auth::user();
        $event = Event::where('student_id', $user->id)
            ->where('status', 'president_approved')
            ->findOrFail($id);
        
        $event->status = 'pending_patron';
        $event->save();
        
        ActivityLog::logActivity($user, "Forwarded event to Patron: {$event->title}");
        
        return redirect()->route('student.events.index')
            ->with('success', 'Event forwarded to Patron for review!');
    }

    public function downloadApproval($id)
    {
        $user = Auth::user();
        $event = Event::with(['items', 'student', 'term'])
            ->where('student_id', $user->id)
            ->where('status', 'approved')
            ->findOrFail($id);
            
        // 1. Try to get HOD assigned to this event's term
        $hodAssignment = \App\Models\RoleAssignment::getCurrentHod($event->term_id);
        $hod = $hodAssignment ? $hodAssignment->user : null;
        
        // 2. Fallback to current active term HOD
        if (!$hod) {
            $activeTerm = \App\Models\AcademicTerm::getActive();
            if ($activeTerm) {
                $currentHodAssignment = \App\Models\RoleAssignment::getCurrentHod($activeTerm->id);
                $hod = $currentHodAssignment ? $currentHodAssignment->user : null;
            }
        }
        
        // 3. Last resort: Find anyone with HOD role
        if (!$hod) {
            $hod = User::where('role', 'hod')->first();
        }
        
        // 4. If still no HOD, check if there's any faculty who has a signature (maybe they were HOD)
        if (!$hod) {
            $hod = User::whereNotNull('digital_signature')->where('role', 'faculty')->first();
        }
        
        return view('student.events.pdf-approval', compact('event', 'hod'));
    }
}
