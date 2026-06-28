<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\ActivityLog;
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
        
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $events = Event::where('student_id', $user->id)
            ->where('created_by_role', 'hod')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('hod.events.index', compact('events'));
    }
    
    public function create()
    {
        $facultyMembers = User::where('role', 'faculty')->orderBy('name')->get();
        return view('hod.events.create', compact('facultyMembers'));
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
        $activeTerm = \App\Models\AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        $existing = Event::where('student_id', $user->id)
            ->where('title', $request->title)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();
            
        if ($existing) {
            return redirect()->route('hod.my-events.index')->with('warning', 'This event was already submitted.');
        }
        
        DB::beginTransaction();
        try {
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['total_amount'] ?? ($item['quantity'] * ($item['unit_rate'] ?? 0));
            }
            
            // HOD created events are self-approved!
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'student_id' => $user->id,
                'created_by_role' => 'hod',
                'term_id' => $termId,
                'expected_date' => $request->expected_date,
                'venue' => $request->venue,
                'grand_total' => $grandTotal,
                'guest_speaker_name' => $request->guest_speaker_name,
                'guest_speaker_designation' => $request->guest_speaker_designation,
                'faculty_mentor_id' => $request->faculty_mentor_id,
                'status' => 'approved', // self-approved
                'hod_comments' => 'Self-approved by Head of Department.'
            ]);
            
            foreach ($request->items as $item) {
                EventItem::create([
                    'event_id' => $event->id,
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_rate' => $item['unit_rate'] ?? 0,
                    'total_amount' => $item['total_amount'] ?? ($item['quantity'] * ($item['unit_rate'] ?? 0)),
                    'is_approved_by_president' => true,
                    'is_approved_by_patron' => true,
                    'is_approved_by_hod' => true,
                ]);
            }
            
            ActivityLog::logActivity($user, "HOD created and self-approved new event: {$event->title}");
            
            DB::commit();
            
            return redirect()->route('hod.my-events.index')
                ->with('success', 'Event created and self-approved successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('HOD event creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to submit event. Please try again.');
        }
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $event = Event::with('items')
            ->where('student_id', $user->id)
            ->where('created_by_role', 'hod')
            ->findOrFail($id);
        
        return view('hod.events.show', compact('event'));
    }
}
