<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'item_name',
        'quantity',
        'unit_rate',
        'total_amount',
        'is_approved_by_patron',
        'is_approved_by_hod',
        'patron_comment',
        'hod_comment',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'is_approved_by_patron' => 'boolean',
            'is_approved_by_hod' => 'boolean',
        ];
    }

    /**
     * Get the event this item belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Calculate total amount based on quantity and unit rate.
     */
    public function calculateTotal(): float
    {
        return $this->quantity * $this->unit_rate;
    }

    /**
     * Update total amount based on quantity and unit rate.
     */
    public function updateTotal(): void
    {
        $this->update(['total_amount' => $this->calculateTotal()]);
    }

    /**
     * Boot method to automatically calculate total on save.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_amount = $item->calculateTotal();
        });

        static::saved(function ($item) {
            // Update event grand total when item is saved
            $item->event->updateGrandTotal();
        });

        static::deleted(function ($item) {
            // Update event grand total when item is deleted
            $item->event->updateGrandTotal();
        });
    }
}