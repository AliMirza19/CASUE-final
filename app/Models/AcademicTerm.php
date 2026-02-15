<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_name',
        'status',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the term name (alias for term_name).
     */
    public function getNameAttribute(): string
    {
        return $this->term_name;
    }

    /**
     * Get all users associated with this term.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'current_term_id');
    }

    /**
     * Get all events for this term.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'term_id');
    }

    /**
     * Get the budget for this term.
     */
    public function budget(): HasOne
    {
        return $this->hasOne(Budget::class, 'term_id');
    }

    /**
     * Get the election settings for this term.
     */
    public function electionSetting(): HasOne
    {
        return $this->hasOne(ElectionSetting::class, 'term_id');
    }

    /**
     * Get all votes for this term.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'term_id');
    }

    /**
     * Check if this term is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the active term.
     */
    public static function getActive(): ?self
    {
        return self::where('status', 'active')->first();
    }

    /**
     * Set this term as active and deactivate others.
     */
    public function setAsActive(): void
    {
        // Deactivate all other terms
        self::where('id', '!=', $this->id)->update(['status' => 'inactive']);
        
        // Activate this term
        $this->update(['status' => 'active']);
    }
}