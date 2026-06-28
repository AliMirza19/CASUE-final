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
        'term_code',
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

    /**
     * Suggest next term details based on the YYT pattern.
     */
    public static function suggestNextTerm(): array
    {
        $last = self::orderBy('term_code', 'desc')->first();
        
        if (!$last) {
            return [
                'term_code' => '261',
                'term_name' => '261 - Spring 2026',
                'start_date' => date('Y-02-01'),
                'end_date' => date('Y-05-31')
            ];
        }

        $lastCode = $last->term_code;
        $year = (int)substr($lastCode, 0, 2);
        $termNum = (int)substr($lastCode, 2, 1);

        if ($termNum >= 3) {
            $nextYear = $year + 1;
            $nextTermNum = 1;
        } else {
            $nextYear = $year;
            $nextTermNum = $termNum + 1;
        }

        $nextCode = (string)$nextYear . (string)$nextTermNum;
        $termNames = [1 => 'Spring', 2 => 'Summer', 3 => 'Fall'];
        $fullYear = "20" . $nextYear;
        
        $termLabel = $termNames[$nextTermNum];
        $name = "{$nextCode} - {$termLabel} {$fullYear}";

        $startDate = \Carbon\Carbon::parse($last->end_date)->addDay();
        $endDate = $startDate->copy()->addMonths(4)->subDay();

        return [
            'term_code' => $nextCode,
            'term_name' => $name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
    }
}