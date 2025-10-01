<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\SubscriptionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCashfreeWebhook implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $webhookId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhook = Webhook::find($this->webhookId);
        
        if (!$webhook || $webhook->processed) {
            return;
        }

        try {
            DB::transaction(function () use ($webhook) {
                $this->processWebhook($webhook);
                $webhook->markAsProcessed();
            });
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'webhook_id' => $this->webhookId,
                'error' => $e->getMessage(),
                'payload' => $webhook->payload,
            ]);
            
            $webhook->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    private function processWebhook(Webhook $webhook): void
    {
        $payload = $webhook->payload;
        $eventType = $payload['type'] ?? null;
        
        switch ($eventType) {
            case 'PAYMENT_SUCCESS':
                $this->handlePaymentSuccess($payload);
                break;
            case 'PAYMENT_FAILED':
                $this->handlePaymentFailed($payload);
                break;
            case 'PAYMENT_USER_DROPPED':
                $this->handlePaymentDropped($payload);
                break;
            default:
                Log::info('Unhandled webhook event type', [
                    'type' => $eventType,
                    'webhook_id' => $webhook->id,
                ]);
        }
    }

    private function handlePaymentSuccess(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        $paymentId = $payload['data']['payment']['cf_payment_id'] ?? null;
        $amount = $payload['data']['payment']['payment_amount'] ?? 0;
        
        if (!$orderId) {
            throw new \Exception('Missing order_id in payment success webhook');
        }

        // Find subscription by order ID
        $subscription = Subscription::where('cashfree_order_id', $orderId)->first();
        
        if (!$subscription) {
            throw new \Exception("Subscription not found for order_id: {$orderId}");
        }

        // Create payment record (no invoice needed for subscription payments)
        Payment::create([
            'invoice_id' => null, // No invoice for subscription payments
            'subscription_id' => $subscription->id,
            'cashfree_order_id' => $orderId,
            'payment_id' => $paymentId,
            'amount' => $amount, // Original amount field
            'amount_cents' => $amount * 100, // Convert to cents
            'currency' => 'INR',
            'method' => 'card',
            'status' => 'completed',
            'paid_at' => now(),
            'raw_response' => $payload,
        ]);

        // Activate subscription if it's pending
        if ($subscription->status === 'pending') {
            $subscription->update(['status' => 'active']);
        }

        // Update user's subscription status
        $user = $subscription->user;
        
        // If this is an upgrade, deactivate the previous subscription
        $previousSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('id', '!=', $subscription->id)
            ->first();
            
        if ($previousSubscription) {
            $previousSubscription->update(['status' => 'cancelled']);
            Log::info('Previous subscription cancelled due to upgrade', [
                'previous_subscription_id' => $previousSubscription->id,
                'new_subscription_id' => $subscription->id,
                'user_id' => $user->id,
            ]);
        }
        
        $user->update([
            'subscription_status' => $subscription->plan_slug,
            'subscription_ends_at' => $subscription->current_period_end,
            'is_trial_active' => false,
        ]);

        // Clear any cached user data
        $user->refresh();

        Log::info('Payment processed successfully', [
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'amount' => $amount,
        ]);
    }

    private function handlePaymentFailed(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        $paymentId = $payload['data']['payment']['cf_payment_id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('Missing order_id in payment failed webhook');
        }

        // Find subscription by order ID
        $subscription = Subscription::where('cashfree_order_id', $orderId)->first();
        
        if (!$subscription) {
            throw new \Exception("Subscription not found for order_id: {$orderId}");
        }

        // Create payment record
        Payment::create([
            'subscription_id' => $subscription->id,
            'cashfree_order_id' => $orderId,
            'payment_id' => $paymentId,
            'amount_cents' => 0,
            'currency' => 'INR',
            'method' => 'card',
            'status' => 'failed',
            'raw_response' => $payload,
        ]);

        // Mark subscription as failed
        $subscription->update(['status' => 'expired']);

        Log::info('Payment failed', [
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'subscription_id' => $subscription->id,
        ]);
    }

    private function handlePaymentDropped(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('Missing order_id in payment dropped webhook');
        }

        // Find subscription by order ID
        $subscription = Subscription::where('cashfree_order_id', $orderId)->first();
        
        if (!$subscription) {
            throw new \Exception("Subscription not found for order_id: {$orderId}");
        }

        // Mark subscription as expired
        $subscription->update(['status' => 'expired']);

        Log::info('Payment dropped by user', [
            'order_id' => $orderId,
            'subscription_id' => $subscription->id,
        ]);
    }
}
