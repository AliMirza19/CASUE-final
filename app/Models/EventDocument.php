<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDocument extends Model
{
    protected $fillable = [
        'event_id', 'uploaded_by', 'doc_type', 'file_path',
        'original_filename', 'description', 'visible_to_roles',
    ];

    protected $casts = [
        'visible_to_roles' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isVisibleTo(string $role): bool
    {
        return in_array($role, $this->visible_to_roles ?? []);
    }
}
