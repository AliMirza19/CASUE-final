<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMessage extends Model
{
    use HasFactory;

    protected $fillable = ['committee_id', 'sender_id', 'message'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(SelectionCommittee::class, 'committee_id');
    }
}
