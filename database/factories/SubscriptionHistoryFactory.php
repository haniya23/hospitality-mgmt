<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionHistory>
 */
class SubscriptionHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $action = $this->faker->randomElement(['created', 'upgraded', 'addon_added', 'cancelled', 'extended']);
        $performedBy = $this->faker->randomElement(['user', 'admin', 'system']);
        
        return [
            'subscription_id' => \App\Models\Subscription::factory(),
            'action' => $action,
            'data' => $this->getActionData($action),
            'performed_by' => $performedBy,
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'created',
            'data' => ['plan' => 'trial', 'accommodation_limit' => 3],
            'performed_by' => 'system',
        ]);
    }

    public function upgraded(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'upgraded',
            'data' => ['from_plan' => 'trial', 'to_plan' => 'starter'],
            'performed_by' => 'system',
        ]);
    }

    public function addonAdded(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'addon_added',
            'data' => ['quantity' => 5, 'unit_price_cents' => 9900],
            'performed_by' => 'user',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'cancelled',
            'data' => ['reason' => 'user_request'],
            'performed_by' => 'admin',
        ]);
    }

    public function extended(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'extended',
            'data' => ['months' => 3, 'new_end_date' => now()->addMonths(3)->toISOString()],
            'performed_by' => 'admin',
        ]);
    }

    private function getActionData(string $action): array
    {
        return match ($action) {
            'created' => ['plan' => 'trial', 'accommodation_limit' => 3],
            'upgraded' => ['from_plan' => 'trial', 'to_plan' => 'starter'],
            'addon_added' => ['quantity' => 5, 'unit_price_cents' => 9900],
            'cancelled' => ['reason' => 'user_request'],
            'extended' => ['months' => 3, 'new_end_date' => now()->addMonths(3)->toISOString()],
            default => [],
        };
    }
}
