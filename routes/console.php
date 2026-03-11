<?php

use App\Jobs\GenerateMonthlyReportJob;
use App\Jobs\GenerateWeeklyReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ================================================
// FINANCIAL REPORT AUTOMATION SCHEDULE
// ================================================

// Generate weekly reports every Monday at midnight
Schedule::job(new GenerateWeeklyReportJob)
    ->weeklyOn(1, '00:00')
    ->name('financial-weekly-reports')
    ->withoutOverlapping()
    ->onOneServer();

// Generate monthly reports on the 1st of each month at 00:30
Schedule::job(new GenerateMonthlyReportJob)
    ->monthlyOn(1, '00:30')
    ->name('financial-monthly-reports')
    ->withoutOverlapping()
    ->onOneServer();
