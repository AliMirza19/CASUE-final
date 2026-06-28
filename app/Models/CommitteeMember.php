<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = ['committee_id', 'faculty_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_user_id');
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(SelectionCommittee::class, 'committee_id');
    }
}
