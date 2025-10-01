<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventType = $this->faker->randomElement(['PAYMENT_SUCCESS', 'PAYMENT_FAILED', 'PAYMENT_USER_DROPPED']);
        $eventId = 'event_' . $this->faker->uuid();
        $orderId = 'order_' . $this->faker->uuid();
        
        return [
            'provider' => 'cashfree',
            'event_id' => $eventId,
            'payload' => $this->getWebhookPayload($eventType, $eventId, $orderId),
            'signature_header' => 'signature_' . $this->faker->sha256(),
            'received_at' => now(),
            'processed' => $this->faker->boolean(80), // 80% chance of being processed
            'processed_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 hour', 'now'),
            'error_message' => $this->faker->optional(0.1)->sentence(),
        ];
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed' => true,
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }

    public function unprocessed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed' => false,
            'processed_at' => null,
            'error_message' => null,
        ]);
    }

    public function withError(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed' => true,
            'processed_at' => now(),
            'error_message' => 'Processing failed: ' . $this->faker->sentence(),
        ]);
    }

    public function paymentSuccess(): static
    {
        $eventId = 'payment_success_' . $this->faker->uuid();
        $orderId = 'order_' . $this->faker->uuid();
        
        return $this->state(fn (array $attributes) => [
            'event_id' => $eventId,
            'payload' => $this->getWebhookPayload('PAYMENT_SUCCESS', $eventId, $orderId),
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    public function paymentFailed(): static
    {
        $eventId = 'payment_failed_' . $this->faker->uuid();
        $orderId = 'order_' . $this->faker->uuid();
        
        return $this->state(fn (array $attributes) => [
            'event_id' => $eventId,
            'payload' => $this->getWebhookPayload('PAYMENT_FAILED', $eventId, $orderId),
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    private function getWebhookPayload(string $eventType, string $eventId, string $orderId): array
    {
        $basePayload = [
            'type' => $eventType,
            'data' => [
                'payment' => [
                    'cf_payment_id' => $eventId,
                    'cf_order_id' => $orderId,
                    'order_id' => $orderId,
                    'entity' => 'payment',
                    'payment_amount' => 299.00,
                    'payment_currency' => 'INR',
                    'payment_method' => 'card',
                    'payment_group' => 'credit_card',
                    'payment_time' => now()->toISOString(),
                    'customer_details' => [
                        'customer_id' => 'customer_' . $this->faker->uuid(),
                        'customer_name' => $this->faker->name(),
                        'customer_email' => $this->faker->email(),
                        'customer_phone' => $this->faker->phoneNumber(),
                    ]
                ]
            ]
        ];

        return match ($eventType) {
            'PAYMENT_SUCCESS' => array_merge($basePayload, [
                'data' => [
                    'payment' => array_merge($basePayload['data']['payment'], [
                        'is_captured' => true,
                        'payment_status' => 'SUCCESS',
                    ])
                ]
            ]),
            'PAYMENT_FAILED' => array_merge($basePayload, [
                'data' => [
                    'payment' => array_merge($basePayload['data']['payment'], [
                        'is_captured' => false,
                        'payment_status' => 'FAILED',
                    ])
                ]
            ]),
            'PAYMENT_USER_DROPPED' => array_merge($basePayload, [
                'data' => [
                    'payment' => array_merge($basePayload['data']['payment'], [
                        'is_captured' => false,
                        'payment_status' => 'USER_DROPPED',
                    ])
                ]
            ]),
            default => $basePayload,
        };
    }
}
