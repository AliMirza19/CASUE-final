<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'voting_enabled',
        'voting_start_date',
        'voting_end_date',
    ];

    protected function casts(): array
    {
        return [
            'voting_enabled' => 'boolean',
            'voting_start_date' => 'datetime',
            'voting_end_date' => 'datetime',
        ];
    }

    /**
     * Get the academic term for this election setting.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    /**
     * Check if voting is currently active.
     */
    public function isVotingActive(): bool
    {
        if (!$this->voting_enabled) {
            return false;
        }

        $now = now();
        return $now >= $this->voting_start_date && $now <= $this->voting_end_date;
    }

    /**
     * Check if voting period has ended.
     */
    public function hasVotingEnded(): bool
    {
        return $this->voting_end_date && now() > $this->voting_end_date;
    }

    /**
     * Check if voting period has not started yet.
     */
    public function isVotingUpcoming(): bool
    {
        return $this->voting_start_date && now() < $this->voting_start_date;
    }

    /**
     * Enable voting for a specific period.
     */
    public function enableVoting(\DateTime $startDate, \DateTime $endDate): void
    {
        $this->update([
            'voting_enabled' => true,
            'voting_start_date' => $startDate,
            'voting_end_date' => $endDate,
        ]);
    }

    /**
     * Disable voting.
     */
    public function disableVoting(): void
    {
        $this->update(['voting_enabled' => false]);
    }
}