<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'created_by',
        'target_roles',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user who created this announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if announcement is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if announcement is targeted to a specific role.
     */
    public function isTargetedToRole(string $role): bool
    {
        return empty($this->target_roles) || in_array($role, $this->target_roles);
    }

    /**
     * Get active announcements for a specific role.
     */
    public static function getActiveForRole(string $role)
    {
        return self::where('is_active', true)
                   ->where(function ($query) use ($role) {
                       $query->whereNull('target_roles')
                             ->orWhereJsonContains('target_roles', $role);
                   })
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get all active announcements.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Activate this announcement.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate this announcement.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}