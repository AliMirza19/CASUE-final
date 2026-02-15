<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVolunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'vc_id',
        'volunteer_name',
        'volunteer_contact',
        'role_description',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public $timestamps = false; // Using assigned_at instead

    /**
     * Get the event this volunteer is assigned to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the volunteer coordinator who assigned this volunteer.
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vc_id');
    }
}