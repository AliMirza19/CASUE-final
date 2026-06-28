<?php

namespace App\Http\Controllers\Patron;

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
        
        // Get events created by patron
        $events = Event::where('student_id', $user->id)
            ->where('created_by_role', 'patron')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patron.events.index', compact('events'));
    }
    
    public function create()
    {
        $facultyMembers = User::where('role', 'faculty')->orderBy('name')->get();
        return view('patron.events.create', compact('facultyMembers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expected_date' => 'required|date|after:today',
            'venue' => 'nullable|string|max:255',
            'guest_speaker_name' => 'nullable|string|max:255',
            'guest_speaker_designation' => 'nullable|string|max:255',
            'faculty_mentor_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_rate' => 'required|numeric|min:0'
        ]);
        
        $user = Auth::user();
        
        // Get active term
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        // Prevent duplicate submissions
        $existing = Event::where('student_id', $user->id)
            ->where('title', $request->title)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();
            
        if ($existing) {
            return redirect()->route('patron.my-events.index')
                ->with('warning', 'This event was already submitted.');
        }
        
        DB::beginTransaction();
        try {
            // Calculate grand total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['total_amount'] ?? ($item['quantity'] * ($item['unit_rate'] ?? 0));
            }
            
            // Create event - Patron's events go directly to HOD
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'student_id' => $user->id,
                'created_by_role' => 'patron',
                'term_id' => $termId,
                'expected_date' => $request->expected_date,
                'venue' => $request->venue,
                'grand_total' => $grandTotal,
                'guest_speaker_name' => $request->guest_speaker_name,
                'guest_speaker_designation' => $request->guest_speaker_designation,
                'faculty_mentor_id' => $request->faculty_mentor_id,
                'status' => 'pending_hod' // Skip president and patron approval
            ]);
            
            // Create event items
            foreach ($request->items as $item) {
                EventItem::create([
                    'event_id' => $event->id,
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_rate' => $item['unit_rate'] ?? 0,
                    'total_amount' => $item['total_amount'] ?? ($item['quantity'] * ($item['unit_rate'] ?? 0)),
                    'is_approved_by_patron' => true, // Auto-approve their own items
                ]);
            }
            
            // Log activity
            ActivityLog::logActivity($user, "Patron submitted new event: {$event->title}");
            
            // AI Risk Assessment
            try {
                $aiRiskService = app(\App\Services\AiRiskService::class);
                $assessment = $aiRiskService->assessEventRisk($event);
                $event->update(['risk_assessment' => $assessment]);
            } catch (\Exception $e) {
                Log::warning('AI Risk Assessment failed', ['error' => $e->getMessage()]);
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
            
            return redirect()->route('patron.my-events.index')
                ->with('success', 'Event submitted successfully! Sent to HOD for final approval.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Patron event creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to submit event. Please try again.');
        }
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $event = Event::with('items')
            ->where('student_id', $user->id)
            ->where('created_by_role', 'patron')
            ->findOrFail($id);
        
        return view('patron.events.show', compact('event'));
    }
    
    public function trackEvents()
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Get all events in current term with their status
        $events = Event::with(['student', 'items'])
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');
        
        return view('patron.events.track', compact('events'));
    }
}
