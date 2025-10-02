<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Webhook;
use App\Jobs\ProcessCashfreeWebhook;

class CashfreeController extends Controller
{
    private $baseUrl;
    private $appId;
    private $secretKey;
    private $apiVersion;

    public function __construct()
    {
        $this->appId = config('cashfree.app_id');
        $this->secretKey = config('cashfree.secret_key');
        $this->apiVersion = config('cashfree.api_version');
        $this->baseUrl = config('cashfree.base_url')[config('cashfree.mode')];
    }

    /**
     * Create a payment order for subscription
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional',
            'additional_accommodations' => 'integer|min:0|max:50',
            'billing' => 'required|in:monthly,yearly'
        ]);

        $user = auth()->user();
        $plan = $request->plan;
        $additionalAccommodations = $request->additional_accommodations ?? 0;
        $billing = $request->billing;

        // Calculate total amount
        $planConfig = config("cashfree.plans.{$plan}");
        $baseAmount = $planConfig['amount'];
        
        if ($billing === 'yearly') {
            $baseAmount = $baseAmount * 12; // 12 months for yearly
        }
        
        $additionalAmount = $additionalAccommodations * config('cashfree.plans.additional_accommodation.amount');
        $totalAmount = $baseAmount + $additionalAmount;
        
        // Use rupees directly for Cashfree API
        $totalAmountInRupees = $totalAmount;

        // Generate unique order ID
        $orderId = 'SUB_' . strtoupper(Str::random(8)) . '_' . time();

        $orderData = [
            'order_id' => $orderId,
            'order_amount' => $totalAmountInRupees,
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => $user->uuid,
                'customer_name' => $user->name,
                'customer_email' => $user->email ?? 'user@example.com',
                'customer_phone' => $user->mobile_number ?? '9999999999',
            ],
            'order_meta' => [
                'return_url' => route('cashfree.success'),
                'notify_url' => url('/api/cashfree/webhook'),
                'plan' => $plan,
                'additional_accommodations' => $additionalAccommodations,
                'billing' => $billing,
                'user_id' => $user->id,
            ],
            'order_note' => "Subscription: {$planConfig['name']}" . 
                          ($additionalAccommodations > 0 ? " + {$additionalAccommodations} accommodations" : '') .
                          " ({$billing})"
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => $this->apiVersion,
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
            ])->post("{$this->baseUrl}/pg/orders", $orderData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['order_token'])) {
                // Store order details in session for verification
                session([
                    'cashfree_order_id' => $orderId,
                    'cashfree_plan' => $plan,
                    'cashfree_additional_accommodations' => $additionalAccommodations,
                    'cashfree_billing' => $billing,
                    'cashfree_amount' => $totalAmount
                ]);

                return response()->json([
                    'success' => true,
                    'payment_session_id' => $responseData['order_token'],
                    'order_id' => $orderId,
                    'payment_link' => $responseData['payment_link']
                ]);
            } else {
                Log::error('Cashfree order creation failed', [
                    'response' => $responseData,
                    'user_id' => $user->id,
                    'plan' => $plan
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Please try again.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Cashfree order creation exception', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'plan' => $plan
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable. Please try again later.'
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $orderId = $request->get('order_id');
        $sessionOrderId = session('cashfree_order_id');

        Log::info('Cashfree success callback received', [
            'order_id' => $orderId,
            'session_order_id' => $sessionOrderId,
            'all_params' => $request->all(),
            'query_params' => $request->query(),
            'referer' => $request->header('referer')
        ]);

        // If no order_id in request, try to get from session
        if (!$orderId && $sessionOrderId) {
            $orderId = $sessionOrderId;
        }

        // If still no order_id, try to get from URL parameters or referrer
        if (!$orderId) {
            $orderId = $request->get('cf_order_id') ?? $request->get('order_token');
            
            // Try to extract from referrer URL
            if (!$orderId && $request->header('referer')) {
                $referer = $request->header('referer');
                if (preg_match('/order_id=([^&]+)/', $referer, $matches)) {
                    $orderId = $matches[1];
                }
            }
        }

        // If still no order_id, try to get from recent webhook data
        if (!$orderId && auth()->check()) {
            $user = auth()->user();
            $recentWebhook = \App\Models\Webhook::where('provider', 'cashfree')
                ->where('payload->data->order->order_meta->user_id', $user->id)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($recentWebhook && isset($recentWebhook->payload['data']['order']['order_id'])) {
                $orderId = $recentWebhook->payload['data']['order']['order_id'];
                Log::info('Found order_id from recent webhook', [
                    'order_id' => $orderId,
                    'webhook_id' => $recentWebhook->id,
                    'user_id' => $user->id
                ]);
            }
        }

        // If still no order_id, try to get from recent subscription orders
        if (!$orderId && auth()->check()) {
            $user = auth()->user();
            $recentSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->whereNotNull('cashfree_order_id')
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($recentSubscription) {
                $orderId = $recentSubscription->cashfree_order_id;
                Log::info('Found order_id from recent subscription', [
                    'order_id' => $orderId,
                    'subscription_id' => $recentSubscription->id,
                    'user_id' => $user->id
                ]);
            }
        }

        if (!$orderId) {
            Log::error('No order ID found in success callback', [
                'request_params' => $request->all(),
                'session_data' => session()->all(),
                'referer' => $request->header('referer'),
                'user_id' => auth()->id()
            ]);
            
            // If user is authenticated, check if they have a recent successful payment
            if (auth()->check()) {
                $user = auth()->user();
                $recentPayment = \App\Models\Payment::where('subscription_id', function($query) use ($user) {
                    $query->select('id')
                          ->from('subscriptions')
                          ->where('user_id', $user->id)
                          ->where('status', 'active')
                          ->orderBy('created_at', 'desc')
                          ->limit(1);
                })
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();
                
                if ($recentPayment) {
                    Log::info('Found recent successful payment, redirecting to success', [
                        'payment_id' => $recentPayment->id,
                        'user_id' => $user->id
                    ]);
                    return redirect()->route('subscription.plans', ['payment' => 'success'])
                        ->with('success', 'Payment successful! Your subscription has been activated.');
                }
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', 'Invalid payment session. Please try again.');
        }

        // Verify payment status
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => $this->apiVersion,
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
            ])->get("{$this->baseUrl}/pg/orders/{$orderId}");

            $orderData = $response->json();
            
            Log::info('Cashfree order verification response', [
                'status' => $response->status(),
                'order_data' => $orderData
            ]);

            if ($response->successful() && isset($orderData['order_status']) && in_array($orderData['order_status'], ['PAID', 'ACTIVE'])) {
                // Update user subscription
                $this->updateUserSubscription($orderData);
                
                // Clear session data
                session()->forget([
                    'cashfree_order_id',
                    'cashfree_plan',
                    'cashfree_additional_accommodations',
                    'cashfree_billing',
                    'cashfree_amount'
                ]);

                // Check if user is authenticated
                if (auth()->check()) {
                    // Force refresh user data from database
                    auth()->user()->refresh();
                    
                    // Check if this was an accommodation add-on payment
                    $orderMeta = $orderData['order_meta'] ?? [];
                    $orderType = $orderMeta['type'] ?? 'subscription';
                    
                    if ($orderType === 'accommodation_addon') {
                        $quantity = $orderMeta['quantity'] ?? 0;
                        return redirect()->route('subscription.plans', ['payment' => 'success'])
                            ->with('success', "Payment successful! {$quantity} additional accommodations have been added to your plan for 30 days.");
                    } else {
                        return redirect()->route('subscription.plans', ['payment' => 'success'])
                            ->with('success', 'Payment successful! Your subscription has been activated.');
                    }
                } else {
                    // Store success message in session for when user logs in
                    session()->flash('payment_success', 'Payment successful! Please log in to view your updated subscription.');
                    return redirect()->route('login')
                        ->with('success', 'Payment successful! Please log in to view your updated subscription.');
                }
            } else {
                Log::error('Payment verification failed', [
                    'order_status' => $orderData['order_status'] ?? 'unknown',
                    'response' => $orderData
                ]);
                
                return redirect()->route('subscription.plans')
                    ->with('error', 'Payment verification failed. Please contact support.');
            }
        } catch (\Exception $e) {
            Log::error('Cashfree payment verification failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            return redirect()->route('subscription.plans')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Handle webhook notifications
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('x-webhook-signature');
        $payload = $request->getContent();

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Cashfree webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $webhookData = $request->json()->all();

        Log::info('Cashfree webhook received', $webhookData);

        // Store webhook in database
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => $webhookData['data']['payment']['cf_payment_id'] ?? null,
            'payload' => $webhookData,
            'signature_header' => $signature,
            'received_at' => now(),
        ]);
        
        // Queue webhook processing
        ProcessCashfreeWebhook::dispatch($webhook->id);

        return response()->json(['status' => 'success']);
    }

    /**
     * Update user subscription after successful payment
     */
    private function updateUserSubscription($orderData)
    {
        $user = auth()->user();

        if (!$user) {
            Log::error('User not found for subscription update');
            return;
        }

        // Try to get data from order_meta first, then fallback to session
        $orderMeta = $orderData['order_meta'] ?? [];
        $orderType = $orderMeta['type'] ?? 'subscription';
        $plan = $orderMeta['plan'] ?? session('cashfree_plan');
        $billing = $orderMeta['billing'] ?? session('cashfree_billing');
        $billingInterval = $orderMeta['billing_interval'] ?? $billing;
        $additionalAccommodations = $orderMeta['additional_accommodations'] ?? session('cashfree_additional_accommodations', 0);
        $userId = $orderMeta['user_id'] ?? $user->id;
        $subscriptionId = $orderMeta['subscription_id'] ?? null;

        // Handle accommodation add-on payments
        if ($orderType === 'accommodation_addon') {
            $this->handleAccommodationAddonPayment($orderData, $orderMeta);
            return;
        }

        // If we have a subscription_id, update the subscription record instead of user directly
        if ($subscriptionId) {
            $subscription = \App\Models\Subscription::find($subscriptionId);
            if ($subscription) {
                // Activate the subscription
                $subscription->update(['status' => 'active']);
                
                // Update user's subscription status from the subscription record
                $user->update([
                    'subscription_status' => $subscription->plan_slug,
                    'subscription_ends_at' => $subscription->current_period_end,
                    'is_trial_active' => false,
                    'properties_limit' => $subscription->plan_slug === 'starter' ? 1 : 5,
                    'billing_cycle' => $subscription->billing_interval === 'year' ? 'yearly' : 'monthly',
                ]);

                Log::info('User subscription updated from subscription record', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'plan' => $subscription->plan_slug,
                    'billing_interval' => $subscription->billing_interval,
                ]);

                // Clear any cached user data
                $user->refresh();
                return;
            }
        }

        // Fallback to old logic for backward compatibility
        // If plan is still not found, try to extract from order_note
        if (!$plan && isset($orderData['order_note'])) {
            $orderNote = $orderData['order_note'];
            if (strpos($orderNote, 'Professional') !== false) {
                $plan = 'professional';
            } elseif (strpos($orderNote, 'Starter') !== false) {
                $plan = 'starter';
            }
        }

        // Default to professional if still not found
        if (!$plan) {
            $plan = 'professional';
        }

        // Default to monthly if billing not found
        if (!$billingInterval) {
            $billingInterval = 'month';
        }

        Log::info('Subscription update data', [
            'order_meta' => $orderMeta,
            'session_plan' => session('cashfree_plan'),
            'session_billing' => session('cashfree_billing'),
            'plan' => $plan,
            'billing_interval' => $billingInterval,
            'user_id' => $userId,
            'subscription_id' => $subscriptionId
        ]);

        // Plan and billing should now have defaults, but log if they're still missing
        if (!$plan || !$billingInterval) {
            Log::error('Missing plan or billing information after fallbacks', [
                'plan' => $plan,
                'billing_interval' => $billingInterval,
                'order_meta' => $orderMeta,
                'order_note' => $orderData['order_note'] ?? null
            ]);
            // Don't return, use defaults
            $plan = $plan ?: 'professional';
            $billingInterval = $billingInterval ?: 'month';
        }

        // Update user subscription
        $updateData = [
            'subscription_status' => $plan,
            'subscription_ends_at' => $billingInterval === 'year' ? now()->addYear() : now()->addMonth(),
            'is_trial_active' => false,
            'properties_limit' => $plan === 'starter' ? 1 : 5,
            'billing_cycle' => $billingInterval === 'year' ? 'yearly' : 'monthly',
        ];

        // Store additional accommodations if any
        if ($additionalAccommodations > 0) {
            $updateData['additional_accommodations'] = $additionalAccommodations;
        }

        $user->update($updateData);

        // Clear any cached user data
        $user->refresh();

        Log::info('User subscription updated successfully', [
            'user_id' => $user->id,
            'plan' => $plan,
            'billing_interval' => $billingInterval,
            'additional_accommodations' => $additionalAccommodations,
            'update_data' => $updateData
        ]);
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature($payload, $signature)
    {
        $webhookSecret = config('cashfree.webhook_secret');
        
        // Skip signature verification if webhook secret is not configured
        if (empty($webhookSecret) || $webhookSecret === 'your_webhook_secret_here') {
            Log::warning('Webhook signature verification skipped - secret not configured');
            return true;
        }
        
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle payment success webhook
     */
    private function handlePaymentSuccess($webhookData)
    {
        $orderId = $webhookData['data']['order']['order_id'] ?? null;
        
        if (!$orderId) {
            Log::error('No order ID in payment success webhook', $webhookData);
            return;
        }
        
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => $this->apiVersion,
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
            ])->get("{$this->baseUrl}/pg/orders/{$orderId}");

            $orderData = $response->json();

            if ($response->successful() && isset($orderData['order_status']) && $orderData['order_status'] === 'PAID') {
                // Get user from order meta
                $orderMeta = $orderData['order_meta'] ?? [];
                $userId = $orderMeta['user_id'] ?? null;
                
                if ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        // Temporarily set auth user for updateUserSubscription
                        auth()->login($user);
                        $this->updateUserSubscription($orderData);
                        Log::info('User subscription updated via webhook', ['user_id' => $userId]);
                    } else {
                        Log::error('User not found for webhook update', ['user_id' => $userId]);
                    }
                } else {
                    Log::error('No user ID in order meta for webhook', $orderMeta);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process payment success webhook', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
        }
    }

    /**
     * Handle payment failed webhook
     */
    private function handlePaymentFailed($webhookData)
    {
        Log::info('Payment failed', $webhookData);
        // Handle failed payment logic here
    }

    /**
     * Handle payment dropped webhook
     */
    private function handlePaymentDropped($webhookData)
    {
        Log::info('Payment dropped by user', $webhookData);
        // Handle dropped payment logic here
    }

    /**
     * Handle accommodation add-on payment success
     */
    private function handleAccommodationAddonPayment($orderData, $orderMeta)
    {
        $user = auth()->user();
        $subscriptionId = $orderMeta['subscription_id'] ?? null;
        $quantity = $orderMeta['quantity'] ?? 0;

        if (!$subscriptionId || !$quantity) {
            Log::error('Missing subscription_id or quantity for accommodation add-on', [
                'order_meta' => $orderMeta,
                'user_id' => $user->id
            ]);
            return;
        }

        try {
            $subscription = \App\Models\Subscription::find($subscriptionId);
            if (!$subscription) {
                Log::error('Subscription not found for accommodation add-on', [
                    'subscription_id' => $subscriptionId,
                    'user_id' => $user->id
                ]);
                return;
            }

            // Create accommodation add-on with 30-day expiry
            $addon = \App\Models\SubscriptionAddon::create([
                'subscription_id' => $subscription->id,
                'qty' => $quantity,
                'unit_price_cents' => 9900, // ₹99 in cents
                'cycle_start' => now(),
                'cycle_end' => now()->addDays(30), // 30-day expiry
            ]);

            // Update subscription addon count
            $subscription->increment('addon_count', $quantity);

            // Update subscription total amount to include addon costs
            $addonAmountCents = $quantity * 9900; // ₹99 per accommodation in cents
            $subscription->increment('price_cents', $addonAmountCents);

            // Create payment record
            \App\Models\Payment::create([
                'subscription_id' => $subscription->id,
                'cashfree_order_id' => $orderData['order_id'],
                'payment_id' => $orderData['cf_order_id'] ?? null,
                'amount' => $quantity * 99, // Keep in rupees for display
                'amount_cents' => $addonAmountCents, // Store in cents
                'currency' => 'INR',
                'method' => 'card',
                'status' => 'completed',
                'paid_at' => now(),
                'raw_response' => $orderData,
            ]);

            Log::info('Accommodation add-on payment processed successfully', [
                'subscription_id' => $subscription->id,
                'addon_id' => $addon->id,
                'quantity' => $quantity,
                'user_id' => $user->id,
                'expires_at' => $addon->cycle_end,
                'addon_count_after' => $subscription->fresh()->addon_count,
                'total_addons' => $subscription->fresh()->addons()->where('cycle_end', '>', now())->sum('qty')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process accommodation add-on payment', [
                'error' => $e->getMessage(),
                'order_meta' => $orderMeta,
                'user_id' => $user->id
            ]);
        }
    }
}
