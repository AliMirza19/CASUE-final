<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Event;
use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsService
{
    /**
     * Generate comprehensive financial report for a term.
     */
    public function generateFinancialReport(int $termId): array
    {
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget) {
            return $this->getEmptyReport($termId);
        }

        $financialSummary = $budget->getFinancialSummary();
        $spendingComparison = $this->getSpendingComparison($termId);
        $historicalTrends = $this->getHistoricalTrends($termId, 6);
        $spendingEfficiency = $this->calculateSpendingEfficiency($termId);
        $topSpendingEvents = $this->getTopSpendingEvents($termId, 5);
        $budgetAlerts = $this->checkBudgetAlerts($termId);

        return [
            'summary' => $financialSummary,
            'spending_comparison' => $spendingComparison,
            'historical_trends' => $historicalTrends,
            'spending_efficiency' => $spendingEfficiency,
            'top_events' => $topSpendingEvents,
            'alerts' => $budgetAlerts,
            'generated_at' => now()->toDateTimeString(),
            'term_id' => $termId
        ];
    }

    /**
     * Get spending comparison data for charts.
     */
    public function getSpendingComparison(int $termId): array
    {
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget) {
            return [
                'total_budget' => 0,
                'total_spent' => 0,
                'remaining_balance' => 0,
                'spent_percentage' => 0
            ];
        }

        $totalSpent = $budget->getSpentAmount();
        $remaining = $budget->remaining_amount;

        return [
            'total_budget' => (float) $budget->total_amount,
            'total_spent' => $totalSpent,
            'remaining_balance' => (float) $remaining,
            'spent_percentage' => $budget->getSpentPercentage(),
            'chart_data' => [
                'labels' => ['Total Budget', 'Total Spent', 'Remaining'],
                'datasets' => [
                    [
                        'data' => [$budget->total_amount, $totalSpent, $remaining],
                        'backgroundColor' => ['#8B5CF6', '#EF4444', '#10B981'],
                        'borderWidth' => 2
                    ]
                ]
            ]
        ];
    }

    /**
     * Get historical spending trends.
     */
    public function getHistoricalTrends(int $termId, int $months = 6): array
    {
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget) {
            return ['monthly_trends' => [], 'comparison_with_previous' => []];
        }

        // Get monthly trends for current term
        $monthlyTrends = $budget->getSpendingTrend($months);
        
        // Get comparison with previous terms
        $previousComparison = $budget->compareWithPreviousTerms(3);

        return [
            'monthly_trends' => $monthlyTrends,
            'comparison_with_previous' => $previousComparison,
            'chart_data' => [
                'monthly' => [
                    'labels' => array_column($monthlyTrends, 'label'),
                    'datasets' => [
                        [
                            'label' => 'Monthly Spending',
                            'data' => array_column($monthlyTrends, 'total'),
                            'borderColor' => '#8B5CF6',
                            'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                            'tension' => 0.4
                        ]
                    ]
                ],
                'comparison' => [
                    'labels' => array_column($previousComparison, 'term_name'),
                    'datasets' => [
                        [
                            'label' => 'Total Spent',
                            'data' => array_column($previousComparison, 'total_spent'),
                            'backgroundColor' => '#8B5CF6'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Calculate spending efficiency for a term.
     */
    public function calculateSpendingEfficiency(int $termId): float
    {
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget || $budget->total_amount == 0) {
            return 0;
        }

        $totalSpent = $budget->getSpentAmount();
        $approvedEventsCount = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->count();

        if ($approvedEventsCount == 0) {
            return 100; // Perfect efficiency if no events approved yet
        }

        // Efficiency = (Budget Utilization * Event Success Rate)
        $budgetUtilization = ($totalSpent / $budget->total_amount) * 100;
        $totalEventsSubmitted = Event::where('term_id', $termId)->count();
        $eventSuccessRate = $totalEventsSubmitted > 0 
            ? ($approvedEventsCount / $totalEventsSubmitted) * 100 
            : 0;

        // Weighted efficiency calculation
        $efficiency = ($budgetUtilization * 0.6) + ($eventSuccessRate * 0.4);
        
        return round($efficiency, 2);
    }

    /**
     * Get top spending events for a term.
     */
    public function getTopSpendingEvents(int $termId, int $limit = 5): Collection
    {
        return Event::with(['student', 'term'])
            ->where('term_id', $termId)
            ->where('status', 'approved')
            ->orderBy('grand_total', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check budget alerts for a term.
     */
    public function checkBudgetAlerts(int $termId): array
    {
        $budget = Budget::where('term_id', $termId)->first();
        
        if (!$budget) {
            return [[
                'type' => 'warning',
                'message' => 'No budget allocated for this term',
                'severity' => 'medium'
            ]];
        }

        $alerts = [];
        $spentPercentage = $budget->getSpentPercentage();
        $remainingAmount = $budget->remaining_amount;

        // Budget threshold alerts
        if ($spentPercentage >= 95) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Critical Alert: ' . round($spentPercentage, 1) . '% of budget utilized',
                'severity' => 'high',
                'icon' => 'exclamation-triangle'
            ];
        } elseif ($spentPercentage >= 80) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Budget Alert: ' . round($spentPercentage, 1) . '% of budget utilized',
                'severity' => 'medium',
                'icon' => 'exclamation-circle'
            ];
        }

        // Low balance alerts
        if ($remainingAmount <= 1000 && $remainingAmount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Low Balance: Only Rs. ' . number_format($remainingAmount, 2) . ' remaining',
                'severity' => 'medium',
                'icon' => 'coins'
            ];
        } elseif ($remainingAmount <= 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Budget Exhausted: No funds remaining',
                'severity' => 'high',
                'icon' => 'ban'
            ];
        }

        // Pending events that might exceed budget
        $pendingEventsTotal = Event::where('term_id', $termId)
            ->where('status', 'like', 'pending_%')
            ->sum('grand_total');

        if ($pendingEventsTotal > $remainingAmount) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'Pending events total (Rs. ' . number_format($pendingEventsTotal, 2) . ') exceeds remaining budget',
                'severity' => 'low',
                'icon' => 'info-circle'
            ];
        }

        // If no alerts, add a positive message
        if (empty($alerts) && $spentPercentage < 70) {
            $alerts[] = [
                'type' => 'success',
                'message' => 'Budget is healthy with ' . round(100 - $spentPercentage, 1) . '% remaining',
                'severity' => 'low',
                'icon' => 'check-circle'
            ];
        }

        return $alerts;
    }

    /**
     * Get event-wise budget breakdown.
     */
    public function getEventWiseBudgetBreakdown(int $termId): array
    {
        $events = Event::with('student')
            ->where('term_id', $termId)
            ->where('status', 'approved')
            ->orderBy('grand_total', 'desc')
            ->get();

        $totalSpent = $events->sum('grand_total');

        return [
            'events' => $events->map(function ($event) use ($totalSpent) {
                $percentage = $totalSpent > 0 ? ($event->grand_total / $totalSpent) * 100 : 0;
                
                return [
                    'title' => $event->title,
                    'student' => $event->student->name,
                    'cost' => (float) $event->grand_total,
                    'percentage' => round($percentage, 2),
                    'date' => $event->expected_date,
                    'venue' => $event->venue
                ];
            })->toArray(),
            'total_spent' => (float) $totalSpent,
            'events_count' => $events->count()
        ];
    }

    /**
     * Get monthly spending statistics.
     */
    public function getMonthlySpendingStats(int $termId): array
    {
        $monthlyData = Event::where('term_id', $termId)
            ->where('status', 'approved')
            ->selectRaw('
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                COUNT(*) as events_count,
                SUM(grand_total) as total_spent,
                AVG(grand_total) as average_cost
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $monthlyData->map(function ($data) {
            return [
                'month' => $data->month,
                'year' => $data->year,
                'month_name' => date('F', mktime(0, 0, 0, $data->month, 1)),
                'events_count' => $data->events_count,
                'total_spent' => (float) $data->total_spent,
                'average_cost' => (float) $data->average_cost,
                'label' => date('M Y', mktime(0, 0, 0, $data->month, 1, $data->year))
            ];
        })->toArray();
    }

    /**
     * Get empty report structure when no budget exists.
     */
    private function getEmptyReport(int $termId): array
    {
        $term = AcademicTerm::find($termId);
        
        return [
            'summary' => [
                'term_info' => [
                    'id' => $termId,
                    'name' => $term ? $term->term_name : 'Unknown Term',
                    'start_date' => $term ? $term->start_date : null,
                    'end_date' => $term ? $term->end_date : null,
                    'is_active' => $term ? $term->isActive() : false
                ],
                'budget_overview' => [
                    'total_allocation' => 0,
                    'total_spent' => 0,
                    'remaining_balance' => 0,
                    'spent_percentage' => 0,
                    'is_over_threshold' => false
                ],
                'spending_breakdown' => [
                    'approved_events_count' => 0,
                    'pending_events_count' => 0,
                    'average_event_cost' => 0,
                    'highest_event_cost' => 0,
                    'lowest_event_cost' => 0
                ],
                'top_events' => [],
                'alerts' => [[
                    'type' => 'warning',
                    'message' => 'No budget allocated for this term',
                    'severity' => 'medium'
                ]]
            ],
            'spending_comparison' => ['total_budget' => 0, 'total_spent' => 0, 'remaining_balance' => 0],
            'historical_trends' => ['monthly_trends' => [], 'comparison_with_previous' => []],
            'spending_efficiency' => 0,
            'top_events' => collect([]),
            'alerts' => [[
                'type' => 'warning',
                'message' => 'No budget allocated for this term',
                'severity' => 'medium'
            ]],
            'generated_at' => now()->toDateTimeString(),
            'term_id' => $termId
        ];
    }
}