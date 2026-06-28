<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDirectMessage extends Notification
{
    use Queueable;

    protected $message;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
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
            'type' => 'direct_message',
            'message' => 'New message from ' . $this->sender->name,
            'sender_id' => $this->sender->id,
            'message_text' => $this->message->message_text,
            'action_url' => route('direct-chat.index', ['user_id' => $this->sender->id]),
        ];
    }
}
