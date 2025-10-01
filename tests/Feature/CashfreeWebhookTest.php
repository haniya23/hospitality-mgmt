<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Webhook;
use App\Models\Payment;
use App\Jobs\ProcessCashfreeWebhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CashfreeWebhookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'cashfree_order_id' => 'test_order_123'
        ]);
    }

    public function test_webhook_requires_signature(): void
    {
        $payload = $this->getPaymentSuccessPayload();

        $response = $this->postJson('/cashfree/webhook', $payload);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid signature']);
    }

    public function test_webhook_with_valid_signature_stores_webhook(): void
    {
        Queue::fake();
        
        $payload = $this->getPaymentSuccessPayload();
        $signature = $this->generateWebhookSignature(json_encode($payload));

        $response = $this->withHeaders([
            'x-webhook-signature' => $signature
        ])->postJson('/cashfree/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('webhooks', [
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'processed' => false
        ]);

        Queue::assertPushed(ProcessCashfreeWebhook::class);
    }

    public function test_webhook_processing_creates_payment_record(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'payload' => $this->getPaymentSuccessPayload(),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'cashfree_order_id' => 'test_order_123',
            'payment_id' => 'test_payment_123',
            'amount_cents' => 29900,
            'status' => 'completed'
        ]);

        $webhook->refresh();
        $this->assertTrue($webhook->processed);
        $this->assertNotNull($webhook->processed_at);
    }

    public function test_webhook_processing_handles_payment_failure(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_failed_123',
            'payload' => $this->getPaymentFailedPayload(),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'cashfree_order_id' => 'test_order_123',
            'payment_id' => 'test_payment_failed_123',
            'amount_cents' => 29900,
            'status' => 'failed'
        ]);
    }

    public function test_webhook_processing_handles_payment_dropped(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_dropped_123',
            'payload' => $this->getPaymentDroppedPayload(),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'cashfree_order_id' => 'test_order_123',
            'payment_id' => 'test_payment_dropped_123',
            'amount_cents' => 29900,
            'status' => 'failed'
        ]);
    }

    public function test_webhook_processing_is_idempotent(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'payload' => $this->getPaymentSuccessPayload(),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        // Process the webhook twice
        $job1 = new ProcessCashfreeWebhook($webhook->id);
        $job1->handle();

        $job2 = new ProcessCashfreeWebhook($webhook->id);
        $job2->handle();

        // Should only create one payment record
        $this->assertEquals(1, Payment::where('payment_id', 'test_payment_123')->count());
    }

    public function test_webhook_processing_handles_unknown_event_type(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_unknown_123',
            'payload' => $this->getUnknownEventPayload(),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        // Should not create a payment record for unknown events
        $this->assertEquals(0, Payment::where('payment_id', 'test_unknown_123')->count());

        $webhook->refresh();
        $this->assertTrue($webhook->processed);
        $this->assertNull($webhook->error_message);
    }

    public function test_webhook_processing_handles_missing_subscription(): void
    {
        $webhook = Webhook::create([
            'provider' => 'cashfree',
            'event_id' => 'test_payment_123',
            'payload' => $this->getPaymentSuccessPayload('nonexistent_order'),
            'signature_header' => 'test_signature',
            'received_at' => now(),
        ]);

        $job = new ProcessCashfreeWebhook($webhook->id);
        $job->handle();

        $webhook->refresh();
        $this->assertTrue($webhook->processed);
        $this->assertNotNull($webhook->error_message);
        $this->assertStringContains('Subscription not found', $webhook->error_message);
    }

    private function getPaymentSuccessPayload(string $orderId = 'test_order_123'): array
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
                    'payment_amount' => 299.00,
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

    private function getPaymentFailedPayload(): array
    {
        return [
            'type' => 'PAYMENT_FAILED',
            'data' => [
                'payment' => [
                    'cf_payment_id' => 'test_payment_failed_123',
                    'cf_order_id' => 'test_order_123',
                    'order_id' => 'test_order_123',
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

    private function getPaymentDroppedPayload(): array
    {
        return [
            'type' => 'PAYMENT_USER_DROPPED',
            'data' => [
                'payment' => [
                    'cf_payment_id' => 'test_payment_dropped_123',
                    'cf_order_id' => 'test_order_123',
                    'order_id' => 'test_order_123',
                    'entity' => 'payment',
                    'is_captured' => false,
                    'payment_amount' => 299.00,
                    'payment_currency' => 'INR',
                    'payment_status' => 'USER_DROPPED',
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

    private function getUnknownEventPayload(): array
    {
        return [
            'type' => 'UNKNOWN_EVENT',
            'data' => [
                'payment' => [
                    'cf_payment_id' => 'test_unknown_123',
                    'cf_order_id' => 'test_order_123',
                    'order_id' => 'test_order_123',
                ]
            ]
        ];
    }

    private function generateWebhookSignature(string $payload): string
    {
        $secret = config('cashfree.webhook_secret', 'test_secret');
        return hash_hmac('sha256', $payload, $secret);
    }
}
