<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        // Check if status changed to 'approved'
        if ($event->isDirty('status') && $event->status === 'approved') {
            $this->createChatGroupsForEvent($event);
        }
    }

    protected function createChatGroupsForEvent(Event $event)
    {
        // Get all teams for the event's academic term
        $teams = \App\Models\Team::where('academic_term_id', $event->term_id)->get();
        
        // Find the President to add to all groups as admin
        $president = \App\Models\User::where('role', 'president')->first();

        foreach ($teams as $team) {
            // Create a chat group for this event and team
            $chatGroup = \App\Models\ChatGroup::firstOrCreate([
                'event_id' => $event->id,
                'name' => $event->title . ' - ' . $team->name,
            ]);

            // Add President as Admin
            if ($president) {
                \App\Models\ChatGroupMember::firstOrCreate([
                    'chat_group_id' => $chatGroup->id,
                    'user_id' => $president->id,
                ], ['role' => 'admin']);
            }

            // Add Team Members
            foreach ($team->users as $member) {
                \App\Models\ChatGroupMember::firstOrCreate([
                    'chat_group_id' => $chatGroup->id,
                    'user_id' => $member->id,
                ], ['role' => 'member']);
            }
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
