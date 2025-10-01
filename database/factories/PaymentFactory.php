<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['completed', 'failed', 'pending', 'refunded']);
        $method = $this->faker->randomElement(['card', 'upi', 'netbanking', 'wallet']);
        $amount = $this->faker->randomElement([29900, 99900, 9900]); // Common subscription amounts
        
        return [
            'user_id' => \App\Models\User::factory(),
            'subscription_id' => \App\Models\Subscription::factory(),
            'cashfree_order_id' => 'order_' . $this->faker->uuid(),
            'payment_id' => 'payment_' . $this->faker->uuid(),
            'amount_cents' => $amount,
            'currency' => 'INR',
            'method' => $method,
            'status' => $status,
            'paid_at' => $status === 'completed' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'raw_response' => $this->getRawResponse($status, $method),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'card',
        ]);
    }

    public function upi(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'upi',
        ]);
    }

    public function netbanking(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'netbanking',
        ]);
    }

    public function wallet(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'wallet',
        ]);
    }

    private function getRawResponse(string $status, string $method): array
    {
        $baseResponse = [
            'cf_payment_id' => 'payment_' . $this->faker->uuid(),
            'cf_order_id' => 'order_' . $this->faker->uuid(),
            'order_id' => 'order_' . $this->faker->uuid(),
            'entity' => 'payment',
            'payment_amount' => 299.00,
            'payment_currency' => 'INR',
            'payment_method' => $method,
            'payment_group' => $this->getPaymentGroup($method),
            'payment_time' => now()->toISOString(),
            'customer_details' => [
                'customer_id' => 'customer_' . $this->faker->uuid(),
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'customer_phone' => $this->faker->phoneNumber(),
            ]
        ];

        return match ($status) {
            'completed' => array_merge($baseResponse, [
                'is_captured' => true,
                'payment_status' => 'SUCCESS',
            ]),
            'failed' => array_merge($baseResponse, [
                'is_captured' => false,
                'payment_status' => 'FAILED',
            ]),
            'pending' => array_merge($baseResponse, [
                'is_captured' => false,
                'payment_status' => 'PENDING',
            ]),
            'refunded' => array_merge($baseResponse, [
                'is_captured' => true,
                'payment_status' => 'SUCCESS',
                'refund_status' => 'SUCCESS',
            ]),
            default => $baseResponse,
        };
    }

    private function getPaymentGroup(string $method): string
    {
        return match ($method) {
            'card' => 'credit_card',
            'upi' => 'upi',
            'netbanking' => 'net_banking',
            'wallet' => 'wallet',
            default => 'credit_card',
        };
    }
}
