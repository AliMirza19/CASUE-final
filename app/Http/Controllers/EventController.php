<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\ApproveEventRequest;
use App\Models\Event;
use App\Models\AcademicTerm;
use App\Services\EventWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class EventController extends Controller
{
    public function __construct(
        private EventWorkflowService $workflowService
    ) {}

    /**
     * Display a listing of events for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Event::with(['student', 'term', 'items']);

        // Filter events based on user role
        switch ($user->role) {
            case 'student':
                $query->where('student_id', $user->id);
                break;
            case 'president':
            case 'patron':
            case 'hod':
            case 'sa':
                // Show events pending approval for this role
                $pendingEvents = $this->workflowService->getPendingEventsForRole($user->role);
                $query->whereIn('id', $pendingEvents->pluck('id'));
                break;
            case 'admin':
                // Admin can see all events
                break;
            default:
                $query->whereRaw('1 = 0'); // No events for other roles
        }

        $events = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        $terms = AcademicTerm::where('status', 'active')->get();
        return view('events.create', compact('terms'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['student_id'] = $request->user()->id;
            $data['term_id'] = $request->user()->current_term_id;

            $event = $this->workflowService->submitEvent($data);

            return redirect()
                ->route('events.show', $event)
                ->with('success', 'Event submitted successfully and is now pending approval.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit event: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): View
    {
        $event->load(['student', 'term', 'items', 'graphics', 'volunteers']);
        $workflowHistory = $this->workflowService->getEventWorkflowHistory($event);
        $canApprove = $this->workflowService->canUserApproveEvent($event, request()->user());

        return view('events.show', compact('event', 'workflowHistory', 'canApprove'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        // Only allow editing if event is still pending_president and user is the creator
        $user = request()->user();
        if ($event->status !== 'pending_president' || $event->student_id !== $user->id) {
            abort(403, 'You cannot edit this event.');
        }

        $terms = AcademicTerm::where('status', 'active')->get();
        return view('events.edit', compact('event', 'terms'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            // Update event
            $event->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'expected_date' => $data['expected_date'],
                'venue' => $data['venue'],
                'guest_speaker_name' => $data['guest_speaker_name'] ?? null,
                'guest_speaker_designation' => $data['guest_speaker_designation'] ?? null,
                'faculty_mentor_id' => $data['faculty_mentor_id'] ?? null,
            ]);

            // Update items if provided
            if (isset($data['items'])) {
                // Delete existing items and create new ones
                $event->items()->delete();
                
                foreach ($data['items'] as $itemData) {
                    $event->items()->create([
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit_rate' => $itemData['unit_rate'],
                    ]);
                }
            }

            return redirect()
                ->route('events.show', $event)
                ->with('success', 'Event updated successfully.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update event: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve or reject an event.
     */
    public function approve(ApproveEventRequest $request, Event $event): RedirectResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            if ($data['action'] === 'approve') {
                $this->workflowService->approveEvent($event, $user);
                $message = 'Event approved successfully.';
            } else {
                $this->workflowService->rejectEvent($event, $user, $data['rejection_reason']);
                $message = 'Event rejected successfully.';
            }

            return redirect()
                ->route('events.show', $event)
                ->with('success', $message);
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }

    /**
     * Resubmit a rejected event.
     */
    public function resubmit(Event $event): RedirectResponse
    {
        try {
            $user = request()->user();
            $this->workflowService->resubmitEvent($event, $user);

            return redirect()
                ->route('events.show', $event)
                ->with('success', 'Event resubmitted successfully.');
        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to resubmit event: ' . $e->getMessage()]);
        }
    }

    /**
     * Display events pending approval for the current user's role.
     */
    public function pending(): View
    {
        $user = request()->user();
        $pendingEvents = $this->workflowService->getPendingEventsForRole($user->role);

        return view('events.pending', compact('pendingEvents'));
    }

    /**
     * Display workflow statistics.
     */
    public function statistics(): View
    {
        $user = request()->user();
        $term = AcademicTerm::find($user->current_term_id);
        
        if (!$term) {
            return back()->withErrors(['error' => 'No active term found.']);
        }

        $statistics = $this->workflowService->getWorkflowStatistics($term);

        return view('events.statistics', compact('statistics', 'term'));
    }
}