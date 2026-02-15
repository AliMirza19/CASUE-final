<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'student_id',
        'term_id',
        'expected_date',
        'venue',
        'grand_total',
        'guest_speaker_name',
        'guest_speaker_designation',
        'faculty_mentor_id',
        'status',
        'rejection_reason',
        'president_comments',
        'patron_comments',
        'hod_comments',
        'sa_comments',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * Get the student who submitted this event.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the academic term for this event.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    /**
     * Get the faculty mentor for this event.
     */
    public function facultyMentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_mentor_id');
    }

    /**
     * Get all items for this event.
     */
    public function items(): HasMany
    {
        return $this->hasMany(EventItem::class);
    }

    /**
     * Get all graphics for this event.
     */
    public function graphics(): HasMany
    {
        return $this->hasMany(EventGraphic::class);
    }

    /**
     * Get all volunteers for this event.
     */
    public function volunteers(): HasMany
    {
        return $this->hasMany(EventVolunteer::class);
    }

    /**
     * Get all activity logs related to this event.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'related_event_id');
    }

    /**
     * Check if event is pending approval.
     */
    public function isPending(): bool
    {
        return str_starts_with($this->status, 'pending_');
    }

    /**
     * Check if event is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if event is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the next approver role based on current status.
     */
    public function getNextApprover(): ?string
    {
        return match ($this->status) {
            'pending_president' => 'president',
            'pending_patron' => 'patron',
            'pending_hod' => 'hod',
            'pending_sa' => 'sa',
            default => null,
        };
    }

    /**
     * Get the next status after approval.
     */
    public function getNextStatus(): ?string
    {
        return match ($this->status) {
            'pending_president' => 'pending_patron',
            'pending_patron' => 'pending_hod',
            'pending_hod' => 'pending_sa',
            'pending_sa' => 'approved',
            default => null,
        };
    }

    /**
     * Calculate total amount from items.
     */
    public function calculateTotal(): float
    {
        // If event is in a stage after HOD review, 
        // only count items BOTH Patron and HOD approved.
        if (in_array($this->status, ['pending_sa', 'approved'])) {
            return $this->items()
                ->where('is_approved_by_patron', true)
                ->where('is_approved_by_hod', true)
                ->sum('total_amount');
        }

        // If event is in HOD review, count items Patron approved.
        if ($this->status === 'pending_hod') {
            return $this->items()->where('is_approved_by_patron', true)->sum('total_amount');
        }
        
        // For Student, President, and Patron stages (initial review),
        // show the full requested amount (sum of all items).
        return $this->items()->sum('total_amount');
    }

    /**
     * Update grand total from items.
     */
    public function updateGrandTotal(): void
    {
        $this->update(['grand_total' => $this->calculateTotal()]);
    }
}