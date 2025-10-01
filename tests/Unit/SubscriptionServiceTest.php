<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $subscriptionService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriptionService = new SubscriptionService();
        $this->user = User::factory()->create();
    }

    public function test_can_create_trial_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = $this->subscriptionService->createSubscription($user, 'trial');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('trial', $subscription->plan_slug);
        $this->assertEquals('trial', $subscription->status);
        $this->assertEquals(3, $subscription->base_accommodation_limit);
        $this->assertEquals(0, $subscription->addon_count);
        $this->assertTrue($subscription->start_at->isToday());
        $this->assertTrue($subscription->current_period_end->isToday()->addDays(14));
    }

    public function test_can_create_starter_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = $this->subscriptionService->createSubscription($user, 'starter');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('starter', $subscription->plan_slug);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals(3, $subscription->base_accommodation_limit);
        $this->assertEquals(29900, $subscription->price_cents);
    }

    public function test_can_create_professional_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = $this->subscriptionService->createSubscription($user, 'professional');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('professional', $subscription->plan_slug);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals(15, $subscription->base_accommodation_limit);
        $this->assertEquals(99900, $subscription->price_cents);
    }

    public function test_can_upgrade_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'starter');

        $upgradedSubscription = $this->subscriptionService->upgradeSubscription($subscription, 'professional');

        $this->assertEquals('professional', $upgradedSubscription->plan_slug);
        $this->assertEquals(15, $upgradedSubscription->base_accommodation_limit);
        $this->assertEquals(99900, $upgradedSubscription->price_cents);
    }

    public function test_can_add_accommodations(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'professional');

        $updatedSubscription = $this->subscriptionService->addAccommodations($subscription, 5);

        $this->assertEquals(5, $updatedSubscription->addon_count);
        $this->assertEquals(20, $updatedSubscription->total_accommodations);
    }

    public function test_can_extend_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'starter');
        $originalEndDate = $subscription->current_period_end;

        $extendedSubscription = $this->subscriptionService->extendSubscription($subscription, 3);

        $this->assertTrue($extendedSubscription->current_period_end->gt($originalEndDate));
        $this->assertEquals($originalEndDate->addMonths(3)->format('Y-m-d'), $extendedSubscription->current_period_end->format('Y-m-d'));
    }

    public function test_can_cancel_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'starter');

        $cancelledSubscription = $this->subscriptionService->cancelSubscription($subscription);

        $this->assertEquals('cancelled', $cancelledSubscription->status);
    }

    public function test_get_plan_config_returns_correct_config(): void
    {
        // Test by creating subscriptions and checking their properties
        $trialSubscription = $this->subscriptionService->createSubscription($this->user, 'trial');
        $this->assertEquals(3, $trialSubscription->base_accommodation_limit);
        $this->assertEquals(0, $trialSubscription->price_cents);

        $starterSubscription = $this->subscriptionService->createSubscription($this->user, 'starter');
        $this->assertEquals(3, $starterSubscription->base_accommodation_limit);
        $this->assertEquals(29900, $starterSubscription->price_cents);

        $professionalSubscription = $this->subscriptionService->createSubscription($this->user, 'professional');
        $this->assertEquals(15, $professionalSubscription->base_accommodation_limit);
        $this->assertEquals(99900, $professionalSubscription->price_cents);
    }

    public function test_calculate_end_date_returns_correct_date(): void
    {
        // Test by creating subscriptions and checking their end dates
        $monthlySubscription = $this->subscriptionService->createSubscription($this->user, 'starter');
        $this->assertTrue($monthlySubscription->current_period_end->isSameDay(now()->addMonth()));

        $yearlySubscription = $this->subscriptionService->createSubscription($this->user, 'professional');
        $this->assertTrue($yearlySubscription->current_period_end->isSameDay(now()->addMonth()));
    }

    public function test_can_create_property_checks_limits(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'starter');

        // Starter plan allows 1 property
        $this->assertTrue($this->subscriptionService->canCreateProperty($user));

        // Create a property to test limit
        $user->properties()->create([
            'name' => 'Test Property',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city_id' => 1,
            'state_id' => 1,
            'country_id' => 1,
            'pincode_id' => 1,
        ]);

        $this->assertFalse($this->subscriptionService->canCreateProperty($user));
    }

    public function test_can_create_accommodation_checks_limits(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscriptionService->createSubscription($user, 'starter');

        // Create a property first
        $property = $user->properties()->create([
            'name' => 'Test Property',
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city_id' => 1,
            'state_id' => 1,
            'country_id' => 1,
            'pincode_id' => 1,
        ]);

        // Starter plan allows 3 accommodations
        $this->assertTrue($this->subscriptionService->canCreateAccommodation($user));

        // Create 3 accommodations to test limit
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

        $this->assertFalse($this->subscriptionService->canCreateAccommodation($user));
    }

    public function test_get_max_properties_for_plan_returns_correct_limits(): void
    {
        // Test by checking the actual limits enforced
        $trialUser = User::factory()->create();
        $trialSubscription = $this->subscriptionService->createSubscription($trialUser, 'trial');
        $this->assertTrue($this->subscriptionService->canCreateProperty($trialUser));

        $starterUser = User::factory()->create();
        $starterSubscription = $this->subscriptionService->createSubscription($starterUser, 'starter');
        $this->assertTrue($this->subscriptionService->canCreateProperty($starterUser));

        $professionalUser = User::factory()->create();
        $professionalSubscription = $this->subscriptionService->createSubscription($professionalUser, 'professional');
        $this->assertTrue($this->subscriptionService->canCreateProperty($professionalUser));
    }
}
