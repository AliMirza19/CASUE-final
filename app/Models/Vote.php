<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'candidate_id',
        'term_id',
        'voted_at',
    ];

    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
        ];
    }

    public $timestamps = false; // Using voted_at instead

    /**
     * Get the student who cast this vote.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the candidate this vote was cast for.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the academic term this vote was cast in.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    /**
     * Check if a student has already voted in a specific term.
     */
    public static function hasStudentVoted(int $studentId, int $termId): bool
    {
        return self::where('student_id', $studentId)
                   ->where('term_id', $termId)
                   ->exists();
    }
}