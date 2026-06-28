<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserImportResult extends Notification
{
    use Queueable;

    protected $type; // 'student', 'faculty', or 'bulk'
    protected $successCount;
    protected $failedCount;
    protected $method; // 'single' or 'bulk'
    protected $userName; // Name of the user added (for single adds)
    protected $errors; // Error details (for failures)

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, int $successCount, int $failedCount = 0, string $method = 'single', ?string $userName = null, array $errors = [])
    {
        $this->type = $type;
        $this->successCount = $successCount;
        $this->failedCount = $failedCount;
        $this->method = $method;
        $this->userName = $userName;
        $this->errors = $errors;
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
        $roleLabel = match($this->type) {
            'student' => 'Student',
            'faculty' => 'Faculty',
            'bulk' => 'User',
            default => 'User',
        };

        // Build the notification message
        if ($this->method === 'single') {
            if ($this->successCount > 0) {
                $message = "✅ New {$roleLabel} added successfully: {$this->userName}";
                $notifType = 'success';
            } else {
                $errorMsg = !empty($this->errors) ? $this->errors[0] : 'Unknown error';
                $message = "❌ Failed to add {$roleLabel}" . ($this->userName ? " ({$this->userName})" : "") . ": {$errorMsg}";
                $notifType = 'error';
            }
        } else {
            // Bulk upload
            $parts = [];
            if ($this->successCount > 0) {
                $parts[] = "{$this->successCount} {$roleLabel}(s) added";
            }
            if ($this->failedCount > 0) {
                $parts[] = "{$this->failedCount} failed";
            }
            
            if ($this->successCount > 0 && $this->failedCount === 0) {
                $message = "✅ Bulk Upload: " . implode(', ', $parts);
                $notifType = 'success';
            } elseif ($this->successCount > 0 && $this->failedCount > 0) {
                $message = "⚠️ Bulk Upload: " . implode(', ', $parts);
                $notifType = 'warning';
            } else {
                $message = "❌ Bulk Upload Failed: No {$roleLabel}(s) were added. {$this->failedCount} row(s) had errors.";
                $notifType = 'error';
            }
        }

        return [
            'type' => 'user_import',
            'import_type' => $this->type,
            'method' => $this->method,
            'message' => $message,
            'success_count' => $this->successCount,
            'failed_count' => $this->failedCount,
            'notification_type' => $notifType,
            'action_url' => route('admin.users.index'),
        ];
    }
}
