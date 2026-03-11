<?php

namespace App\Services;

use App\Models\B2bPartner;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use App\Models\IncomeRecord;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDataService
{
    /**
     * Get period start/end dates based on period type.
     * 
     * @param string $period 'day', 'week', or 'month'
     */
    public function getPeriodDates(string $period = 'month'): array
    {
        return match ($period) {
            'day' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
                'previous_start' => now()->subDay()->startOfDay(),
                'previous_end' => now()->subDay()->endOfDay(),
                'label' => 'Today',
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
                'previous_start' => now()->subWeek()->startOfWeek(),
                'previous_end' => now()->subWeek()->endOfWeek(),
                'label' => 'This Week',
            ],
            default => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'previous_start' => now()->subMonth()->startOfMonth(),
                'previous_end' => now()->subMonth()->endOfMonth(),
                'label' => now()->format('F Y'),
            ],
        };
    }

    /**
     * Get property summary list for dashboard with comparison.
     */
    public function getPropertySummaryList(array $propertyIds = [], string $period = 'month'): array
    {
        $dates = $this->getPeriodDates($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        $prevStart = $dates['previous_start'];
        $prevEnd = $dates['previous_end'];

        $properties = Property::when(!empty($propertyIds), fn($q) => $q->whereIn('id', $propertyIds))
            ->with('accommodations')
            ->get()
            ->map(function ($property) use ($startDate, $endDate, $prevStart, $prevEnd) {
                // Current period
                $income = IncomeRecord::forProperty($property->id)->forDateRange($startDate, $endDate)->sum('amount');
                $expenses = ExpenseRecord::forProperty($property->id)->forDateRange($startDate, $endDate)->sum('amount');
                $profit = $income - $expenses;

                // Previous period for comparison
                $prevIncome = IncomeRecord::forProperty($property->id)->forDateRange($prevStart, $prevEnd)->sum('amount');
                $prevExpenses = ExpenseRecord::forProperty($property->id)->forDateRange($prevStart, $prevEnd)->sum('amount');
                $prevProfit = $prevIncome - $prevExpenses;

                // Calculate changes
                $incomeChange = $prevIncome > 0 ? round((($income - $prevIncome) / $prevIncome) * 100, 1) : ($income > 0 ? 100 : 0);
                $profitChange = $prevProfit > 0 ? round((($profit - $prevProfit) / $prevProfit) * 100, 1) : ($profit > 0 ? 100 : 0);

                // Outstanding
                $outstanding = IncomeRecord::forProperty($property->id)
                    ->forDateRange($startDate, $endDate)
                    ->where('payment_status', '!=', 'paid')
                    ->sum(DB::raw('amount - paid_amount'));

                // Booking count
                $bookingCount = IncomeRecord::forProperty($property->id)
                    ->forDateRange($startDate, $endDate)
                    ->whereIn('income_type', ['booking', 'b2b_booking'])
                    ->count();

                return [
                    'id' => $property->id,
                    'uuid' => $property->uuid,
                    'name' => $property->name,
                    'accommodation_count' => $property->accommodations->count(),
                    'income' => round($income, 2),
                    'expenses' => round($expenses, 2),
                    'profit' => round($profit, 2),
                    'margin' => $income > 0 ? round(($profit / $income) * 100, 1) : 0,
                    'outstanding' => round($outstanding, 2),
                    'booking_count' => $bookingCount,
                    'income_change' => $incomeChange,
                    'profit_change' => $profitChange,
                ];
            })
            ->sortByDesc('income')
            ->values()
            ->toArray();

        // Calculate totals
        $totals = [
            'income' => round(array_sum(array_column($properties, 'income')), 2),
            'expenses' => round(array_sum(array_column($properties, 'expenses')), 2),
            'profit' => round(array_sum(array_column($properties, 'profit')), 2),
            'outstanding' => round(array_sum(array_column($properties, 'outstanding')), 2),
            'booking_count' => array_sum(array_column($properties, 'booking_count')),
        ];
        $totals['margin'] = $totals['income'] > 0 ? round(($totals['profit'] / $totals['income']) * 100, 1) : 0;

        return [
            'period' => $dates['label'],
            'period_type' => $period,
            'properties' => $properties,
            'totals' => $totals,
        ];
    }

    /**
     * Get executive summary for multiple properties.
     */
    public function getExecutiveSummary(array $propertyIds = [], ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $incomeQuery = IncomeRecord::forDateRange($startDate, $endDate);
        $expenseQuery = ExpenseRecord::forDateRange($startDate, $endDate);

        if (!empty($propertyIds)) {
            $incomeQuery->whereIn('property_id', $propertyIds);
            $expenseQuery->whereIn('property_id', $propertyIds);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpenses = $expenseQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        // Payment status breakdown
        $paidIncome = IncomeRecord::forDateRange($startDate, $endDate)
            ->when(!empty($propertyIds), fn($q) => $q->whereIn('property_id', $propertyIds))
            ->sum('paid_amount');

        $unpaidIncome = $totalIncome - $paidIncome;

        // Outstanding receivables
        $outstandingReceivables = IncomeRecord::forDateRange($startDate, $endDate)
            ->when(!empty($propertyIds), fn($q) => $q->whereIn('property_id', $propertyIds))
            ->where('payment_status', '!=', 'paid')
            ->sum(DB::raw('amount - paid_amount'));

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'label' => $startDate->format('M d') . ' - ' . $endDate->format('M d, Y'),
            ],
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'profit_margin' => $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0,
            'paid_income' => round($paidIncome, 2),
            'unpaid_income' => round($unpaidIncome, 2),
            'paid_ratio' => $totalIncome > 0 ? round(($paidIncome / $totalIncome) * 100, 2) : 0,
            'outstanding_receivables' => round($outstandingReceivables, 2),
        ];
    }

    /**
     * Get property-wise metrics for dashboards.
     */
    public function getPropertyWiseMetrics($propertyId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $income = IncomeRecord::forProperty($propertyId)->forDateRange($startDate, $endDate)->sum('amount');
        $expenses = ExpenseRecord::forProperty($propertyId)->forDateRange($startDate, $endDate)->sum('amount');

        // Get income breakdown by type
        $incomeByType = IncomeRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->select('income_type', DB::raw('SUM(amount) as total'))
            ->groupBy('income_type')
            ->pluck('total', 'income_type')
            ->toArray();

        // Get expense breakdown by category
        $expenseByCategory = ExpenseRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->join('expense_categories', 'expense_records.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', 'expense_categories.color', DB::raw('SUM(expense_records.amount) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->get()
            ->toArray();

        // Monthly trend (last 6 months)
        $monthlyTrend = $this->getMonthlyTrend($propertyId, 6);

        return [
            'property_id' => $propertyId,
            'total_income' => round($income, 2),
            'total_expenses' => round($expenses, 2),
            'net_profit' => round($income - $expenses, 2),
            'income_by_type' => $incomeByType,
            'expense_by_category' => $expenseByCategory,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Get accommodation-wise metrics.
     */
    public function getAccommodationWiseMetrics($propertyId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $accommodations = PropertyAccommodation::where('property_id', $propertyId)
            ->with('predefinedType')
            ->get()
            ->map(function ($accommodation) use ($startDate, $endDate) {
                $income = IncomeRecord::where('accommodation_id', $accommodation->id)
                    ->forDateRange($startDate, $endDate)
                    ->sum('amount');

                $expenses = ExpenseRecord::where('accommodation_id', $accommodation->id)
                    ->forDateRange($startDate, $endDate)
                    ->sum('amount');

                return [
                    'id' => $accommodation->id,
                    'name' => $accommodation->display_name,
                    'income' => round($income, 2),
                    'expenses' => round($expenses, 2),
                    'net_contribution' => round($income - $expenses, 2),
                ];
            })
            ->sortByDesc('net_contribution')
            ->values()
            ->toArray();

        $totalIncome = array_sum(array_column($accommodations, 'income'));

        // Calculate contribution percentage
        foreach ($accommodations as &$acc) {
            $acc['contribution_pct'] = $totalIncome > 0
                ? round(($acc['income'] / $totalIncome) * 100, 2)
                : 0;
        }

        return [
            'property_id' => $propertyId,
            'accommodations' => $accommodations,
            'total_income' => round($totalIncome, 2),
        ];
    }

    /**
     * Get B2B dashboard data.
     */
    public function getB2bDashboardData(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $partners = B2bPartner::where('status', 'active')
            ->get()
            ->map(function ($partner) use ($startDate, $endDate) {
                $revenue = IncomeRecord::where('b2b_partner_id', $partner->id)
                    ->forDateRange($startDate, $endDate)
                    ->sum('amount');

                $paidAmount = IncomeRecord::where('b2b_partner_id', $partner->id)
                    ->forDateRange($startDate, $endDate)
                    ->sum('paid_amount');

                return [
                    'id' => $partner->id,
                    'uuid' => $partner->uuid,
                    'name' => $partner->partner_name,
                    'revenue' => round($revenue, 2),
                    'paid' => round($paidAmount, 2),
                    'outstanding' => round($revenue - $paidAmount, 2),
                    'transaction_count' => IncomeRecord::where('b2b_partner_id', $partner->id)
                        ->forDateRange($startDate, $endDate)
                        ->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values()
            ->toArray();

        // Aging analysis for outstanding amounts
        $agingAnalysis = $this->getOutstandingAgingAnalysis();

        return [
            'partners' => $partners,
            'total_revenue' => round(array_sum(array_column($partners, 'revenue')), 2),
            'total_outstanding' => round(array_sum(array_column($partners, 'outstanding')), 2),
            'aging_analysis' => $agingAnalysis,
        ];
    }

    /**
     * Get chart data for various chart types.
     */
    public function getChartData(string $chartType, array $filters = []): array
    {
        $propertyId = $filters['property_id'] ?? null;
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->startOfMonth();
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now()->endOfMonth();

        return match ($chartType) {
            'income_vs_expense' => $this->getIncomeVsExpenseChart($propertyId, $startDate, $endDate),
            'profit_trend' => $this->getProfitTrendChart($propertyId, 6),
            'expense_pie' => $this->getExpensePieChart($propertyId, $startDate, $endDate),
            'income_pie' => $this->getIncomePieChart($propertyId, $startDate, $endDate),
            'accommodation_bar' => $this->getAccommodationBarChart($propertyId, $startDate, $endDate),
            'b2b_ranking' => $this->getB2bRankingChart($startDate, $endDate),
            default => [],
        };
    }

    /**
     * Get monthly trend for last N months.
     */
    protected function getMonthlyTrend($propertyId, int $months = 6): array
    {
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $incomeQuery = IncomeRecord::forDateRange($startOfMonth, $endOfMonth);
            $expenseQuery = ExpenseRecord::forDateRange($startOfMonth, $endOfMonth);

            if ($propertyId) {
                $incomeQuery->forProperty($propertyId);
                $expenseQuery->forProperty($propertyId);
            }

            $income = $incomeQuery->sum('amount');
            $expenses = $expenseQuery->sum('amount');

            $trend[] = [
                'month' => $month->format('M Y'),
                'income' => round($income, 2),
                'expenses' => round($expenses, 2),
                'profit' => round($income - $expenses, 2),
            ];
        }

        return $trend;
    }

    /**
     * Get outstanding aging analysis.
     */
    protected function getOutstandingAgingAnalysis(): array
    {
        $today = now()->toDateString();

        $aging = [
            'current' => 0,      // 0-30 days
            '31_60' => 0,        // 31-60 days
            '61_90' => 0,        // 61-90 days
            'over_90' => 0,      // 90+ days
        ];

        $unpaidRecords = IncomeRecord::where('payment_status', '!=', 'paid')->get();

        foreach ($unpaidRecords as $record) {
            $daysOld = $record->transaction_date->diffInDays(now());
            $outstanding = $record->amount - $record->paid_amount;

            if ($daysOld <= 30) {
                $aging['current'] += $outstanding;
            } elseif ($daysOld <= 60) {
                $aging['31_60'] += $outstanding;
            } elseif ($daysOld <= 90) {
                $aging['61_90'] += $outstanding;
            } else {
                $aging['over_90'] += $outstanding;
            }
        }

        return array_map(fn($v) => round($v, 2), $aging);
    }

    /**
     * Income vs Expense bar chart data.
     */
    protected function getIncomeVsExpenseChart($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $incomeQuery = IncomeRecord::forDateRange($startDate, $endDate);
        $expenseQuery = ExpenseRecord::forDateRange($startDate, $endDate);

        if ($propertyId) {
            $incomeQuery->forProperty($propertyId);
            $expenseQuery->forProperty($propertyId);
        }

        return [
            'labels' => ['Income', 'Expenses'],
            'datasets' => [
                [
                    'label' => 'Amount (₹)',
                    'data' => [
                        round($incomeQuery->sum('amount'), 2),
                        round($expenseQuery->sum('amount'), 2),
                    ],
                    'backgroundColor' => ['#10B981', '#EF4444'],
                ]
            ]
        ];
    }

    /**
     * Profit trend line chart data.
     */
    protected function getProfitTrendChart($propertyId, int $months): array
    {
        $trend = $this->getMonthlyTrend($propertyId, $months);

        return [
            'labels' => array_column($trend, 'month'),
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => array_column($trend, 'income'),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Expenses',
                    'data' => array_column($trend, 'expenses'),
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
                [
                    'label' => 'Profit',
                    'data' => array_column($trend, 'profit'),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ]
            ]
        ];
    }

    /**
     * Expense breakdown pie chart data.
     */
    protected function getExpensePieChart($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $data = ExpenseRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->join('expense_categories', 'expense_records.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', 'expense_categories.color', DB::raw('SUM(expense_records.amount) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('total')->map(fn($v) => round($v, 2))->toArray(),
                    'backgroundColor' => $data->pluck('color')->toArray(),
                ]
            ]
        ];
    }

    /**
     * Income breakdown pie chart data.
     */
    protected function getIncomePieChart($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $incomeTypeColors = [
            'booking' => '#10B981',
            'rental' => '#3B82F6',
            'service' => '#8B5CF6',
            'deposit' => '#F59E0B',
            'penalty' => '#EF4444',
            'commission' => '#EC4899',
            'other' => '#6B7280',
        ];

        $data = IncomeRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->select('income_type', DB::raw('SUM(amount) as total'))
            ->groupBy('income_type')
            ->get();

        return [
            'labels' => $data->pluck('income_type')->map(fn($t) => ucfirst($t))->toArray(),
            'datasets' => [
                [
                    'data' => $data->pluck('total')->map(fn($v) => round($v, 2))->toArray(),
                    'backgroundColor' => $data->pluck('income_type')->map(fn($t) => $incomeTypeColors[$t] ?? '#6B7280')->toArray(),
                ]
            ]
        ];
    }

    /**
     * Accommodation comparison bar chart.
     */
    protected function getAccommodationBarChart($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        if (!$propertyId) {
            return ['labels' => [], 'datasets' => []];
        }

        $data = $this->getAccommodationWiseMetrics($propertyId, $startDate, $endDate);
        $accommodations = $data['accommodations'];

        return [
            'labels' => array_column($accommodations, 'name'),
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => array_column($accommodations, 'income'),
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'Expenses',
                    'data' => array_column($accommodations, 'expenses'),
                    'backgroundColor' => '#EF4444',
                ]
            ]
        ];
    }

    /**
     * B2B partner ranking chart.
     */
    protected function getB2bRankingChart(Carbon $startDate, Carbon $endDate): array
    {
        $data = $this->getB2bDashboardData($startDate, $endDate);
        $partners = array_slice($data['partners'], 0, 10); // Top 10

        return [
            'labels' => array_column($partners, 'name'),
            'datasets' => [
                [
                    'label' => 'Revenue (₹)',
                    'data' => array_column($partners, 'revenue'),
                    'backgroundColor' => '#3B82F6',
                ]
            ]
        ];
    }
}
