<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_role',
        'action_text',
        'related_event_id',
    ];

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event related to this activity (if any).
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'related_event_id');
    }

    /**
     * Create a new activity log entry.
     */
    public static function logActivity(User $user, string $action, ?Event $event = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'user_role' => $user->role,
            'action_text' => $action,
            'related_event_id' => $event?->id,
        ]);
    }

    /**
     * Get recent activities for a user.
     */
    public static function getRecentForUser(User $user, int $limit = 10)
    {
        return self::where('user_id', $user->id)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get recent activities for a role.
     */
    public static function getRecentForRole(string $role, int $limit = 10)
    {
        return self::where('user_role', $role)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get recent system-wide activities.
     */
    public static function getRecentSystemWide(int $limit = 20)
    {
        return self::with(['user', 'event'])
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }
}