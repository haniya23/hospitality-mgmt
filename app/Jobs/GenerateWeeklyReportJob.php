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

class GenerateWeeklyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

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
        Log::info('Starting weekly financial report generation');

        try {
            // Generate global weekly report (all properties combined)
            $globalReport = $reportService->generateWeeklyReport();
            Log::info("Generated global weekly report: {$globalReport->report_number}");

            // Generate per-property weekly reports
            $properties = Property::whereHas('owner')->get();

            foreach ($properties as $property) {
                try {
                    $report = $reportService->generateWeeklyReport($property->id);
                    Log::info("Generated weekly report for property {$property->name}: {$report->report_number}");
                } catch (\Exception $e) {
                    Log::error("Failed to generate weekly report for property {$property->id}: {$e->getMessage()}");
                }
            }

            Log::info('Weekly financial report generation completed successfully');

        } catch (\Exception $e) {
            Log::error("Weekly report generation failed: {$e->getMessage()}");
            throw $e;
        }
    }
}
