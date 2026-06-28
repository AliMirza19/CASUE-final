<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'roll_no',
        'father_name',
        'gender',
        'admission_date',
        'nationality',
        'cnic_number',
        'passport_number',
        'dob',
        'phone_number',
        'domicile_district',
        'domicile_province',
        'mailing_address',
        'city',
        'ssc_degree_name',
        'ssc_board_name',
        'ssc_total_marks',
        'ssc_obtained_marks',
        'hssc_degree_name',
        'hssc_degree_nomenclature',
        'hssc_board_name',
        'hssc_total_marks',
        'hssc_obtained_marks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
