<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'manifesto',
        'photo_url',
        'experience',
        'vp_name',
        'status',
        'patron_feedback',
    ];

    /**
     * Get the student who submitted this candidate profile.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all votes for this candidate.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }

    /**
     * Check if candidate profile is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if candidate profile is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if candidate profile is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending_patron';
    }

    /**
     * Get vote count for this candidate in a specific term.
     */
    public function getVoteCount(?int $termId = null): int
    {
        $query = $this->votes();
        
        if ($termId) {
            $query->where('term_id', $termId);
        }
        
        return $query->count();
    }

    /**
     * Get vote count for this candidate in the active term.
     */
    public function getActiveTermVoteCount(): int
    {
        $activeTerm = AcademicTerm::getActive();
        return $activeTerm ? $this->getVoteCount($activeTerm->id) : 0;
    }
}