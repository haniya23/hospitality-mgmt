<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->subscription = Subscription::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_create_order_requires_authentication(): void
    {
        $response = $this->postJson('/api/subscription/create-order', [
            'plan' => 'starter',
            'billing_interval' => 'month',
            'quantity' => 1
        ]);

        $response->assertStatus(401);
    }

    public function test_create_order_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plan', 'billing_interval', 'quantity']);
    }

    public function test_create_order_validates_plan_choices(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'invalid_plan',
                'billing_interval' => 'month',
                'quantity' => 1
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plan']);
    }

    public function test_create_order_validates_billing_interval(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'starter',
                'billing_interval' => 'invalid',
                'quantity' => 1
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['billing_interval']);
    }

    public function test_create_order_validates_quantity_range(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/create-order', [
                'plan' => 'starter',
                'billing_interval' => 'month',
                'quantity' => 0
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_create_order_returns_payment_url(): void
    {
        // Mock the SubscriptionService
        $mockService = Mockery::mock(SubscriptionService::class);
        $mockService->shouldReceive('createSubscription')
            ->once()
            ->andReturn($this->subscription);

        $this->app->instance(SubscriptionService::class, $mockService);

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
    }

    public function test_add_accommodations_requires_authentication(): void
    {
        $response = $this->postJson('/api/subscription/addons', [
            'quantity' => 5
        ]);

        $response->assertStatus(401);
    }

    public function test_add_accommodations_validates_quantity(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/addons', [
                'quantity' => 0
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_add_accommodations_requires_active_subscription(): void
    {
        // Create a user without active subscription
        $userWithoutSubscription = User::factory()->create();

        $response = $this->actingAs($userWithoutSubscription)
            ->postJson('/api/subscription/addons', [
                'quantity' => 5
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No active subscription found'
            ]);
    }

    public function test_add_accommodations_requires_professional_plan(): void
    {
        // Create a starter subscription
        $starterSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'starter',
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/addons', [
                'quantity' => 5
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Add-ons are only available for Professional plan'
            ]);
    }

    public function test_add_accommodations_success(): void
    {
        // Create a professional subscription
        $professionalSubscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_slug' => 'professional',
            'status' => 'active'
        ]);

        // Mock the SubscriptionService
        $mockService = Mockery::mock(SubscriptionService::class);
        $mockService->shouldReceive('getActiveSubscription')
            ->once()
            ->andReturn($professionalSubscription);
        $mockService->shouldReceive('addAccommodations')
            ->once()
            ->andReturn($professionalSubscription);

        $this->app->instance(SubscriptionService::class, $mockService);

        $response = $this->actingAs($this->user)
            ->postJson('/api/subscription/addons', [
                'quantity' => 5
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Add-ons added successfully'
            ]);
    }

    public function test_status_requires_authentication(): void
    {
        $response = $this->getJson('/api/subscription/status');

        $response->assertStatus(401);
    }

    public function test_status_returns_subscription_data(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/subscription/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'subscription' => [
                    'id',
                    'plan_slug',
                    'plan_name',
                    'status',
                    'base_accommodation_limit',
                    'addon_count',
                    'total_accommodations',
                    'current_period_end',
                    'days_remaining'
                ]
            ]);
    }

    public function test_status_returns_null_for_no_subscription(): void
    {
        $userWithoutSubscription = User::factory()->create();

        $response = $this->actingAs($userWithoutSubscription)
            ->getJson('/api/subscription/status');

        $response->assertStatus(200)
            ->assertJson([
                'subscription' => null
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
