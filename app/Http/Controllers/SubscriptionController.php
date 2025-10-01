<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function plans()
    {
        return view('subscription.plans');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:starter,professional',
            'billing' => 'required|in:monthly,yearly'
        ]);
        
        $user = auth()->user();
        
        // Check if user already has pending request
        if ($user->hasPendingRequest()) {
            return redirect()->back()->with('warning', 'You already have a pending subscription request.');
        }
        
        // Create subscription request for admin approval
        $subscriptionRequest = SubscriptionRequest::create([
            'user_id' => $user->id,
            'requested_plan' => $request->plan,
            'billing_cycle' => $request->billing,
            'status' => 'pending',
        ]);
        
        $planName = ucfirst($request->plan);
        $price = $request->plan === 'starter' ? '₹299' : '₹999';
        $period = $request->billing === 'yearly' ? 'year' : 'month';
        
        return redirect()->back()->with('success', "Subscription request for {$planName} plan ({$price}/{$period}) sent successfully! Our executives will contact you soon.");
    }

    /**
     * Create subscription order via API
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'plan' => 'required|in:starter,professional,additional_accommodation',
            'quantity' => 'integer|min:1|max:50',
            'billing_interval' => 'in:month,year',
        ]);

        $user = auth()->user();
        $plan = $request->plan;
        $quantity = $request->quantity ?? 1;
        $billingInterval = $request->billing_interval ?? 'month';

        // Check if user can subscribe to this plan
        if (!$this->canSubscribeToPlan($user, $plan)) {
            return response()->json([
                'error' => 'Cannot subscribe to this plan',
                'message' => $this->getSubscriptionErrorMessage($user, $plan)
            ], 400);
        }

        try {
            // Create subscription record
            $subscription = $this->subscriptionService->createSubscription(
                $user,
                $plan,
                [
                    'status' => 'pending',
                    'performed_by' => 'user'
                ]
            );

            // Create Cashfree order
            $orderData = $this->createCashfreeOrder($subscription, $plan, $quantity, $billingInterval);

            // Update subscription with order ID
            $subscription->update([
                'cashfree_order_id' => $orderData['order_id']
            ]);

            return response()->json([
                'order_id' => $orderData['order_id'],
                'payment_url' => $orderData['payment_url'],
                'subscription_id' => $subscription->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add accommodations to existing subscription
     */
    public function addAccommodations(Request $request): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $user = auth()->user();
        $subscription = $this->subscriptionService->getActiveSubscription($user);

        // If no active subscription but user has professional status, create one
        if (!$subscription && $user->subscription_status === 'professional') {
            $subscription = $this->subscriptionService->createSubscription($user, 'professional', [
                'status' => 'active',
                'performed_by' => 'system'
            ]);
        }

        if (!$subscription) {
            return response()->json([
                'error' => 'No active subscription found'
            ], 400);
        }

        if ($subscription->plan_slug !== 'professional') {
            return response()->json([
                'error' => 'Add-ons are only available for Professional plan'
            ], 400);
        }

        try {
            // Create Cashfree order for accommodation add-ons
            $orderData = $this->createAccommodationOrder($subscription, $request->quantity);

            return response()->json([
                'success' => true,
                'order_id' => $orderData['order_id'],
                'payment_url' => $orderData['payment_url'],
                'amount' => $request->quantity * 99, // ₹99 per accommodation
                'quantity' => $request->quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create payment order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's subscription status
     */
    public function status(): JsonResponse
    {
        $user = auth()->user();
        $subscription = $this->subscriptionService->getActiveSubscription($user);

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
                'plan' => 'trial',
                'status' => 'trial',
            ]);
        }

        return response()->json([
            'has_subscription' => true,
            'subscription' => $subscription,
            'plan' => $subscription->plan_slug,
            'status' => $subscription->status,
            'total_accommodations' => $subscription->total_accommodations,
            'days_remaining' => $subscription->days_remaining,
        ]);
    }

    /**
     * Check if user can subscribe to plan
     */
    private function canSubscribeToPlan($user, string $plan): bool
    {
        $activeSubscription = $this->subscriptionService->getActiveSubscription($user);

        if ($plan === 'additional_accommodation') {
            return $activeSubscription && $activeSubscription->plan_slug === 'professional';
        }

        // Allow upgrades from Starter to Professional
        if ($activeSubscription && $plan === 'professional' && $activeSubscription->plan_slug === 'starter') {
            return true;
        }

        // For new subscriptions, user shouldn't have active subscription
        return !$activeSubscription;
    }

    /**
     * Get subscription error message
     */
    private function getSubscriptionErrorMessage($user, string $plan): string
    {
        $activeSubscription = $this->subscriptionService->getActiveSubscription($user);

        if ($plan === 'additional_accommodation') {
            return 'Add-ons are only available for Professional plan users';
        }

        if ($activeSubscription) {
            if ($plan === 'professional' && $activeSubscription->plan_slug === 'starter') {
                return 'Upgrade to Professional plan is allowed';
            }
            return 'You already have an active subscription. Please contact support to upgrade.';
        }

        return 'Unable to subscribe to this plan';
    }

    /**
     * Create Cashfree order
     */
    private function createCashfreeOrder(Subscription $subscription, string $plan, int $quantity, string $billingInterval): array
    {
        $user = $subscription->user;
        $planConfig = config("cashfree.plans.{$plan}");
        $amount = $planConfig['amount'];
        
        if ($billingInterval === 'year') {
            $amount = $amount * 12; // 12 months for yearly
        }
        
        // Use rupees directly for Cashfree API
        $amountInRupees = $amount;

        // Generate unique order ID
        $orderId = 'order_' . time() . '_' . $subscription->id;

        $orderData = [
            'order_id' => $orderId,
            'order_amount' => $amountInRupees,
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
                'quantity' => $quantity,
                'billing_interval' => $billingInterval,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ],
            'order_note' => "Subscription: {$planConfig['name']} ({$billingInterval})"
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => config('cashfree.api_version'),
                'x-client-id' => config('cashfree.app_id'),
                'x-client-secret' => config('cashfree.secret_key'),
            ])->post(config('cashfree.base_url')[config('cashfree.mode')] . '/pg/orders', $orderData);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'order_id' => $responseData['order_id'],
                    'payment_url' => $responseData['payment_link'],
                    'cf_order_id' => $responseData['cf_order_id'],
                ];
            } else {
                throw new \Exception('Cashfree API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Cashfree order: ' . $e->getMessage());
        }
    }

    /**
     * Create Cashfree order for accommodation add-ons
     */
    private function createAccommodationOrder(Subscription $subscription, int $quantity): array
    {
        $user = $subscription->user;
        $amount = $quantity * 99; // ₹99 per accommodation
        
        // Use rupees directly for Cashfree API
        $amountInRupees = $amount;

        // Generate unique order ID
        $orderId = 'accommodation_' . time() . '_' . $subscription->id . '_' . $quantity;

        $orderData = [
            'order_id' => $orderId,
            'order_amount' => $amountInRupees,
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
                'type' => 'accommodation_addon',
                'quantity' => $quantity,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ],
            'order_note' => "Additional Accommodations: {$quantity} accommodations (₹99 each)"
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => config('cashfree.api_version'),
                'x-client-id' => config('cashfree.app_id'),
                'x-client-secret' => config('cashfree.secret_key'),
            ])->post(config('cashfree.base_url')[config('cashfree.mode')] . '/pg/orders', $orderData);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'order_id' => $responseData['order_id'],
                    'payment_url' => $responseData['payment_link'],
                    'cf_order_id' => $responseData['cf_order_id'],
                ];
            } else {
                throw new \Exception('Cashfree API error: ' . $response->body());
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Cashfree order: ' . $e->getMessage());
        }
    }
}