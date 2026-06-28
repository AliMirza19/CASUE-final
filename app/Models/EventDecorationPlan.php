<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDecorationPlan extends Model
{
    protected $fillable = [
        'event_id', 'created_by', 'plan_description', 'material_list',
        'estimated_budget', 'status', 'setup_photos', 'notes',
    ];

    protected $casts = [
        'material_list'  => 'array',
        'setup_photos'   => 'array',
        'estimated_budget' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'not_started' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Not Started</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>',
            'done'        => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Done</span>',
            default       => '',
        };
    }
}
