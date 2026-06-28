<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelectionCommittee extends Model
{
    use HasFactory;

    protected $fillable = ['hod_id', 'patron_id', 'is_active'];

    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function patron(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patron_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class, 'committee_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommitteeMessage::class, 'committee_id');
    }
}
