<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileSubscriptions;
use Illuminate\Console\Command;

class ReconcileSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:reconcile {--sync : Run synchronously instead of queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile subscription data and check for inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription reconciliation...');

        if ($this->option('sync')) {
            // Run synchronously
            $job = new ReconcileSubscriptions();
            $job->handle();
            $this->info('Reconciliation completed synchronously.');
        } else {
            // Queue the job
            ReconcileSubscriptions::dispatch();
            $this->info('Reconciliation job queued successfully.');
        }

        return Command::SUCCESS;
    }
}
