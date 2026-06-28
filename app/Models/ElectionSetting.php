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
        'registration_start',
        'registration_end',
        'voting_start',
        'voting_end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'registration_start' => 'datetime',
            'registration_end' => 'datetime',
            'voting_start' => 'datetime',
            'voting_end' => 'datetime',
            'is_active' => 'boolean',
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
     * Check if registration is currently open.
     */
    public function isRegistrationOpen(): bool
    {
        if (!$this->is_active || !$this->registration_start || !$this->registration_end) {
            return false;
        }

        $now = now();
        return $now >= $this->registration_start && $now <= $this->registration_end;
    }

    /**
     * Check if voting is currently active.
     */
    public function isVotingActive(): bool
    {
        if (!$this->is_active || !$this->voting_start || !$this->voting_end) {
            return false;
        }

        $now = now();
        return $now >= $this->voting_start && $now <= $this->voting_end;
    }

    /**
     * Get the current status text.
     */
    public function getStatus(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $now = now();

        if ($this->registration_start && $now < $this->registration_start) {
            return 'Registration Upcoming';
        }

        if ($this->isRegistrationOpen()) {
            return 'Registration Open';
        }

        if ($this->registration_end && $now < $this->voting_start) {
            return 'Awaiting Voting';
        }

        if ($this->isVotingActive()) {
            return 'Voting Live';
        }

        if ($this->voting_end && $now > $this->voting_end) {
            return 'Elections Closed';
        }

        return 'Status Unknown';
    }

    /**
     * Get status color class (Tailwind).
     */
    public function getStatusColor(): string
    {
        $status = $this->getStatus();
        
        return match($status) {
            'Registration Open' => 'bg-blue-100 text-blue-800',
            'Voting Live' => 'bg-green-100 text-green-800',
            'Registration Upcoming', 'Awaiting Voting' => 'bg-yellow-100 text-yellow-800',
            'Elections Closed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}