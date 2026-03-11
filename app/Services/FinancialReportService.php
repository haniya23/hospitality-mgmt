<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use App\Models\FinancialPeriod;
use App\Models\FinancialReport;
use App\Models\IncomeRecord;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Generate a weekly report for a property.
     */
    public function generateWeeklyReport($propertyId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): FinancialReport
    {
        $startDate = $startDate ?? now()->subWeek()->startOfWeek();
        $endDate = $endDate ?? now()->subWeek()->endOfWeek();

        return $this->generateReport($propertyId, 'weekly', $startDate, $endDate);
    }

    /**
     * Generate a monthly report for a property.
     */
    public function generateMonthlyReport($propertyId = null, ?int $year = null, ?int $month = null): FinancialReport
    {
        $year = $year ?? now()->subMonth()->year;
        $month = $month ?? now()->subMonth()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return $this->generateReport($propertyId, 'monthly', $startDate, $endDate);
    }

    /**
     * Generate a report for the given parameters.
     */
    public function generateReport($propertyId, string $type, Carbon $startDate, Carbon $endDate): FinancialReport
    {
        // Get or create the financial period
        $period = $type === 'weekly'
            ? FinancialPeriod::getOrCreateWeekPeriod($startDate, $propertyId)
            : FinancialPeriod::getOrCreateMonthPeriod($startDate, $propertyId);

        // Check if report already exists
        $existingReport = FinancialReport::where('financial_period_id', $period->id)
            ->where('property_id', $propertyId)
            ->where('report_type', $type)
            ->first();

        if ($existingReport && $existingReport->is_locked) {
            return $existingReport; // Cannot regenerate locked reports
        }

        // Calculate totals
        $incomeQuery = IncomeRecord::forDateRange($startDate, $endDate);
        $expenseQuery = ExpenseRecord::forDateRange($startDate, $endDate);

        if ($propertyId) {
            $incomeQuery->forProperty($propertyId);
            $expenseQuery->forProperty($propertyId);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpenses = $expenseQuery->sum('amount');

        // Calculate outstanding receivables (unpaid income)
        $receivablesQuery = IncomeRecord::where('payment_status', '!=', 'paid')
            ->forDateRange($startDate, $endDate);
        if ($propertyId) {
            $receivablesQuery->forProperty($propertyId);
        }
        $outstandingReceivables = $receivablesQuery->sum(DB::raw('amount - paid_amount'));

        // Calculate outstanding payables (unpaid expenses)
        $payablesQuery = ExpenseRecord::where('payment_status', '!=', 'paid')
            ->forDateRange($startDate, $endDate);
        if ($propertyId) {
            $payablesQuery->forProperty($propertyId);
        }
        $outstandingPayables = $payablesQuery->sum(DB::raw('amount - paid_amount'));

        // Build summary data
        $summaryData = $this->buildSummaryData($propertyId, $startDate, $endDate);

        // Create or update the report
        $reportData = [
            'property_id' => $propertyId,
            'financial_period_id' => $period->id,
            'report_type' => $type,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalIncome - $totalExpenses,
            'outstanding_receivables' => $outstandingReceivables,
            'outstanding_payables' => $outstandingPayables,
            'summary_data' => $summaryData,
            'status' => 'draft',
            'generated_by' => auth()->id(),
        ];

        if ($existingReport) {
            $existingReport->update($reportData);
            $report = $existingReport;
            // Clear existing items
            $report->items()->delete();
        } else {
            $report = FinancialReport::create($reportData);
        }

        // Add detailed line items
        $this->addReportItems($report, $propertyId, $startDate, $endDate);

        return $report;
    }

    /**
     * Build summary data for the report.
     */
    protected function buildSummaryData($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $incomeByType = IncomeRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->select('income_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('income_type')
            ->get()
            ->keyBy('income_type')
            ->toArray();

        $expenseByCategory = ExpenseRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->join('expense_categories', 'expense_records.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', 'expense_categories.color', DB::raw('SUM(expense_records.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->get()
            ->keyBy('name')
            ->toArray();

        $paymentStatusBreakdown = IncomeRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->select('payment_status', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status')
            ->toArray();

        return [
            'income_by_type' => $incomeByType,
            'expense_by_category' => $expenseByCategory,
            'payment_status_breakdown' => $paymentStatusBreakdown,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Add detailed line items to the report.
     */
    protected function addReportItems(FinancialReport $report, $propertyId, Carbon $startDate, Carbon $endDate): void
    {
        // Add income items by type
        $incomeByType = IncomeRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->select('income_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('income_type')
            ->get();

        foreach ($incomeByType as $item) {
            $report->addItem('income', $item->income_type, $item->total, $item->count);
        }

        // Add expense items by category
        $expenseByCategory = ExpenseRecord::forDateRange($startDate, $endDate)
            ->when($propertyId, fn($q) => $q->forProperty($propertyId))
            ->join('expense_categories', 'expense_records.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', DB::raw('SUM(expense_records.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->get();

        foreach ($expenseByCategory as $item) {
            $report->addItem('expense', $item->name, $item->total, $item->count);
        }
    }

    /**
     * Get property summary for a date range.
     */
    public function getPropertySummary($propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $totalIncome = IncomeRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->sum('amount');

        $totalExpenses = ExpenseRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->sum('amount');

        $paidIncome = IncomeRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->paid()
            ->sum('amount');

        $unpaidIncome = IncomeRecord::forProperty($propertyId)
            ->forDateRange($startDate, $endDate)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum(DB::raw('amount - paid_amount'));

        return [
            'property_id' => $propertyId,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($totalIncome - $totalExpenses, 2),
            'paid_income' => round($paidIncome, 2),
            'unpaid_income' => round($unpaidIncome, 2),
            'profit_margin' => $totalIncome > 0 ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2) : 0,
        ];
    }

    /**
     * Get accommodation-wise summary.
     */
    public function getAccommodationSummary($accommodationId, Carbon $startDate, Carbon $endDate): array
    {
        $income = IncomeRecord::where('accommodation_id', $accommodationId)
            ->forDateRange($startDate, $endDate)
            ->sum('amount');

        $expenses = ExpenseRecord::where('accommodation_id', $accommodationId)
            ->forDateRange($startDate, $endDate)
            ->sum('amount');

        return [
            'accommodation_id' => $accommodationId,
            'total_income' => round($income, 2),
            'total_expenses' => round($expenses, 2),
            'net_contribution' => round($income - $expenses, 2),
        ];
    }

    /**
     * Get B2B partner summary.
     */
    public function getB2bPartnerSummary($partnerId, Carbon $startDate, Carbon $endDate): array
    {
        $revenue = IncomeRecord::where('b2b_partner_id', $partnerId)
            ->forDateRange($startDate, $endDate)
            ->sum('amount');

        $paidAmount = IncomeRecord::where('b2b_partner_id', $partnerId)
            ->forDateRange($startDate, $endDate)
            ->sum('paid_amount');

        $transactionCount = IncomeRecord::where('b2b_partner_id', $partnerId)
            ->forDateRange($startDate, $endDate)
            ->count();

        return [
            'partner_id' => $partnerId,
            'total_revenue' => round($revenue, 2),
            'paid_amount' => round($paidAmount, 2),
            'outstanding' => round($revenue - $paidAmount, 2),
            'transaction_count' => $transactionCount,
        ];
    }

    /**
     * Lock a financial period.
     */
    public function lockPeriod($periodId, $userId = null): bool
    {
        $period = FinancialPeriod::findOrFail($periodId);

        if ($period->is_locked) {
            return false;
        }

        // Lock all reports in this period
        $period->reports()->update(['status' => 'locked']);

        return $period->lock($userId);
    }

    /**
     * Get all properties financial summary.
     */
    public function getAllPropertiesSummary(Carbon $startDate, Carbon $endDate): array
    {
        return Property::with(['owner'])
            ->get()
            ->map(function ($property) use ($startDate, $endDate) {
                $summary = $this->getPropertySummary($property->id, $startDate, $endDate);
                $summary['property_name'] = $property->name;
                $summary['owner_name'] = $property->owner?->name;
                return $summary;
            })
            ->toArray();
    }
}
