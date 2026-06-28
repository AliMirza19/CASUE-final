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
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.hod', compact('announcements'));
    }

    public function overview()
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
            ->where('status', 'approved')
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
            ->where('status', 'approved')
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
        
        return view('hod.overview', compact(
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

        // Refresh AI Risk if it was previously "offline" or invalid
        if (!$event->risk_assessment || 
            ($event->risk_assessment['risk_level'] ?? '') === 'N/A' || 
            ($event->risk_assessment['risk_level'] ?? '') === 'Error' ||
            str_contains(($event->risk_assessment['suggestions'] ?? ''), 'Update .env')) {
            
            $riskData = app(\App\Services\AiRiskService::class)->assessEventRisk($event);
            if ($riskData['risk_level'] !== 'Unknown') {
                $event->risk_assessment = $riskData;
                $event->save();
            }
        }
        
        $aiAnalysis = app(\App\Services\AiDecisionSupportService::class)->analyzeEventBudget($event, 'hod');
        
        return view('hod.review-event', compact('event', 'budget', 'aiAnalysis'));
    }

    /**
     * Show final approval form with signature options.
     */
    public function finalApprovalForm(Request $request, $id)
    {
        $event = Event::with(['student', 'items', 'facultyMentor'])->findOrFail($id);
        $budget = Budget::where('term_id', Auth::user()->current_term_id)->first();
        $user = Auth::user();

        // If it's a rejection, bypass the final form
        if ($request->action === 'reject') {
            return $this->approveEvent($request, $id);
        }

        // Validate items and calculate grand total first
        if ($request->has('items')) {
            foreach ($request->items as $itemId => $itemData) {
                $item = $event->items->find($itemId);
                if ($item) {
                    $newQty = isset($itemData['quantity']) && $itemData['quantity'] !== '' ? (int)$itemData['quantity'] : $item->quantity;
                    $newRate = isset($itemData['unit_rate']) && $itemData['unit_rate'] !== '' ? (float)$itemData['unit_rate'] : $item->unit_rate;
                    
                    $item->quantity = $newQty;
                    $item->unit_rate = $newRate;
                    $item->total_amount = $newQty * $newRate;
                    
                    $item->is_approved_by_hod = isset($itemData['is_approved_by_hod']) ? ($itemData['is_approved_by_hod'] == '1') : $item->is_approved_by_hod;
                    $item->hod_comment = $itemData['hod_comment'] ?? null;
                    $item->save();
                }
            }
        }

        // Recalculate event grand total
        $event->grand_total = $event->items()
            ->where('is_approved_by_patron', true)
            ->where('is_approved_by_hod', true)
            ->sum('total_amount');
        $event->save();

        if (!$budget) {
            return back()->with('error', 'No budget found for this term.');
        }

        if ($budget->remaining_amount < $event->grand_total) {
            return back()->with('error', 'Insufficient budget!');
        }

        $comments = $request->comments;

        return view('hod.final-approval-form', compact('event', 'budget', 'user', 'comments'));
    }
    
    public function approveEvent(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'digital_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $event = Event::with('items')->findOrFail($id);
        $budget = Budget::where('term_id', Auth::user()->current_term_id)->first();
        $user = Auth::user();

        // Handle signature upload if provided
        if ($request->hasFile('digital_signature')) {
            $path = $request->file('digital_signature')->store('signatures', 'public');
            $user->digital_signature = $path;
            $user->save();
        }

        
        if ($request->action === 'approve') {
            // Check if user has signature
            if (!$user->digital_signature) {
                return back()->with('error', 'Digital signature is required for final approval.');
            }

            // Recalculate event grand total based on items approved by both Patron and HOD
            $event->grand_total = $event->items()
                ->where('is_approved_by_patron', true)
                ->where('is_approved_by_hod', true)
                ->sum('total_amount');
            
            // Final Approval Logic (Migrated from Student Affairs)
            if (!$budget) {
                return back()->with('error', 'No budget found for this term. Please set up the budget first.');
            }
            
            // Check if sufficient budget available
            if ($budget->remaining_amount < $event->grand_total) {
                return back()->with('error', 'Insufficient budget! Required: ' . number_format($event->grand_total, 2) . ', Available: ' . number_format($budget->remaining_amount, 2));
            }
            
            // Deduct from budget
            $budget->remaining_amount -= $event->grand_total;
            $budget->save();
            
            // Final Approve event
            $event->status = 'approved';
            $event->hod_comments = $request->comments;
            
            // Save signature settings
            $event->signature_settings = [
                'sig_scale' => $request->sig_scale,
                'sig_y' => $request->sig_y,
                'stamp_scale' => $request->stamp_scale,
                'stamp_rotate' => $request->stamp_rotate,
                'stamp_x' => $request->stamp_x,
                'stamp_y' => $request->stamp_y,
            ];
            
            $event->save();
            
            // Notify student
            $event->student->notify(new EventStatusUpdated(
            $event,
                "Congratulations! Your event '{$event->title}' has been fully approved by the HOD.",
                'success'
            ));
            
            // Add President as Admin
            $presidentAssignment = \App\Models\RoleAssignment::where('term_id', $event->term_id)
                ->where('role', 'president')
                ->where('is_active', true)
                ->first();
            $president = $presidentAssignment ? $presidentAssignment->user : \App\Models\User::where('role', 'president')->first();
            if ($president) {
                $president->notify(new EventStatusUpdated(
                    $event,
                    "Event '{$event->title}' is fully approved! You can now assign tasks to teams.",
                    'info'
                ));
            }

            // Notify Volunteer Coordinator to select volunteers
            $vc = \App\Models\User::where('role', 'vc')->first();
            if ($vc) {
                $vc->notify(new EventStatusUpdated(
                    $event,
                    "Event '{$event->title}' is approved! Please select volunteers for this event and send to President.",
                    'info'
                ));
            }

            // --- AUTOMATIC CHAT GROUP CREATION ---
            $chatGroup = \App\Models\ChatGroup::create([
                'event_id' => $event->id,
                'name' => "Chat: " . $event->title,
            ]);

            // Add President as Admin
            if ($president) {
                \App\Models\ChatGroupMember::create([
                    'chat_group_id' => $chatGroup->id,
                    'user_id' => $president->id,
                    'role' => 'admin',
                ]);
            }

            // Add Event Owner (Student)
            \App\Models\ChatGroupMember::create([
                'chat_group_id' => $chatGroup->id,
                'user_id' => $event->student_id,
                'role' => 'member',
            ]);

            // Add current team leads for this term
            $teamRoles = ['gd', 'vc', 'smt', 'photo', 'video', 'doc', 'deco', 'sa'];
            foreach ($teamRoles as $roleKey) {
                $assignment = \App\Models\RoleAssignment::where('term_id', $event->term_id)
                    ->where('role', $roleKey)
                    ->where('is_active', true)
                    ->first();
                
                if ($assignment) {
                    \App\Models\ChatGroupMember::updateOrCreate(
                        ['chat_group_id' => $chatGroup->id, 'user_id' => $assignment->user_id],
                        ['role' => 'member']
                    );
                }
            }
            
            // Archive Signed Approval Form
            $this->archiveApprovalForm($event);
            
            return redirect()->route('hod.dashboard')
                ->with('success', 'Event fully approved and budget deducted!');
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
        $user = \Illuminate\Support\Facades\Auth::user();
        return view('profile.show', compact('user'));
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
        
        // Generate filename
        $filename = 'Financial_Summary_' . ($activeTerm ? str_replace(' ', '_', $activeTerm->term_name) : 'Current_Term') . '_' . now()->format('Y-m-d') . '.html';
        
        // Render view
        $html = view('hod.financial-summary-pdf', compact('financialReport', 'activeTerm'))->render();
        
        // ARCHIVE IT TOO
        $filename = 'financial_reports/Financial_Report_' . $termId . '_' . time() . '.html';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $html);

        \App\Models\EventDocument::updateOrCreate(
            [
                'term_id'           => $termId,
                'doc_type'          => 'financial_report',
                'original_filename' => $filename, // Use unique name as key or similar
            ],
            [
                'event_id'          => null,
                'uploaded_by'       => Auth::id(),
                'file_path'         => $filename,
                'original_filename' => 'Term_Financial_Report_' . now()->format('Y_m_d') . '.html',
                'description'       => "Institutional Financial Summary for " . ($activeTerm ? $activeTerm->term_name : 'Current Term'),
                'visible_to_roles'  => ['admin', 'hod', 'patron'],
            ]
        );
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

    /**
     * Show HOD settings page for signature management.
     */
    public function settings()
    {
        $user = Auth::user();
        return view('hod.settings', compact('user'));
    }

    /**
     * Update HOD digital assets.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'digital_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('digital_signature')) {
            $path = $request->file('digital_signature')->store('signatures', 'public');
            $user->digital_signature = $path;
        }



        $user->save();

        return back()->with('success', 'Digital assets updated successfully.');
    }

    /**
     * Archive the Signed Approval Form as a static snapshot.
     */
    private function archiveApprovalForm(Event $event)
    {
        // Get HOD for the event's term
        $hodAssignment = \App\Models\RoleAssignment::getCurrentHod($event->term_id);
        $hod = $hodAssignment ? $hodAssignment->user : Auth::user();

        // Render the approval PDF view
        $html = view('student.events.pdf-approval', compact('event', 'hod'))->render();
        
        // Save to storage
        $filename = 'approval_forms/Approval_' . $event->id . '_' . time() . '.html';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $html);

        // Create document entry
        \App\Models\EventDocument::updateOrCreate(
            [
                'event_id' => $event->id,
                'doc_type' => 'approval_form',
            ],
            [
                'term_id'           => $event->term_id,
                'uploaded_by'       => Auth::id(),
                'file_path'         => $filename,
                'original_filename' => 'Signed_Approval_Form.html',
                'description'       => "Official Signed Approval Form for event: {$event->title}",
                'visible_to_roles'  => ['admin', 'hod', 'patron', 'president', 'student'],
            ]
        );
    }
}



