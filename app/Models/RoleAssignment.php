<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'term_id',
        'role',
        'assigned_by',
        'assigned_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user assigned to this role.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the term for this assignment.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    /**
     * Get the user who made this assignment.
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get current HOD for a term.
     */
    public static function getCurrentHod($termId)
    {
        return self::where('term_id', $termId)
            ->where('role', 'hod')
            ->where('is_active', true)
            ->with('user')
            ->first();
    }

    /**
     * Get current Patron for a term.
     */
    public static function getCurrentPatron($termId)
    {
        return self::where('term_id', $termId)
            ->where('role', 'patron')
            ->where('is_active', true)
            ->with('user')
            ->first();
    }

    /**
     * Get previous term's HOD.
     */
    public static function getPreviousHod($currentTermId)
    {
        $previousTerm = AcademicTerm::where('id', '<', $currentTermId)
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$previousTerm) {
            return null;
        }
        
        return self::where('term_id', $previousTerm->id)
            ->where('role', 'hod')
            ->where('is_active', true)
            ->with('user')
            ->first();
    }

    /**
     * Get previous term's Patron.
     */
    public static function getPreviousPatron($currentTermId)
    {
        $previousTerm = AcademicTerm::where('id', '<', $currentTermId)
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$previousTerm) {
            return null;
        }
        
        return self::where('term_id', $previousTerm->id)
            ->where('role', 'patron')
            ->where('is_active', true)
            ->with('user')
            ->first();
    }

    /**
     * Assign HOD to a term.
     * Automatically transfers pending events from previous HOD.
     */
    public static function assignHod($userId, $termId, $assignedBy = null)
    {
        // Get previous HOD assignment for this term (if any)
        $previousHod = self::where('term_id', $termId)
            ->where('role', 'hod')
            ->where('is_active', true)
            ->first();
        
        // Deactivate any existing HOD for this term
        self::where('term_id', $termId)
            ->where('role', 'hod')
            ->update(['is_active' => false]);
        
        // Create new assignment
        $assignment = self::create([
            'user_id' => $userId,
            'term_id' => $termId,
            'role' => 'hod',
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
            'is_active' => true,
        ]);
        
        // Transfer pending events from previous HOD to new HOD
        // Events that are still pending_hod should remain accessible to new HOD
        // No need to change anything - events are filtered by term_id and status
        
        return $assignment;
    }

    /**
     * Assign Patron to a term.
     * Automatically transfers pending events from previous Patron.
     */
    public static function assignPatron($userId, $termId, $assignedBy = null)
    {
        // Get previous Patron assignment for this term (if any)
        $previousPatron = self::where('term_id', $termId)
            ->where('role', 'patron')
            ->where('is_active', true)
            ->first();
        
        // Deactivate any existing Patron for this term
        self::where('term_id', $termId)
            ->where('role', 'patron')
            ->update(['is_active' => false]);
        
        // Create new assignment
        $assignment = self::create([
            'user_id' => $userId,
            'term_id' => $termId,
            'role' => 'patron',
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
            'is_active' => true,
        ]);
        
        // Transfer pending events from previous Patron to new Patron
        // Events that are still pending_patron should remain accessible to new Patron
        // No need to change anything - events are filtered by term_id and status
        
        return $assignment;
    }

    /**
     * Get assignment history for a role.
     */
    public static function getHistory($role, $limit = 10)
    {
        return self::where('role', $role)
            ->with(['user', 'term', 'assignedByUser'])
            ->orderBy('assigned_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
