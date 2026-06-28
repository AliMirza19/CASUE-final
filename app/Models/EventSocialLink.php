<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSocialLink extends Model
{
    protected $fillable = [
        'event_id', 'posted_by', 'platform', 'post_url',
        'status', 'posted_at', 'notes',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function getPlatformIconAttribute(): string
    {
        return match($this->platform) {
            'instagram' => '📸',
            'linkedin'  => '💼',
            'facebook'  => '📘',
            'twitter'   => '🐦',
            'youtube'   => '🎥',
            'whatsapp'  => '💬',
            default     => '🔗',
        };
    }
}
