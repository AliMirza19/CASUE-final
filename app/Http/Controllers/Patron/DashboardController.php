<?php

namespace App\Http\Controllers\Patron;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\CandidateProfile;
use App\Models\EventGraphic;
use App\Models\Message;
use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventStatusUpdated;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.patron', compact('announcements'));
    }

    public function overview()
    {
        $user = Auth::user();
        
        // Get active term instead of user's current_term_id
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Pending events for patron review
        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_patron')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Pending candidate profiles
        $pendingCandidates = CandidateProfile::with('student')
            ->where('status', 'pending_patron')
            ->get();
        
        // Pending graphics
        $pendingGraphics = EventGraphic::with(['event', 'designer'])
            ->where('status', 'pending_patron')
            ->get();
        
        // Stats
        $totalPendingEvents = $pendingEvents->count();
        $totalPendingCandidates = $pendingCandidates->count();
        $totalPendingGraphics = $pendingGraphics->count();
        
        // Approved events this term
        $approvedEvents = Event::where('term_id', $termId)
            ->whereIn('status', ['pending_hod', 'pending_sa', 'approved'])
            ->count();
        
        // Get unread message count from HOD
        $unreadMessageCount = 0;
        $hodAssignment = RoleAssignment::getCurrentHod($termId);
        if ($hodAssignment) {
            $unreadMessageCount = Message::getUnreadCount($user->id, $hodAssignment->user_id);
        }
        
        return view('patron.overview', compact(
            'pendingEvents',
            'pendingCandidates',
            'pendingGraphics',
            'totalPendingEvents',
            'totalPendingCandidates',
            'totalPendingGraphics',
            'approvedEvents',
            'unreadMessageCount'
        ));
    }
    
    public function graphics()
    {
        // All graphics for review
        $allGraphics = EventGraphic::with(['event', 'designer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pendingGraphics = $allGraphics->where('status', 'pending_patron');
        $approvedGraphics = $allGraphics->where('status', 'approved');
        $rejectedGraphics = $allGraphics->where('status', 'rejected');
        
        return view('patron.graphics', compact(
            'allGraphics',
            'pendingGraphics',
            'approvedGraphics',
            'rejectedGraphics'
        ));
    }

    public function candidates()
    {
        // All candidates
        $allCandidates = CandidateProfile::with('student')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pendingCandidates = $allCandidates->where('status', 'pending_patron');
        $approvedCandidates = $allCandidates->where('status', 'approved');
        $rejectedCandidates = $allCandidates->where('status', 'rejected');
        
        return view('patron.candidates', compact(
            'allCandidates',
            'pendingCandidates',
            'approvedCandidates',
            'rejectedCandidates'
        ));
    }
    
    public function reviewEvent($id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        
        // Re-run AI Risk Assessment if it was previously saved as "offline" or "N/A"
        if (!$event->risk_assessment || 
            ($event->risk_assessment['risk_level'] ?? '') === 'N/A' || 
            str_contains(($event->risk_assessment['suggestions'] ?? ''), 'Update .env')) {
            
            $riskData = app(\App\Services\AiRiskService::class)->assessEventRisk($event);
            if ($riskData['risk_level'] !== 'Unknown') {
                $event->risk_assessment = $riskData;
                $event->save();
            }
        }

        $aiAnalysis = app(\App\Services\AiDecisionSupportService::class)->analyzeEventBudget($event, 'patron');

        return view('patron.review-event', compact('event', 'aiAnalysis'));
    }
    
    public function approveEvent(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit_rate' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'nullable|numeric|min:0',
            'items.*.is_approved' => 'nullable|boolean',
            'items.*.comment' => 'nullable|string|max:255',
            'digital_signature' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('digital_signature')) {
            $path = $request->file('digital_signature')->store('signatures', 'public');
            $user = Auth::user();
            $user->digital_signature = $path;
            $user->save();
        }
        
        $event = Event::with('items')->findOrFail($id);
        $changes = [];
        
        if ($request->has('items')) {
            foreach ($request->items as $itemId => $itemData) {
                $item = $event->items->find($itemId);
                if ($item) {
                    $oldQty = $item->quantity;
                    $oldAmount = $item->total_amount;
                    $oldApproved = $item->is_approved_by_patron;
                    
                    // Use existing value if input is missing or empty, otherwise case to type
                    $newQty = isset($itemData['quantity']) && $itemData['quantity'] !== '' ? (int)$itemData['quantity'] : $oldQty;
                    $newRate = isset($itemData['unit_rate']) && $itemData['unit_rate'] !== '' ? (float)$itemData['unit_rate'] : $item->unit_rate;
                    $newAmount = $newQty * $newRate;
                    
                    // Approval logic: If checkbox/radio not sent, keep existing state? 
                    // Or default to false? Usually forms send nothing for unchecked. 
                    // If user didn't touch it, we should probably keep old state?
                    // But if they clicked "Reject", the value '0' is sent. 
                    // If they clicked "Approve", value '1' is sent.
                    // If NOTHING sent, user probably didn't touch the radios.
                    if (isset($itemData['is_approved'])) {
                        $newApproved = $itemData['is_approved'] == '1';
                    } else {
                        $newApproved = $oldApproved; // Keep previous state if not modified
                    }

                    $newComment = $itemData['comment'] ?? $item->patron_comment;
                    
                    // Update item
                    $item->quantity = $newQty;
                    $item->unit_rate = $newRate;
                    $item->total_amount = $newAmount;
                    $item->is_approved_by_patron = $newApproved;
                    $item->patron_comment = $newComment;
                    $item->save();
                    
                    // Track changes for notification
                    if ($oldQty != $newQty || $oldAmount != $newAmount || $oldApproved != $newApproved) {
                        $changeDetails = "Amount: PKR {$newAmount} (Qty: {$newQty}, Rate: {$newRate})";
                        if (!$newApproved) $changeDetails = "REMOVED FROM BUDGET";
                        $changes[] = "- {$item->item_name}: {$changeDetails}";
                    }
                }
            }
        }
        
        // Recalculate event grand total (only approved items)
        $event->grand_total = $event->items()->where('is_approved_by_patron', true)->sum('total_amount');
        
        if ($request->action === 'approve') {
            $event->status = 'pending_hod';
            $event->patron_comments = $request->comments;
            $event->save();
            
            // Notify student if there were budget adjustments
            if (!empty($changes)) {
                Message::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $event->student_id,
                    'message_text' => "Your event \"{$event->title}\" has been reviewed by the Patron with budget adjustments:\n\n" . implode("\n", $changes) . "\n\nComments: " . ($request->comments ?? 'No additional comments.'),
                    'is_read' => false,
                ]);
            }
            
            // Notification message
            $notifyMessage = "Your event '{$event->title}' has been approved by the Patron.";
            $notifyType = 'success';
            
            if (!empty($changes)) {
                $notifyMessage .= " Budget adjustments were made.";
                $notifyType = 'warning';
            }
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                $notifyMessage,
                $notifyType
            ));
            
            return redirect()->route('patron.dashboard')
                ->with('success', 'Event reviewed, budget adjusted, and forwarded to HOD!');
        } else {
            $event->status = 'rejected';
            $event->patron_comments = $request->comments;
            $event->rejection_reason = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been rejected by the Patron.",
                'error'
            ));
            
            return redirect()->route('patron.dashboard')
                ->with('success', 'Event rejected.');
        }
    }
    
    public function reviewCandidate($id)
    {
        $candidate = CandidateProfile::with('student')->findOrFail($id);
        
        return view('patron.review-candidate', compact('candidate'));
    }
    
    public function approveCandidate(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'nullable|string|max:1000'
        ]);
        
        $candidate = CandidateProfile::findOrFail($id);
        
        $candidate->status = $request->action === 'approve' ? 'approved' : 'rejected';
        $candidate->patron_feedback = $request->feedback;
        $candidate->save();
        
        return redirect()->route('patron.dashboard')
            ->with('success', 'Candidate profile ' . $request->action . 'd!');
    }
    
    public function reviewGraphics($id)
    {
        $graphic = EventGraphic::with(['event', 'designer'])->findOrFail($id);
        
        return view('patron.review-graphics', compact('graphic'));
    }
    
    public function approveGraphics(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'feedback' => 'nullable|string|max:1000',
            'annotations' => 'nullable|string'
        ]);
        
        $graphic = EventGraphic::findOrFail($id);
        
        $graphic->status = $request->action === 'approve' ? 'approved' : 'rejected';
        $graphic->patron_feedback = $request->feedback;
        
        // Save annotations if provided
        if ($request->annotations) {
            $graphic->annotations = json_decode($request->annotations, true);
        }
        
        $graphic->save();
        
        $message = $request->action === 'approve' 
            ? 'Graphic design approved successfully!' 
            : 'Graphic design rejected with annotations.';
        
        return redirect()->route('patron.graphics')
            ->with('success', $message);
    }
}
