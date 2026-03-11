<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\FinancialReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FinancialReportService $reportService): void
    {
        Log::info('Starting monthly financial report generation');

        try {
            // Generate global monthly report (all properties combined)
            $globalReport = $reportService->generateMonthlyReport();
            Log::info("Generated global monthly report: {$globalReport->report_number}");

            // Generate per-property monthly reports
            $properties = Property::whereHas('owner')->get();

            foreach ($properties as $property) {
                try {
                    $report = $reportService->generateMonthlyReport($property->id);
                    Log::info("Generated monthly report for property {$property->name}: {$report->report_number}");
                } catch (\Exception $e) {
                    Log::error("Failed to generate monthly report for property {$property->id}: {$e->getMessage()}");
                }
            }

            Log::info('Monthly financial report generation completed successfully');

        } catch (\Exception $e) {
            Log::error("Monthly report generation failed: {$e->getMessage()}");
            throw $e;
        }
    }
}
