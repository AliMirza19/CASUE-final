<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VolunteerPoolNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $message;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, $message, $type = 'info')
    {
        $this->student = $student;
        $this->message = $message;
        $this->type = $type;
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
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => route('vc.search-students'),
        ];
    }
}
