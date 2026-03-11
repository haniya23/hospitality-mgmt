<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Services\DashboardDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialDashboardController extends Controller
{
    protected DashboardDataService $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Executive financial dashboard with period toggle.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all properties for admin/super users, or user's own properties
        // If user has no properties, show all properties (for demo/admin purposes)
        $userProperties = $user->properties;
        if ($userProperties->isEmpty() || $user->is_admin) {
            $propertyIds = []; // Empty means "all properties" in service
        } else {
            $propertyIds = $userProperties->pluck('id')->toArray();
        }

        // Period: day, week, or month (default: month)
        $period = $request->input('period', 'month');

        // Get property summary with comparison data
        $dashboardData = $this->dashboardService->getPropertySummaryList($propertyIds, $period);

        // Get chart data for the selected period
        $dates = $this->dashboardService->getPeriodDates($period);

        $profitTrend = $this->dashboardService->getChartData('profit_trend', [
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
        ]);

        $incomePie = $this->dashboardService->getChartData('income_pie', [
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
        ]);

        return view('owner.financial.dashboard', compact(
            'dashboardData',
            'profitTrend',
            'incomePie',
            'period'
        ));
    }

    /**
     * Property-specific financial dashboard.
     */
    public function propertyDashboard(Request $request, Property $property)
    {
        $this->authorize('view', $property);

        $period = $request->input('period', 'month');
        $dates = $this->dashboardService->getPeriodDates($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        $metrics = $this->dashboardService->getPropertyWiseMetrics($property->id, $startDate, $endDate);
        $accommodationData = $this->dashboardService->getAccommodationWiseMetrics($property->id, $startDate, $endDate);

        // Recent transactions for this property
        $recentIncome = \App\Models\IncomeRecord::forProperty($property->id)
            ->forDateRange($startDate, $endDate)
            ->with('accommodation')
            ->latest('transaction_date')
            ->take(10)
            ->get();

        $recentExpenses = \App\Models\ExpenseRecord::forProperty($property->id)
            ->forDateRange($startDate, $endDate)
            ->with(['accommodation', 'category'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        $incomePie = $this->dashboardService->getChartData('income_pie', [
            'property_id' => $property->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $accommodationBar = $this->dashboardService->getChartData('accommodation_bar', [
            'property_id' => $property->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return view('owner.financial.property-dashboard', compact(
            'property',
            'metrics',
            'accommodationData',
            'recentIncome',
            'recentExpenses',
            'incomePie',
            'accommodationBar',
            'period',
            'dates'
        ));
    }

    /**
     * Accommodation-specific financial dashboard.
     */
    public function accommodationDashboard(Request $request, PropertyAccommodation $accommodation)
    {
        $property = $accommodation->property;
        $this->authorize('view', $property);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        $reportService = app(\App\Services\FinancialReportService::class);
        $summary = $reportService->getAccommodationSummary($accommodation->id, $startDate, $endDate);

        return view('owner.financial.accommodation-dashboard', compact(
            'property',
            'accommodation',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * B2B partner financial dashboard.
     */
    public function b2bDashboard(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        $data = $this->dashboardService->getB2bDashboardData($startDate, $endDate);

        $rankingChart = $this->dashboardService->getChartData('b2b_ranking', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return view('owner.financial.b2b-dashboard', compact(
            'data',
            'rankingChart',
            'startDate',
            'endDate'
        ));
    }

    /**
     * API endpoint for chart data (AJAX).
     */
    public function chartData(Request $request)
    {
        $chartType = $request->input('type', 'profit_trend');
        $filters = $request->only(['property_id', 'start_date', 'end_date']);

        $data = $this->dashboardService->getChartData($chartType, $filters);

        return response()->json($data);
    }
}
