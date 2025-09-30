<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

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
        
        // Convert to paise for Cashfree API (multiply by 100)
        $totalAmountInPaise = $totalAmount * 100;

        // Generate unique order ID
        $orderId = 'SUB_' . strtoupper(Str::random(8)) . '_' . time();

        $orderData = [
            'order_id' => $orderId,
            'order_amount' => $totalAmountInPaise,
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => $user->uuid,
                'customer_name' => $user->name,
                'customer_email' => $user->email ?? 'user@example.com',
                'customer_phone' => $user->mobile_number ?? '9999999999',
            ],
            'order_meta' => [
                'return_url' => route('cashfree.success'),
                'notify_url' => route('cashfree.webhook'),
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
            'all_params' => $request->all()
        ]);

        // If no order_id in request, try to get from session
        if (!$orderId && $sessionOrderId) {
            $orderId = $sessionOrderId;
        }

        if (!$orderId) {
            Log::error('No order ID found in success callback');
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

            if ($response->successful() && isset($orderData['order_status']) && $orderData['order_status'] === 'PAID') {
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

                return redirect()->route('subscription.plans', ['payment' => 'success'])
                    ->with('success', 'Payment successful! Your subscription has been activated.');
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

        // Handle different webhook events
        switch ($webhookData['type']) {
            case 'PAYMENT_SUCCESS_WEBHOOK':
                $this->handlePaymentSuccess($webhookData);
                break;
            case 'PAYMENT_FAILED_WEBHOOK':
                $this->handlePaymentFailed($webhookData);
                break;
            case 'PAYMENT_USER_DROPPED_WEBHOOK':
                $this->handlePaymentDropped($webhookData);
                break;
        }

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
        $plan = $orderMeta['plan'] ?? session('cashfree_plan');
        $billing = $orderMeta['billing'] ?? session('cashfree_billing');
        $additionalAccommodations = $orderMeta['additional_accommodations'] ?? session('cashfree_additional_accommodations', 0);
        $userId = $orderMeta['user_id'] ?? $user->id;

        Log::info('Subscription update data', [
            'order_meta' => $orderMeta,
            'session_plan' => session('cashfree_plan'),
            'session_billing' => session('cashfree_billing'),
            'plan' => $plan,
            'billing' => $billing,
            'user_id' => $userId
        ]);

        if (!$plan || !$billing) {
            Log::error('Missing plan or billing information', [
                'plan' => $plan,
                'billing' => $billing,
                'order_meta' => $orderMeta
            ]);
            return;
        }

        // Update user subscription
        $updateData = [
            'subscription_status' => $plan,
            'subscription_ends_at' => $billing === 'yearly' ? now()->addYear() : now()->addMonth(),
            'is_trial_active' => false,
            'properties_limit' => $plan === 'starter' ? 1 : 5,
        ];

        // Store additional accommodations if any
        if ($additionalAccommodations > 0) {
            $updateData['additional_accommodations'] = $additionalAccommodations;
        }

        $user->update($updateData);

        Log::info('User subscription updated successfully', [
            'user_id' => $user->id,
            'plan' => $plan,
            'billing' => $billing,
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
}
