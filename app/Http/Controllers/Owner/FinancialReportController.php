<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\FinancialReport;
use App\Models\Property;
use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    protected FinancialReportService $reportService;

    public function __construct(FinancialReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display weekly reports listing.
     */
    public function weeklyReports(Request $request)
    {
        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $query = FinancialReport::weekly()
            ->with(['property', 'period', 'generatedBy'])
            ->where(function ($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds)
                    ->orWhereNull('property_id');
            })
            ->latest('created_at');

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15);
        $properties = $user->properties;

        return view('owner.reports.weekly', compact('reports', 'properties'));
    }

    /**
     * Display monthly reports listing.
     */
    public function monthlyReports(Request $request)
    {
        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $query = FinancialReport::monthly()
            ->with(['property', 'period', 'generatedBy'])
            ->where(function ($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds)
                    ->orWhereNull('property_id');
            })
            ->latest('created_at');

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15);
        $properties = $user->properties;

        return view('owner.reports.monthly', compact('reports', 'properties'));
    }

    /**
     * Display a specific report.
     */
    public function viewReport(FinancialReport $report)
    {
        if ($report->property_id) {
            $this->authorize('view', $report->property);
        }

        $report->load(['property', 'period', 'items', 'generatedBy', 'approvedBy']);

        return view('owner.reports.view', compact('report'));
    }

    /**
     * Regenerate a draft report (admin only).
     */
    public function regenerateReport(Request $request, FinancialReport $report)
    {
        if ($report->is_locked) {
            return back()->with('error', 'Cannot regenerate a locked report.');
        }

        if ($report->property_id) {
            $this->authorize('update', $report->property);
        }

        $period = $report->period;

        $newReport = $this->reportService->generateReport(
            $report->property_id,
            $report->report_type,
            $period->start_date,
            $period->end_date
        );

        return redirect()->route('owner.reports.view', $newReport)
            ->with('success', 'Report regenerated successfully.');
    }

    /**
     * Approve a report.
     */
    public function approveReport(FinancialReport $report)
    {
        if ($report->property_id) {
            $this->authorize('update', $report->property);
        }

        if ($report->is_approved) {
            return back()->with('info', 'Report is already approved.');
        }

        $report->approve();

        return back()->with('success', 'Report approved successfully.');
    }

    /**
     * Lock a report (final state).
     */
    public function lockReport(FinancialReport $report)
    {
        if ($report->property_id) {
            $this->authorize('update', $report->property);
        }

        if (!$report->is_approved) {
            return back()->with('error', 'Report must be approved before locking.');
        }

        $report->lock();

        return back()->with('success', 'Report locked successfully.');
    }

    /**
     * Export report to PDF.
     */
    public function exportPdf(FinancialReport $report)
    {
        if ($report->property_id) {
            $this->authorize('view', $report->property);
        }

        $report->load(['property', 'period', 'items', 'generatedBy', 'approvedBy']);

        $pdf = Pdf::loadView('owner.reports.pdf', compact('report'));

        $filename = $report->report_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export report to Excel.
     */
    public function exportExcel(FinancialReport $report)
    {
        if ($report->property_id) {
            $this->authorize('view', $report->property);
        }

        $report->load(['property', 'period', 'items']);

        // Simple CSV export (can be enhanced with Laravel Excel package)
        $filename = $report->report_number . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Financial Report: ' . $report->report_title]);
            fputcsv($file, ['Generated: ' . $report->created_at->format('M d, Y H:i')]);
            fputcsv($file, []);

            // Summary
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Income', '₹' . number_format($report->total_income, 2)]);
            fputcsv($file, ['Total Expenses', '₹' . number_format($report->total_expenses, 2)]);
            fputcsv($file, ['Net Profit', '₹' . number_format($report->net_profit, 2)]);
            fputcsv($file, ['Outstanding Receivables', '₹' . number_format($report->outstanding_receivables, 2)]);
            fputcsv($file, []);

            // Income breakdown
            fputcsv($file, ['Income Breakdown']);
            fputcsv($file, ['Category', 'Amount', 'Transactions']);
            foreach ($report->items->where('item_type', 'income') as $item) {
                fputcsv($file, [ucfirst($item->category), '₹' . number_format($item->amount, 2), $item->transaction_count]);
            }
            fputcsv($file, []);

            // Expense breakdown
            fputcsv($file, ['Expense Breakdown']);
            fputcsv($file, ['Category', 'Amount', 'Transactions']);
            foreach ($report->items->where('item_type', 'expense') as $item) {
                fputcsv($file, [$item->category, '₹' . number_format($item->amount, 2), $item->transaction_count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate a report on demand.
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'property_id' => ['nullable', 'exists:properties,id'],
            'report_type' => ['required', 'in:weekly,monthly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validated['property_id']) {
            $property = Property::findOrFail($validated['property_id']);
            $this->authorize('update', $property);
        }

        $report = $this->reportService->generateReport(
            $validated['property_id'],
            $validated['report_type'],
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date'])
        );

        return redirect()->route('owner.reports.view', $report)
            ->with('success', 'Report generated successfully.');
    }
}
