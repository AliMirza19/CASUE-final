<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGroupMessage extends Notification
{
    use Queueable;

    protected $message;
    protected $sender;
    protected $group;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $sender, $group)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->group = $group;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_message',
            'message' => '[' . ($this->group->event->title ?? $this->group->name) . '] New message from ' . $this->sender->name,
            'group_id' => $this->group->id,
            'message_text' => $this->message->message,
            'action_url' => route('chat.show', $this->group->id),
        ];
    }
}
