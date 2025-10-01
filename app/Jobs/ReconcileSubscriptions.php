<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Webhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ReconcileSubscriptions implements ShouldQueue
{
    use Queueable;

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
    public function handle(): void
    {
        Log::info('Starting subscription reconciliation job');

        // 1. Check for expired subscriptions
        $this->checkExpiredSubscriptions();

        // 2. Check for unprocessed webhooks
        $this->checkUnprocessedWebhooks();

        // 3. Check for pending payments
        $this->checkPendingPayments();

        // 4. Verify subscription limits
        $this->verifySubscriptionLimits();

        Log::info('Subscription reconciliation job completed');
    }

    private function checkExpiredSubscriptions(): void
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('current_period_end', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info("Marked subscription {$subscription->id} as expired");
        }

        if ($expiredSubscriptions->count() > 0) {
            Log::warning("Found {$expiredSubscriptions->count()} expired subscriptions");
        }
    }

    private function checkUnprocessedWebhooks(): void
    {
        $unprocessedWebhooks = Webhook::where('processed', false)
            ->where('received_at', '<', now()->subHours(1))
            ->get();

        foreach ($unprocessedWebhooks as $webhook) {
            Log::warning("Unprocessed webhook found: {$webhook->id} received at {$webhook->received_at}");
            
            // Mark as failed if too old
            if ($webhook->received_at < now()->subHours(24)) {
                $webhook->markAsFailed('Reconciliation: Webhook too old to process');
            }
        }

        if ($unprocessedWebhooks->count() > 0) {
            Log::warning("Found {$unprocessedWebhooks->count()} unprocessed webhooks");
        }
    }

    private function checkPendingPayments(): void
    {
        $pendingPayments = Payment::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($pendingPayments as $payment) {
            Log::warning("Old pending payment found: {$payment->id} created at {$payment->created_at}");
            
            // Mark as failed if too old
            $payment->update(['status' => 'failed']);
        }

        if ($pendingPayments->count() > 0) {
            Log::warning("Found {$pendingPayments->count()} old pending payments");
        }
    }

    private function verifySubscriptionLimits(): void
    {
        $subscriptions = Subscription::where('status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            
            if (!$user) {
                Log::warning("Subscription {$subscription->id} has no associated user");
                continue;
            }

            // Check property limits
            $propertyCount = $user->properties()->count();
            $maxProperties = $this->getMaxPropertiesForPlan($subscription->plan_slug);
            
            if ($propertyCount > $maxProperties) {
                Log::warning("User {$user->id} exceeds property limit: {$propertyCount}/{$maxProperties}");
            }

            // Check accommodation limits
            $accommodationCount = $user->properties()->withCount('accommodations')->get()->sum('accommodations_count');
            $maxAccommodations = $subscription->total_accommodations;
            
            if ($accommodationCount > $maxAccommodations) {
                Log::warning("User {$user->id} exceeds accommodation limit: {$accommodationCount}/{$maxAccommodations}");
            }
        }
    }

    private function getMaxPropertiesForPlan(string $planSlug): int
    {
        return match ($planSlug) {
            'trial' => 1,
            'starter' => 1,
            'professional' => 5,
            default => 1,
        };
    }
}
