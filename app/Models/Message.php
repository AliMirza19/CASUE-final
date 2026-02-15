<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message_text',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get conversation between two users with proper ordering.
     */
    public static function getConversation(int $user1Id, int $user2Id)
    {
        return self::with(['sender', 'receiver'])
            ->where(function ($query) use ($user1Id, $user2Id) {
                $query->where(function ($q) use ($user1Id, $user2Id) {
                    $q->where('sender_id', $user1Id)
                      ->where('receiver_id', $user2Id);
                })->orWhere(function ($q) use ($user1Id, $user2Id) {
                    $q->where('sender_id', $user2Id)
                      ->where('receiver_id', $user1Id);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Mark messages as read (WhatsApp style).
     */
    public static function markAsRead(int $senderId, int $receiverId): void
    {
        self::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread message count.
     */
    public static function getUnreadCount(int $receiverId, int $senderId = null): int
    {
        $query = self::where('receiver_id', $receiverId)
                     ->where('is_read', false);
        
        if ($senderId) {
            $query->where('sender_id', $senderId);
        }
        
        return $query->count();
    }

    /**
     * Get formatted time for display (WhatsApp style).
     */
    public function getFormattedTimeAttribute(): string
    {
        $now = Carbon::now();
        $messageTime = $this->created_at;
        
        if ($messageTime->isToday()) {
            return $messageTime->format('g:i A');
        } elseif ($messageTime->isYesterday()) {
            return 'Yesterday ' . $messageTime->format('g:i A');
        } elseif ($messageTime->diffInDays($now) <= 7) {
            return $messageTime->format('l g:i A'); // Monday 2:30 PM
        } else {
            return $messageTime->format('M d, Y g:i A');
        }
    }

    /**
     * Check if message is from current user.
     */
    public function isFromUser(int $userId): bool
    {
        return $this->sender_id === $userId;
    }

    /**
     * Get read status for display.
     */
    public function getReadStatusAttribute(): string
    {
        return $this->is_read ? 'read' : 'unread';
    }
}