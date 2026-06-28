<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'gender',
        'cnic_passport',
        'dob',
        'mobile_number',
        'address',
        'province',
        'city',
        'contract_type',
        'academic_rank',
        'joining_date',
        'highest_degree_name',
        'highest_degree_type',
        'field_of_study',
        'degree_country',
        'university_name',
        'degree_start_date',
        'degree_end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
