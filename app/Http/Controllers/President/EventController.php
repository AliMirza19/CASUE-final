<?php

namespace App\Http\Controllers\President;

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
        
        // Get events created by president
        $events = Event::where('student_id', $user->id)
            ->where('created_by_role', 'president')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('president.events.index', compact('events'));
    }
    
    public function create()
    {
        $facultyMembers = User::where('role', 'faculty')->orderBy('name')->get();
        return view('president.events.create', compact('facultyMembers'));
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

        // Prevent duplicate submissions
        $existing = Event::where('student_id', $user->id)
            ->where('title', $request->title)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();
            
        if ($existing) {
            return redirect()->route('president.my-events.index')
                ->with('warning', 'This event was already submitted.');
        }
        
        DB::beginTransaction();
        try {
            // Calculate grand total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['total_amount'] ?? 0;
            }
            
            // Create event - President's events go directly to Patron
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'student_id' => $user->id,
                'created_by_role' => 'president',
                'term_id' => $termId,
                'expected_date' => $request->expected_date,
                'grand_total' => $grandTotal,
                'guest_speaker_name' => $request->guest_speaker_name,
                'guest_speaker_designation' => $request->guest_speaker_designation,
                'faculty_mentor_id' => $request->faculty_mentor_id,
                'status' => 'pending_patron' // Skip president approval
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
            ActivityLog::logActivity($user, "President submitted new event: {$event->title}");
            
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
            
            return redirect()->route('president.events.index')
                ->with('success', 'Event submitted successfully! Sent to Patron for review.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('President event creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to submit event. Please try again.');
        }
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $event = Event::with('items')
            ->where('student_id', $user->id)
            ->where('created_by_role', 'president')
            ->findOrFail($id);
        
        return view('president.events.show', compact('event'));
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
        
        return view('president.events.track', compact('events'));
    }
}
