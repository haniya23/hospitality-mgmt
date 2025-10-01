<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planSlug = $this->faker->randomElement(['trial', 'starter', 'professional']);
        $planConfig = $this->getPlanConfig($planSlug);
        
        return [
            'user_id' => \App\Models\User::factory(),
            'plan_slug' => $planSlug,
            'plan_name' => $planConfig['name'],
            'status' => $planSlug === 'trial' ? 'trial' : 'active',
            'base_accommodation_limit' => $planConfig['accommodation_limit'],
            'addon_count' => 0,
            'start_at' => now(),
            'current_period_end' => $planSlug === 'trial' ? now()->addDays(14) : now()->addMonth(),
            'billing_interval' => 'month',
            'price_cents' => $planConfig['price_cents'],
            'currency' => 'INR',
            'cashfree_order_id' => $planSlug === 'trial' ? null : 'order_' . $this->faker->uuid(),
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_slug' => 'trial',
            'plan_name' => 'Trial Plan',
            'status' => 'trial',
            'base_accommodation_limit' => 3,
            'price_cents' => 0,
            'current_period_end' => now()->addDays(14),
            'cashfree_order_id' => null,
        ]);
    }

    public function starter(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_slug' => 'starter',
            'plan_name' => 'Starter Plan',
            'status' => 'active',
            'base_accommodation_limit' => 3,
            'price_cents' => 29900,
            'current_period_end' => now()->addMonth(),
        ]);
    }

    public function professional(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_slug' => 'professional',
            'plan_name' => 'Professional Plan',
            'status' => 'active',
            'base_accommodation_limit' => 15,
            'price_cents' => 99900,
            'current_period_end' => now()->addMonth(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'current_period_end' => now()->subDays(1),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    private function getPlanConfig(string $planSlug): array
    {
        return match ($planSlug) {
            'trial' => [
                'name' => 'Trial Plan',
                'accommodation_limit' => 3,
                'price_cents' => 0,
            ],
            'starter' => [
                'name' => 'Starter Plan',
                'accommodation_limit' => 3,
                'price_cents' => 29900,
            ],
            'professional' => [
                'name' => 'Professional Plan',
                'accommodation_limit' => 15,
                'price_cents' => 99900,
            ],
            default => [
                'name' => 'Trial Plan',
                'accommodation_limit' => 3,
                'price_cents' => 0,
            ],
        };
    }
}
