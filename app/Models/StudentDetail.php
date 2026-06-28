<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'admission_date',
        'nationality',
        'passport_number',
        'dob',
        'domicile_district',
        'domicile_province',
        'mailing_address',
        'city',
        'ssc_degree_name',
        'ssc_board_name',
        'ssc_total_marks',
        'ssc_obtained_marks',
        'hssc_degree_name',
        'hssc_nomenclature',
        'hssc_board_name',
        'hssc_total_marks',
        'hssc_obtained_marks',
    ];

    /**
     * Get the user that owns the student details.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
