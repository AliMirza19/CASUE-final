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
        'created_by_role',
        'term_id',
        'expected_date',
        'venue',
        'grand_total',
        'guest_speaker_name',
        'guest_speaker_designation',
        'guest_speaker_profile_link',
        'faculty_mentor_id',
        'status',
        'rejection_reason',
        'president_comments',
        'patron_comments',
        'hod_comments',
        'risk_assessment',
        'signature_settings',
    ];

    protected function casts(): array
    {
        return [
            'expected_date' => 'date',
            'grand_total' => 'decimal:2',
            'risk_assessment' => 'array',
            'signature_settings' => 'array',
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
     * Get all volunteers (legacy) for this event.
     */
    public function volunteers(): HasMany
    {
        return $this->hasMany(EventVolunteer::class);
    }

    /**
     * Get all registered student volunteers assigned to this event.
     */
    public function assignedVolunteers(): HasMany
    {
        return $this->hasMany(Volunteer::class);
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
            default => null,
        };
    }

    /**
     * Get the next status after approval based on who created the event.
     */
    public function getNextStatus(): ?string
    {
        // If President created the event
        if ($this->created_by_role === 'president') {
            return match ($this->status) {
                'pending_patron' => 'pending_hod',
                'pending_hod' => 'approved',
                default => null,
            };
        }
        
        // If Patron created the event
        if ($this->created_by_role === 'patron') {
            return match ($this->status) {
                'pending_hod' => 'approved',
                default => null,
            };
        }
        
        // Normal student flow (including president's team members)
        return match ($this->status) {
            'pending_president' => 'pending_patron',
            'pending_patron' => 'pending_hod',
            'pending_hod' => 'approved',
            default => null,
        };
    }
    
    /**
     * Get initial status based on who is creating the event.
     */
    public static function getInitialStatus(string $creatorRole): string
    {
        return match ($creatorRole) {
            'president' => 'pending_patron',  // President → Patron
            'patron' => 'pending_hod',        // Patron → HOD
            default => 'pending_president',   // Everyone else → President
        };
    }
    
    /**
     * Check if this event needs president approval.
     */
    public function needsPresidentApproval(): bool
    {
        return !in_array($this->created_by_role, ['president', 'patron']);
    }

    /**
     * Calculate total amount from items.
     */
    public function calculateTotal(): float
    {
        // If event is approved, 
        // only count items BOTH Patron and HOD approved.
        if ($this->status === 'approved') {
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

    /** --- New Team Module Relationships --- **/

    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    public function photos()
    {
        return $this->hasMany(EventMedia::class)->where('media_type', 'photo');
    }

    public function videos()
    {
        return $this->hasMany(EventMedia::class)->whereIn('media_type', ['video', 'highlight']);
    }

    public function documents()
    {
        return $this->hasMany(EventDocument::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(EventSocialLink::class);
    }

    public function decorationPlan()
    {
        return $this->hasOne(EventDecorationPlan::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}