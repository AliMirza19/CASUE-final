<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reg_id',
        'name',
        'email',
        'password',
        'role',
        'password_changed',
        'password_changed',
        'current_term_id',
        'cnic',
        'contact_number',
        'father_name',
        'current_semester',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'password_changed' => 'boolean',
        ];
    }

    /**
     * Get the current academic term for this user.
     */
    public function currentTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'current_term_id');
    }

    /**
     * Get all events submitted by this user (if student).
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'student_id');
    }

    /**
     * Get the candidate profile for this user (if student).
     */
    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class, 'student_id');
    }

    /**
     * Get all activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all votes cast by this user (if student).
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'student_id');
    }

    /**
     * Get all graphics designed by this user (if GD).
     */
    public function graphics(): HasMany
    {
        return $this->hasMany(EventGraphic::class, 'gd_id');
    }

    /**
     * Get all volunteer assignments made by this user (if VC).
     */
    public function volunteerAssignments(): HasMany
    {
        return $this->hasMany(EventVolunteer::class, 'vc_id');
    }

    /**
     * Get all announcements created by this user.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user needs to change password.
     */
    public function needsPasswordChange(): bool
    {
        return !$this->password_changed;
    }

    /**
     * Check if user is faculty.
     */
    public function isFaculty(): bool
    {
        return $this->hasRole('faculty');
    }

    /**
     * Get the display role for the user.
     * If faculty user is appointed as HOD/Patron, return that role.
     */
    public function getDisplayRole(): string
    {
        if ($this->isAppointedHod()) {
            return 'HOD';
        }
        
        if ($this->isAppointedPatron()) {
            return 'Patron';
        }
        
        return ucfirst($this->role);
    }

    /**
     * Get the display role color class for badges.
     */
    public function getDisplayRoleColor(): string
    {
        if ($this->isAppointedHod()) {
            return 'bg-orange-100 text-orange-800';
        }
        
        if ($this->isAppointedPatron()) {
            return 'bg-purple-100 text-purple-800';
        }
        
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800',
            'president' => 'bg-blue-100 text-blue-800',
            'student' => 'bg-green-100 text-green-800',
            'sa' => 'bg-indigo-100 text-indigo-800',
            'vc' => 'bg-pink-100 text-pink-800',
            'gd' => 'bg-yellow-100 text-yellow-800',
            'faculty' => 'bg-teal-100 text-teal-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if faculty user is appointed as HOD for active term.
     */
    public function isAppointedHod(): bool
    {
        $activeTerm = AcademicTerm::getActive();
        if (!$activeTerm) {
            return false;
        }
        
        $assignment = RoleAssignment::getCurrentHod($activeTerm->id);
        return $assignment && $assignment->user_id === $this->id;
    }

    /**
     * Check if faculty user is appointed as Patron for active term.
     */
    public function isAppointedPatron(): bool
    {
        $activeTerm = AcademicTerm::getActive();
        if (!$activeTerm) {
            return false;
        }
        
        $assignment = RoleAssignment::getCurrentPatron($activeTerm->id);
        return $assignment && $assignment->user_id === $this->id;
    }
}
