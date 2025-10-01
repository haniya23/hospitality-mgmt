<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\Payment;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminSubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $user;
    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create();
        $this->subscription = Subscription::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_admin_can_view_subscriptions(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/subscriptions');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_subscription_details(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/subscriptions/' . $this->subscription->id);

        $response->assertStatus(200);
    }

    public function test_admin_can_extend_subscription(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/subscriptions/' . $this->subscription->id . '/extend', [
                'months' => 3
            ]);

        $response->assertStatus(200);

        $this->subscription->refresh();
        $this->assertTrue($this->subscription->current_period_end->gt(now()->addMonths(2)));

        $this->assertDatabaseHas('subscription_history', [
            'subscription_id' => $this->subscription->id,
            'action' => 'extended',
            'performed_by' => 'admin'
        ]);
    }

    public function test_admin_can_cancel_subscription(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/subscriptions/' . $this->subscription->id . '/cancel');

        $response->assertStatus(200);

        $this->subscription->refresh();
        $this->assertEquals('cancelled', $this->subscription->status);

        $this->assertDatabaseHas('subscription_history', [
            'subscription_id' => $this->subscription->id,
            'action' => 'cancelled',
            'performed_by' => 'admin'
        ]);
    }

    public function test_admin_can_view_subscription_history(): void
    {
        // Create some history entries
        SubscriptionHistory::factory()->create([
            'subscription_id' => $this->subscription->id,
            'action' => 'created',
            'performed_by' => 'user'
        ]);

        SubscriptionHistory::factory()->create([
            'subscription_id' => $this->subscription->id,
            'action' => 'upgraded',
            'performed_by' => 'system'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/subscription-histories');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_webhooks(): void
    {
        // Create some webhook entries
        Webhook::factory()->create([
            'provider' => 'cashfree',
            'event_id' => 'test_event_1',
            'processed' => true
        ]);

        Webhook::factory()->create([
            'provider' => 'cashfree',
            'event_id' => 'test_event_2',
            'processed' => false
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/webhooks');

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_webhooks_by_status(): void
    {
        Webhook::factory()->create([
            'provider' => 'cashfree',
            'processed' => true
        ]);

        Webhook::factory()->create([
            'provider' => 'cashfree',
            'processed' => false
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/webhooks?processed=1');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_payments(): void
    {
        // Create some payment entries
        Payment::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'status' => 'completed'
        ]);

        Payment::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'status' => 'failed'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/payments');

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_payments_by_status(): void
    {
        Payment::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'status' => 'completed'
        ]);

        Payment::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'status' => 'failed'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/payments?status=completed');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_subscription_stats(): void
    {
        // Create subscriptions with different statuses
        Subscription::factory()->create(['status' => 'active']);
        Subscription::factory()->create(['status' => 'trial']);
        Subscription::factory()->create(['status' => 'expired']);

        $response = $this->actingAs($this->admin)
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_export_subscription_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/subscriptions/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_bulk_extend_subscriptions(): void
    {
        $subscription1 = Subscription::factory()->create(['status' => 'active']);
        $subscription2 = Subscription::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin)
            ->post('/admin/subscriptions/bulk-extend', [
                'subscription_ids' => [$subscription1->id, $subscription2->id],
                'months' => 2
            ]);

        $response->assertStatus(200);

        $subscription1->refresh();
        $subscription2->refresh();

        $this->assertTrue($subscription1->current_period_end->gt(now()->addMonth()));
        $this->assertTrue($subscription2->current_period_end->gt(now()->addMonth()));
    }

    public function test_non_admin_cannot_access_admin_features(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/subscriptions');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_reconciliation_report(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/reconciliation');

        $response->assertStatus(200);
    }

    public function test_admin_can_run_reconciliation_command(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/reports/reconciliation/run');

        $response->assertStatus(200);
    }
}
