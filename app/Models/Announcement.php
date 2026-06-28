<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'link_url',
        'target_role',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created this announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if announcement is active.
     */
    public function isActive(): bool
    {
        return is_null($this->expires_at) || $this->expires_at->isFuture();
    }

    /**
     * Check if announcement is targeted to a specific role.
     */
    public function isTargetedToRole(string $role): bool
    {
        return empty($this->target_role) || $this->target_role === $role;
    }

    /**
     * Get active announcements for a specific role.
     */
    public static function getActiveForRole(string $role)
    {
        return self::where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                   ->where(function ($query) use ($role) {
                        $query->whereNull('target_role')
                              ->orWhere('target_role', $role);
                   })
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get all active announcements.
     */
    public static function getActive()
    {
        return self::where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                   ->orderBy('created_at', 'desc')
                   ->get();
    }
}