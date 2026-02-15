<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view events (with appropriate filtering)
        return true;
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        // Students can view their own events
        if ($user->isStudent() && $event->student_id === $user->id) {
            return true;
        }

        // Approvers can view events in their approval queue
        $nextApprover = $event->getNextApprover();
        if ($nextApprover && $user->hasRole($nextApprover)) {
            return true;
        }

        // Admins and SA can view all events
        return $user->isAdmin() || $user->hasRole('sa');
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        // Only the student who submitted the event can edit it
        // And only if it's still pending
        return $user->isStudent() && 
               $event->student_id === $user->id && 
               $event->isPending();
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Students can delete their own pending events
        if ($user->isStudent() && 
            $event->student_id === $user->id && 
            $event->isPending()) {
            return true;
        }

        // Admins can delete any event
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve the event.
     */
    public function approve(User $user, Event $event): bool
    {
        $nextApprover = $event->getNextApprover();
        return $nextApprover && $user->hasRole($nextApprover);
    }

    /**
     * Determine whether the user can reject the event.
     */
    public function reject(User $user, Event $event): bool
    {
        // Any approver in the chain can reject
        $approverRoles = ['president', 'patron', 'hod', 'sa'];
        return in_array($user->role, $approverRoles) && $event->isPending();
    }

    /**
     * Determine whether the user can add items to the event.
     */
    public function addItems(User $user, Event $event): bool
    {
        // Only the student who submitted the event can add items
        // And only if it's still pending
        return $user->isStudent() && 
               $event->student_id === $user->id && 
               $event->isPending();
    }

    /**
     * Determine whether the user can view event items.
     */
    public function viewItems(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    /**
     * Determine whether the user can approve event items.
     */
    public function approveItems(User $user, Event $event): bool
    {
        return $user->hasRole('patron');
    }

    /**
     * Determine whether the user can add graphics to the event.
     */
    public function addGraphics(User $user, Event $event): bool
    {
        // Only GD users can add graphics to approved events
        return $user->hasRole('gd') && $event->isApproved();
    }

    /**
     * Determine whether the user can add volunteers to the event.
     */
    public function addVolunteers(User $user, Event $event): bool
    {
        // Only VC users can add volunteers to approved events
        return $user->hasRole('vc') && $event->isApproved();
    }

    /**
     * Determine whether the user can view event analytics.
     */
    public function viewAnalytics(User $user, Event $event): bool
    {
        // Admins, HOD, and SA can view analytics
        return $user->isAdmin() || $user->hasRole('hod') || $user->hasRole('sa');
    }
}