<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'total_amount',
        'remaining_amount',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Get the academic term for this budget.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    /**
     * Deduct amount from remaining budget.
     */
    public function deductAmount(float $amount): bool
    {
        if ($this->remaining_amount >= $amount) {
            $this->decrement('remaining_amount', $amount);
            return true;
        }
        return false;
    }

    /**
     * Add amount back to remaining budget.
     */
    public function addAmount(float $amount): void
    {
        $this->increment('remaining_amount', $amount);
    }

    /**
     * Check if budget has sufficient funds.
     */
    public function hasSufficientFunds(float $amount): bool
    {
        return $this->remaining_amount >= $amount;
    }

    /**
     * Get the spent amount.
     */
    public function getSpentAmount(): float
    {
        return $this->total_amount - $this->remaining_amount;
    }

    /**
     * Get the percentage spent.
     */
    public function getSpentPercentage(): float
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->getSpentAmount() / $this->total_amount) * 100;
    }

    /**
     * Check if budget is locked.
     */
    public function isLocked(): bool
    {
        return $this->is_locked;
    }

    /**
     * Lock the budget.
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the budget.
     */
    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    /**
     * Set total budget amount and update remaining amount.
     */
    public function setTotalAmount(float $amount): void
    {
        $spent = $this->getSpentAmount();
        $this->update([
            'total_amount' => $amount,
            'remaining_amount' => $amount - $spent,
        ]);
    }

    /**
     * Get spent amount for a specific term.
     */
    public function getSpentAmountForTerm(int $termId): float
    {
        return Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->sum('grand_total');
    }

    /**
     * Get spending trend for the last N months.
     */
    public function getSpendingTrend(int $months = 6): array
    {
        $startDate = now()->subMonths($months);
        
        return Event::where('term_id', $this->term_id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(grand_total) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'year' => $item->year,
                    'total' => (float) $item->total,
                    'label' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year))
                ];
            })
            ->toArray();
    }

    /**
     * Get top expensive events for this term.
     */
    public function getTopExpensiveEvents(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Event::with('student')
            ->where('term_id', $this->term_id)
            ->where('status', 'approved')
            ->orderBy('grand_total', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if spending is over threshold percentage.
     */
    public function isOverSpendingThreshold(float $threshold = 80.0): bool
    {
        return $this->getSpentPercentage() > $threshold;
    }

    /**
     * Get comprehensive financial summary.
     */
    public function getFinancialSummary(): array
    {
        $spentAmount = $this->getSpentAmount();
        $spentPercentage = $this->getSpentPercentage();
        
        return [
            'term_info' => [
                'id' => $this->term->id,
                'name' => $this->term->term_name,
                'start_date' => $this->term->start_date,
                'end_date' => $this->term->end_date,
                'is_active' => $this->term->isActive()
            ],
            'budget_overview' => [
                'total_allocation' => (float) $this->total_amount,
                'total_spent' => $spentAmount,
                'remaining_balance' => (float) $this->remaining_amount,
                'spent_percentage' => $spentPercentage,
                'is_over_threshold' => $this->isOverSpendingThreshold()
            ],
            'spending_breakdown' => [
                'approved_events_count' => Event::where('term_id', $this->term_id)->where('status', 'approved')->count(),
                'pending_events_count' => Event::where('term_id', $this->term_id)->where('status', 'like', 'pending_%')->count(),
                'average_event_cost' => $this->getAverageEventCost(),
                'highest_event_cost' => $this->getHighestEventCost(),
                'lowest_event_cost' => $this->getLowestEventCost()
            ],
            'top_events' => $this->getTopExpensiveEvents(5)->map(function ($event) {
                return [
                    'title' => $event->title,
                    'cost' => (float) $event->grand_total,
                    'date' => $event->expected_date,
                    'student' => $event->student->name
                ];
            })->toArray(),
            'alerts' => $this->getBudgetAlerts()
        ];
    }

    /**
     * Compare spending with previous terms.
     */
    public function compareWithPreviousTerms(int $count = 3): array
    {
        $previousTerms = AcademicTerm::where('id', '!=', $this->term_id)
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get();

        return $previousTerms->map(function ($term) {
            $termBudget = Budget::where('term_id', $term->id)->first();
            $totalSpent = Event::where('term_id', $term->id)
                ->where('status', 'approved')
                ->sum('grand_total');
            
            $efficiency = $termBudget && $termBudget->total_amount > 0 
                ? (($termBudget->total_amount - $totalSpent) / $termBudget->total_amount) * 100 
                : 0;

            return [
                'term_name' => $term->term_name,
                'total_budget' => $termBudget ? (float) $termBudget->total_amount : 0,
                'total_spent' => (float) $totalSpent,
                'efficiency' => round($efficiency, 2)
            ];
        })->toArray();
    }

    /**
     * Get average event cost for this term.
     */
    private function getAverageEventCost(): float
    {
        return Event::where('term_id', $this->term_id)
            ->where('status', 'approved')
            ->avg('grand_total') ?? 0;
    }

    /**
     * Get highest event cost for this term.
     */
    private function getHighestEventCost(): float
    {
        return Event::where('term_id', $this->term_id)
            ->where('status', 'approved')
            ->max('grand_total') ?? 0;
    }

    /**
     * Get lowest event cost for this term.
     */
    private function getLowestEventCost(): float
    {
        return Event::where('term_id', $this->term_id)
            ->where('status', 'approved')
            ->min('grand_total') ?? 0;
    }

    /**
     * Get budget alerts based on spending thresholds.
     */
    private function getBudgetAlerts(): array
    {
        $alerts = [];
        $spentPercentage = $this->getSpentPercentage();

        if ($spentPercentage >= 90) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Critical: Budget utilization has reached ' . round($spentPercentage, 1) . '%',
                'severity' => 'high'
            ];
        } elseif ($spentPercentage >= 80) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Warning: Budget utilization has reached ' . round($spentPercentage, 1) . '%',
                'severity' => 'medium'
            ];
        } elseif ($spentPercentage >= 70) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'Notice: Budget utilization is at ' . round($spentPercentage, 1) . '%',
                'severity' => 'low'
            ];
        }

        if ($this->remaining_amount <= 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Budget exhausted: No remaining funds available',
                'severity' => 'high'
            ];
        }

        return $alerts;
    }
}