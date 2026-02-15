<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Budget;
use App\Models\AcademicTerm;
use App\Models\User;
use App\Models\RoleAssignment;
use App\Models\Message;
use App\Services\FinancialAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventStatusUpdated;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active term instead of user's current_term_id
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        // Get current term
        $currentTerm = AcademicTerm::find($termId);
        
        // Get budget for current term
        $budget = Budget::where('term_id', $termId)->first();
        
        // Pending events for HOD review
        $pendingEvents = Event::with(['student', 'items'])
            ->where('status', 'pending_hod')
            ->where('term_id', $termId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Stats
        $totalPending = $pendingEvents->count();
        $totalApproved = Event::where('term_id', $termId)
            ->whereIn('status', ['pending_sa', 'approved'])
            ->count();
        $totalRejected = Event::where('term_id', $termId)
            ->where('status', 'rejected')
            ->count();
        
        // Budget stats
        $totalBudget = $budget ? $budget->total_amount : 0;
        $remainingBudget = $budget ? $budget->remaining_amount : 0;
        $spentBudget = $totalBudget - $remainingBudget;
        
        // Recent approved events
        $recentApproved = Event::with('student')
            ->where('term_id', $termId)
            ->whereIn('status', ['pending_sa', 'approved'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get current Patron assignment
        $currentPatronAssignment = RoleAssignment::getCurrentPatron($termId);
        $previousPatronAssignment = RoleAssignment::getPreviousPatron($termId);
        $needsPatronAssignment = !$currentPatronAssignment;
        
        // Patron assignment history
        $patronHistory = RoleAssignment::getHistory('patron', 5);
        
        // Get unread message count from Patron
        $unreadMessageCount = 0;
        if ($currentPatronAssignment) {
            $unreadMessageCount = Message::getUnreadCount($user->id, $currentPatronAssignment->user_id);
        }
        
        return view('dashboards.hod', compact(
            'currentTerm',
            'budget',
            'pendingEvents',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'totalBudget',
            'remainingBudget',
            'spentBudget',
            'recentApproved',
            'currentPatronAssignment',
            'previousPatronAssignment',
            'needsPatronAssignment',
            'patronHistory',
            'unreadMessageCount'
        ));
    }
    
    public function manageBudget()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        $currentTerm = AcademicTerm::find($termId);
        $budget = Budget::where('term_id', $termId)->first();
        
        return view('hod.manage-budget', compact('currentTerm', 'budget'));
    }
    
    public function saveBudget(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0'
        ]);
        
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        $budget = Budget::where('term_id', $termId)->first();

        // Check if budget exists and is locked
        if ($budget && $budget->is_locked) {
            return redirect()->route('hod.budget')
                ->with('error', 'Budget is locked and cannot be modified.');
        }
        
        $budget = Budget::updateOrCreate(
            ['term_id' => $termId],
            [
                'total_amount' => $request->total_amount,
                'remaining_amount' => $request->total_amount,
                'is_locked' => true // Lock immediately upon setting
            ]
        );
        
        return redirect()->route('hod.dashboard')
            ->with('success', 'Budget set and locked successfully!');
    }
    
    public function reviewEvent($id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        $budget = Budget::where('term_id', Auth::user()->current_term_id)->first();
        
        return view('hod.review-event', compact('event', 'budget'));
    }
    
    public function approveEvent(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit_rate' => 'nullable|numeric|min:0'
        ]);
        
        $event = Event::with('items')->findOrFail($id);
        $budget = Budget::where('term_id', Auth::user()->current_term_id)->first();
        $changes = [];
        
        if ($request->has('items')) {
            foreach ($request->items as $itemId => $itemData) {
                $item = $event->items->find($itemId);
                if ($item) {
                    $oldQty = $item->quantity;
                    $oldRate = $item->unit_rate;
                    $oldDecision = $item->is_approved_by_hod;
                    
                    $newQty = isset($itemData['quantity']) && $itemData['quantity'] !== '' ? (int)$itemData['quantity'] : $oldQty;
                    $newRate = isset($itemData['unit_rate']) && $itemData['unit_rate'] !== '' ? (float)$itemData['unit_rate'] : $oldRate;
                    $newDecision = isset($itemData['is_approved_by_hod']) ? ($itemData['is_approved_by_hod'] == '1') : $oldDecision;
                    
                    // Update item
                    $item->quantity = $newQty;
                    $item->unit_rate = $newRate;
                    $item->is_approved_by_hod = $newDecision;
                    $item->hod_comment = $itemData['hod_comment'] ?? null;
                    $item->save();
                    
                        // Track changes
                        if ($oldQty != $newQty || $oldRate != $newRate || $oldDecision != $newDecision) {
                            $changeDetails = "({$newQty} x {$newRate})";
                            if (!$newDecision) $changeDetails = "REMOVED FROM BUDGET";
                            $changes[] = "- {$item->item_name}: {$changeDetails}";
                        }
                }
            }
        }
        
        // Recalculate event grand total based on items approved by both Patron and HOD
        $event->grand_total = $event->items()
            ->where('is_approved_by_patron', true)
            ->where('is_approved_by_hod', true)
            ->sum('total_amount');
        
        if ($request->action === 'approve') {
            // Check budget
            if ($budget && $event->grand_total > $budget->remaining_amount) {
                return back()->with('error', 'Insufficient budget for this event after adjustments!');
            }
            
            // NOTE: Budget deduction moved to Student Affairs approval stage.
            // if ($budget) {
            //     $budget->remaining_amount -= $event->grand_total;
            //     $budget->save();
            // }
            
            $event->status = 'pending_sa';
            $event->hod_comments = $request->comments;
            $event->save();
            
            // Notify student of HOD adjustments
            if (!empty($changes)) {
                Message::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $event->student_id,
                    'message_text' => "Your event \"{$event->title}\" has finalized budget review by HOD with the following adjustments:\n\n" . implode("\n", $changes) . "\n\nComments: " . ($request->comments ?? 'No additional comments.'),
                    'is_read' => false,
                ]);
            }
            
            // Notification message
            $notifyMessage = "Your event '{$event->title}' has been approved by the HOD.";
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
            
            return redirect()->route('hod.dashboard')
                ->with('success', 'Event approved, budget finalized, and forwarded to Student Affairs!');
        } else {
            $event->status = 'rejected';
            $event->hod_comments = $request->comments;
            $event->rejection_reason = $request->comments;
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
                $event,
                "Your event '{$event->title}' has been rejected by the HOD.",
                'error'
            ));
            
            return redirect()->route('hod.dashboard')
                ->with('success', 'Event rejected.');
        }
    }
    
    /**
     * Show Patron management page.
     */
    public function managePatron()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        $currentTerm = AcademicTerm::find($termId);
        
        if (!$currentTerm) {
            return redirect()->route('hod.dashboard')
                ->with('error', 'No active term found.');
        }
        
        $currentPatronAssignment = RoleAssignment::getCurrentPatron($termId);
        $previousPatronAssignment = RoleAssignment::getPreviousPatron($termId);
        $patronHistory = RoleAssignment::getHistory('patron', 10);
        
        // Get all users with Patron role for reference
        $patronUsers = User::where('role', 'patron')->get();
        
        return view('hod.manage-patron', compact(
            'currentTerm',
            'currentPatronAssignment',
            'previousPatronAssignment',
            'patronHistory',
            'patronUsers'
        ));
    }
    
    /**
     * Search users for Patron assignment.
     */
    public function searchUserForPatron(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $users = User::where(function($q) use ($query) {
            $q->where('reg_id', 'like', "%{$query}%")
              ->orWhere('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'reg_id', 'name', 'email', 'role']);
        
        return response()->json($users);
    }
    
    /**
     * Continue with previous Patron.
     */
    public function continuePatron(Request $request)
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        $currentTerm = AcademicTerm::find($termId);
        
        if (!$currentTerm) {
            return back()->with('error', 'No active term found.');
        }
        
        $previousPatronAssignment = RoleAssignment::getPreviousPatron($termId);
        
        if (!$previousPatronAssignment) {
            return back()->with('error', 'No previous Patron found to continue.');
        }
        
        // Assign the same user as Patron for new term
        RoleAssignment::assignPatron(
            $previousPatronAssignment->user_id,
            $termId,
            Auth::id()
        );
        
        // Update user's current_term_id
        $previousPatronAssignment->user->update(['current_term_id' => $termId]);
        
        return redirect()->route('hod.manage-patron')
            ->with('success', 'Patron continued from previous term successfully!');
    }
    
    /**
     * Appoint new Patron.
     */
    public function appointPatron(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $user = Auth::user();
        
        // Get active term
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        $currentTerm = AcademicTerm::find($termId);
        
        if (!$currentTerm) {
            return back()->with('error', 'No active term found.');
        }
        
        $patronUser = User::findOrFail($request->user_id);
        
        // Ensure user is faculty (no need to change role - it should already be faculty)
        if ($patronUser->role !== 'faculty') {
            return back()->with('error', 'Only faculty members can be appointed as Patron.');
        }
        
        // Assign as Patron for this term
        RoleAssignment::assignPatron(
            $patronUser->id,
            $termId,
            Auth::id()
        );
        
        // Update user's current_term_id
        $patronUser->update(['current_term_id' => $termId]);
        
        return redirect()->route('hod.manage-patron')
            ->with('success', "Patron appointed successfully! {$patronUser->name} is now the Patron for {$currentTerm->term_name}.");
    }
    
    /**
     * Show analytics page with financial charts.
     */
    public function analytics()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        $currentTerm = AcademicTerm::find($termId);
        
        // Budget data
        $budget = Budget::where('term_id', $termId)->first();
        $totalBudget = $budget ? $budget->total_amount : 0;
        $remainingBudget = $budget ? $budget->remaining_amount : 0;
        $spentBudget = $totalBudget - $remainingBudget;
        
        // Events by status
        $eventsByStatus = [
            'pending' => Event::where('term_id', $termId)->where('status', 'like', 'pending%')->count(),
            'approved' => Event::where('term_id', $termId)->where('status', 'approved')->count(),
            'rejected' => Event::where('term_id', $termId)->where('status', 'rejected')->count(),
        ];
        
        // Monthly spending (last 6 months)
        $monthlySpending = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        // Top 5 events by budget
        $topEvents = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->orderBy('grand_total', 'desc')
            ->limit(5)
            ->get();
        
        return view('hod.analytics', compact(
            'currentTerm',
            'totalBudget',
            'remainingBudget',
            'spentBudget',
            'eventsByStatus',
            'monthlySpending',
            'topEvents'
        ));
    }
    
    /**
     * Lock budget for the term.
     */
    public function lockBudget(Request $request)
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget) {
            return back()->with('error', 'No budget found to lock.');
        }
        
        $budget->is_locked = true;
        $budget->save();
        
        return back()->with('success', 'Budget has been locked successfully!');
    }
    
    /**
     * Show profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('hod.profile', compact('user'));
    }
    
    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);
        
        $user = Auth::user();
        $user->update($request->only(['name', 'email']));
        
        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show financial reports dashboard.
     */
    public function financialReports()
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $financialService = new FinancialAnalyticsService();
        $financialReport = $financialService->generateFinancialReport($termId);
        
        return view('hod.financial-reports', compact('financialReport', 'activeTerm'));
    }

    /**
     * Get financial chart data for AJAX requests.
     */
    public function getFinancialChartData(Request $request)
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $financialService = new FinancialAnalyticsService();
        
        $chartType = $request->get('type', 'comparison');
        
        switch ($chartType) {
            case 'comparison':
                $data = $financialService->getSpendingComparison($termId);
                break;
            case 'trends':
                $data = $financialService->getHistoricalTrends($termId, 6);
                break;
            case 'monthly':
                $data = $financialService->getMonthlySpendingStats($termId);
                break;
            default:
                $data = $financialService->getSpendingComparison($termId);
        }
        
        return response()->json($data);
    }

    /**
     * Export financial summary as PDF.
     */
    public function exportFinancialSummary(Request $request)
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $financialService = new FinancialAnalyticsService();
        $financialReport = $financialService->generateFinancialReport($termId);
        
        // Generate filename with timestamp
        $filename = 'Financial_Summary_' . ($activeTerm ? str_replace(' ', '_', $activeTerm->term_name) : 'Current_Term') . '_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        // Return HTML view with PDF-friendly headers
        $html = view('hod.financial-summary-pdf', compact('financialReport', 'activeTerm'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get spending analytics data for real-time updates.
     */
    public function getSpendingAnalytics(Request $request)
    {
        $user = Auth::user();
        
        // Get active term
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
        
        $financialService = new FinancialAnalyticsService();
        
        $analytics = [
            'spending_comparison' => $financialService->getSpendingComparison($termId),
            'top_events' => $financialService->getTopSpendingEvents($termId, 5),
            'alerts' => $financialService->checkBudgetAlerts($termId),
            'efficiency' => $financialService->calculateSpendingEfficiency($termId),
            'updated_at' => now()->toDateTimeString()
        ];
        
        return response()->json($analytics);
    }
}
