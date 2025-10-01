<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Webhook;
use App\Jobs\ProcessCashfreeWebhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_complete_trial_to_starter_flow(): void
    {
        // 1. User starts with trial subscription
        $trialSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'trial',
            'status' => 'trial',
            'base_accommodation_limit' => 3,
            'addon_count' => 0
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_slug' => 'trial',
            'status' => 'trial'
        ]);

        // 2. User creates order for starter plan
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'starter',
                'billing_interval' => 'month',
                'quantity' => 1
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'order_id',
                'payment_url'
            ]);

        $orderId = $response->json('order_id');

        // 3. Simulate successful payment webhook
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'payload' => $this->getPaymentSuccessPayload($orderId),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        // 4. Verify subscription was upgraded
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_slug' => 'starter',
            'status' => 'active',
            'cashfree_order_id' => $orderId
        ]);

        // 5. Verify payment was recorded
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'cashfree_order_id' => $orderId,
            'status' => 'completed',
            'amount_cents' => 29900
        ]);

        // 6. Verify subscription history was logged
        $this->assertDatabaseHas('subscription_history', [
            'subscription_id' => $trialSubscription->id,
            'action' => 'upgraded',
            'performed_by' => 'system'
        ]);
    }

    public function test_complete_starter_to_professional_flow(): void
    {
        // 1. User has active starter subscription
        $starterSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'starter',
            'status' => 'active',
            'base_accommodation_limit' => 3,
            'addon_count' => 0
        ]);

        // 2. User creates order for professional plan
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'professional',
                'billing_interval' => 'month',
                'quantity' => 1
            ]);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');

        // 3. Simulate successful payment webhook
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'payload' => $this->getPaymentSuccessPayload($orderId, 999.00),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        // 4. Verify subscription was upgraded
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_slug' => 'professional',
            'status' => 'active',
            'base_accommodation_limit' => 15
        ]);

        // 5. Verify payment was recorded
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'cashfree_order_id' => $orderId,
            'status' => 'completed',
            'amount_cents' => 99900
        ]);
    }

    public function test_complete_professional_addon_flow(): void
    {
        // 1. User has active professional subscription
        $professionalSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'professional',
            'status' => 'active',
            'base_accommodation_limit' => 15,
            'addon_count' => 0
        ]);

        // 2. User adds accommodations
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/addons', [
                'quantity' => 5
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Add-ons added successfully'
            ]);

        // 3. Verify subscription was updated
        $professionalSubscription->refresh();
        $this->assertEquals(5, $professionalSubscription->addon_count);
        $this->assertEquals(20, $professionalSubscription->total_accommodations);

        // 4. Verify subscription history was logged
        $this->assertDatabaseHas('subscription_history', [
            'subscription_id' => $professionalSubscription->id,
            'action' => 'addon_added',
            'performed_by' => 'user'
        ]);
    }

    public function test_subscription_limits_enforcement(): void
    {
        // 1. User has starter subscription (1 property, 3 accommodations)
        $starterSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'starter',
            'status' => 'active',
            'base_accommodation_limit' => 3,
            'addon_count' => 0
        ]);

        // 2. Create a property
        $property = $this->user->properties()->create([
            'name' => 'Test Property',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city_id' => 1,
            'state_id' => 1,
            'country_id' => 1,
            'pincode_id' => 1,
        ]);

        // 3. Create 3 accommodations (should be allowed)
        for ($i = 1; $i <= 3; $i++) {
            $property->accommodations()->create([
                'name' => "Accommodation {$i}",
                'description' => "Description {$i}",
                'base_price' => 1000,
                'max_guests' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
            ]);
        }

        $this->assertEquals(3, $property->accommodations()->count());

        // 4. Try to create 4th accommodation (should be blocked)
        $response = $this->actingAs($this->user)
            ->postJson('/api/properties/' . $property->id . '/accommodations', [
                'name' => 'Accommodation 4',
                'description' => 'Description 4',
                'base_price' => 1000,
                'max_guests' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Accommodation limit exceeded for your subscription plan'
            ]);
    }

    public function test_trial_expiry_flow(): void
    {
        // 1. User has expired trial subscription
        $expiredTrial = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'trial',
            'status' => 'trial',
            'current_period_end' => now()->subDays(1)
        ]);

        // 2. Try to access protected route
        $response = $this->actingAs($this->user)
            ->getJson('/api/subscription/status');

        $response->assertStatus(200);
        
        $subscription = $response->json('subscription');
        $this->assertEquals('trial', $subscription['status']);
        $this->assertLessThan(0, $subscription['days_remaining']);

        // 3. User should be redirected to plans page when accessing dashboard
        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertRedirect('/subscription/plans');
    }

    public function test_payment_failure_flow(): void
    {
        // 1. User creates order for starter plan
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'starter',
                'billing_interval' => 'month',
                'quantity' => 1
            ]);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');

        // 2. Simulate failed payment webhook
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_failed_123',
            'payload' => $this->getPaymentFailedPayload($orderId),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        // 3. Verify payment was recorded as failed
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'cashfree_order_id' => $orderId,
            'status' => 'failed'
        ]);

        // 4. Verify subscription remains in pending state
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'cashfree_order_id' => $orderId,
            'status' => 'pending'
        ]);
    }

    private function getPaymentSuccessPayload(string $orderId, float $amount = 299.00): array
    {
        return [
            'type' => 'PAYMENT_SUCCESS',
            'data' => [
                'payment' => [
                    'cf_payment_id' => 'test_payment_123',
                    'cf_order_id' => $orderId,
                    'order_id' => $orderId,
                    'entity' => 'payment',
                    'is_captured' => true,
                    'payment_amount' => $amount,
                    'payment_currency' => 'INR',
                    'payment_status' => 'SUCCESS',
                    'payment_method' => 'card',
                    'payment_group' => 'credit_card',
                    'payment_time' => now()->toISOString(),
                    'customer_details' => [
                        'customer_id' => 'test_customer',
                        'customer_name' => 'Test Customer',
                        'customer_email' => 'test@example.com',
                        'customer_phone' => '9999999999'
                    ]
                ]
            ]
        ];
    }

    private function getPaymentFailedPayload(string $orderId): array
    {
        return [
            'type' => 'PAYMENT_FAILED',
            'data' => [
                'payment' => [
                    'cf_payment_id' => 'test_payment_failed_123',
                    'cf_order_id' => $orderId,
                    'order_id' => $orderId,
                    'entity' => 'payment',
                    'is_captured' => false,
                    'payment_amount' => 299.00,
                    'payment_currency' => 'INR',
                    'payment_status' => 'FAILED',
                    'payment_method' => 'card',
                    'payment_group' => 'credit_card',
                    'payment_time' => now()->toISOString(),
                    'customer_details' => [
                        'customer_id' => 'test_customer',
                        'customer_name' => 'Test Customer',
                        'customer_email' => 'test@example.com',
                        'customer_phone' => '9999999999'
                    ]
                ]
            ]
        ];
    }
}
