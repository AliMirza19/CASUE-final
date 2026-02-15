<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Budget;
use App\Models\AcademicTerm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EventWorkflowService
{
    /**
     * Submit a new event for approval workflow.
     * 
     * @param array $data Event data including items
     * @return Event The created event
     * @throws Exception If submission fails
     */
    public function submitEvent(array $data): Event
    {
        return DB::transaction(function () use ($data) {
            // Create the event with pending_president status
            $event = Event::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'student_id' => $data['student_id'],
                'term_id' => $data['term_id'],
                'expected_date' => $data['expected_date'],
                'venue' => $data['venue'],
                'guest_speaker_name' => $data['guest_speaker_name'] ?? null,
                'guest_speaker_designation' => $data['guest_speaker_designation'] ?? null,
                'faculty_mentor_id' => $data['faculty_mentor_id'] ?? null,
                'status' => 'pending_president',
                'grand_total' => 0, // Will be calculated from items
            ]);

            // Create event items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    EventItem::create([
                        'event_id' => $event->id,
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['quantity'],
                        'unit_rate' => $itemData['unit_rate'],
                    ]);
                }
            }

            // Refresh to get updated grand_total
            $event->refresh();

            // Log the activity
            $this->logActivity(
                $event->student_id,
                'student',
                "Submitted event: {$event->title}",
                $event->id
            );

            // Notify the next approver (president)
            $this->notifyNextApprover($event);

            return $event;
        });
    }

    /**
     * Approve an event and move it to the next stage in the workflow.
     * 
     * @param Event $event The event to approve
     * @param User $approver The user approving the event
     * @return bool True if approval was successful
     * @throws Exception If approval fails or user is not authorized
     */
    public function approveEvent(Event $event, User $approver): bool
    {
        return DB::transaction(function () use ($event, $approver) {
            // Verify the approver has the right role for current status
            $expectedRole = $event->getNextApprover();
            if ($approver->role !== $expectedRole) {
                throw new Exception("User with role '{$approver->role}' cannot approve event at status '{$event->status}'");
            }

            // Get the next status
            $nextStatus = $event->getNextStatus();
            if (!$nextStatus) {
                throw new Exception("Event cannot be approved from status '{$event->status}'");
            }

            // Special handling for final approval (SA -> approved)
            if ($nextStatus === 'approved') {
                // Check budget availability
                $budget = Budget::where('term_id', $event->term_id)->first();
                if ($budget && $budget->remaining_amount < $event->grand_total) {
                    throw new Exception("Insufficient budget. Required: {$event->grand_total}, Available: {$budget->remaining_amount}");
                }

                // Deduct from budget
                if ($budget) {
                    $budget->deductAmount($event->grand_total);
                }
            }

            // Update event status
            $event->update([
                'status' => $nextStatus,
                'rejection_reason' => null, // Clear any previous rejection reason
            ]);

            // Log the activity
            $this->logActivity(
                $approver->id,
                $approver->role,
                "Approved event: {$event->title} (Status: {$event->status})",
                $event->id
            );

            // Notify next approver if not final approval
            if ($nextStatus !== 'approved') {
                $this->notifyNextApprover($event);
            } else {
                // Notify student of final approval
                $this->logActivity(
                    $event->student_id,
                    'student',
                    "Event approved: {$event->title}",
                    $event->id
                );
            }

            return true;
        });
    }

    /**
     * Reject an event with a reason.
     * 
     * @param Event $event The event to reject
     * @param User $approver The user rejecting the event
     * @param string $reason The rejection reason
     * @return bool True if rejection was successful
     * @throws Exception If rejection fails or user is not authorized
     */
    public function rejectEvent(Event $event, User $approver, string $reason): bool
    {
        return DB::transaction(function () use ($event, $approver, $reason) {
            // Verify the approver has the right role for current status
            $expectedRole = $event->getNextApprover();
            if ($approver->role !== $expectedRole) {
                throw new Exception("User with role '{$approver->role}' cannot reject event at status '{$event->status}'");
            }

            // Update event status to rejected
            $event->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            // Log the activity
            $this->logActivity(
                $approver->id,
                $approver->role,
                "Rejected event: {$event->title} - Reason: {$reason}",
                $event->id
            );

            // Notify student of rejection
            $this->logActivity(
                $event->student_id,
                'student',
                "Event rejected: {$event->title} - Reason: {$reason}",
                $event->id
            );

            return true;
        });
    }

    /**
     * Get the next approver role for an event based on its current status.
     * 
     * @param Event $event The event to check
     * @return string|null The next approver role or null if no more approvers
     */
    public function getNextApprover(Event $event): ?string
    {
        return $event->getNextApprover();
    }

    /**
     * Notify the next approver about a pending event.
     * 
     * @param Event $event The event requiring approval
     * @return void
     */
    public function notifyNextApprover(Event $event): void
    {
        $nextApprover = $this->getNextApprover($event);
        
        if (!$nextApprover) {
            return; // No more approvers needed
        }

        // Log notification activity
        $this->logActivity(
            null, // System notification
            'system',
            "Event '{$event->title}' requires approval from {$nextApprover}",
            $event->id
        );

        // In a real application, you might send email notifications here
        // For now, we'll just log the notification
        Log::info("Event approval notification", [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'next_approver' => $nextApprover,
            'status' => $event->status,
        ]);
    }

    /**
     * Get all events pending approval for a specific role.
     * 
     * @param string $role The approver role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingEventsForRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        $statusMap = [
            'president' => 'pending_president',
            'patron' => 'pending_patron',
            'hod' => 'pending_hod',
            'sa' => 'pending_sa',
        ];

        $status = $statusMap[$role] ?? null;
        
        if (!$status) {
            return collect();
        }

        return Event::where('status', $status)
            ->with(['student', 'term', 'items'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get event workflow history.
     * 
     * @param Event $event The event to get history for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventWorkflowHistory(Event $event): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('related_event_id', $event->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Check if a user can approve/reject an event.
     * 
     * @param Event $event The event to check
     * @param User $user The user to check permissions for
     * @return bool True if user can approve/reject
     */
    public function canUserApproveEvent(Event $event, User $user): bool
    {
        $nextApprover = $this->getNextApprover($event);
        return $nextApprover && $user->role === $nextApprover;
    }

    /**
     * Get workflow statistics for a term.
     * 
     * @param AcademicTerm $term The academic term
     * @return array Statistics array
     */
    public function getWorkflowStatistics(AcademicTerm $term): array
    {
        $events = Event::where('term_id', $term->id);

        return [
            'total_events' => $events->count(),
            'pending_president' => $events->clone()->where('status', 'pending_president')->count(),
            'pending_patron' => $events->clone()->where('status', 'pending_patron')->count(),
            'pending_hod' => $events->clone()->where('status', 'pending_hod')->count(),
            'pending_sa' => $events->clone()->where('status', 'pending_sa')->count(),
            'approved' => $events->clone()->where('status', 'approved')->count(),
            'rejected' => $events->clone()->where('status', 'rejected')->count(),
            'completed' => $events->clone()->where('status', 'completed')->count(),
        ];
    }

    /**
     * Resubmit a rejected event (reset to pending_president).
     * 
     * @param Event $event The rejected event to resubmit
     * @param User $student The student resubmitting
     * @return bool True if resubmission was successful
     * @throws Exception If resubmission fails
     */
    public function resubmitEvent(Event $event, User $student): bool
    {
        if ($event->status !== 'rejected') {
            throw new Exception("Only rejected events can be resubmitted");
        }

        if ($event->student_id !== $student->id) {
            throw new Exception("Only the event creator can resubmit");
        }

        return DB::transaction(function () use ($event, $student) {
            $event->update([
                'status' => 'pending_president',
                'rejection_reason' => null,
            ]);

            // Log the activity
            $this->logActivity(
                $student->id,
                'student',
                "Resubmitted event: {$event->title}",
                $event->id
            );

            // Notify next approver
            $this->notifyNextApprover($event);

            return true;
        });
    }

    /**
     * Log an activity for audit trail.
     * 
     * @param int|null $userId The user ID (null for system activities)
     * @param string $userRole The user role
     * @param string $actionText The action description
     * @param int|null $eventId The related event ID
     * @return void
     */
    private function logActivity(?int $userId, string $userRole, string $actionText, ?int $eventId = null): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'user_role' => $userRole,
            'action_text' => $actionText,
            'related_event_id' => $eventId,
        ]);
    }
}