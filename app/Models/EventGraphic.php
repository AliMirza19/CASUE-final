<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventGraphic extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'gd_id',
        'design_category',
        'image_path',
        'image_link',
        'status',
        'patron_feedback',
        'annotations',
    ];

    protected function casts(): array
    {
        return [
            'annotations' => 'array',
        ];
    }

    /**
     * Get the event this graphic belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the graphics designer who created this graphic.
     */
    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gd_id');
    }

    /**
     * Check if graphic is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if graphic is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if graphic is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending_patron';
    }
}