<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sr_no',
        'title',
        'gender',
        'dob',
        'province',
        'city',
        'address',
        'contract_type',
        'academic_rank',
        'joining_date',
        'leaving_date',
        'degree_name',
        'degree_type',
        'field_of_study',
        'degree_awarding_country',
        'university_name',
        'degree_start_date',
        'degree_end_date',
    ];

    /**
     * Get the user that owns the faculty details.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
