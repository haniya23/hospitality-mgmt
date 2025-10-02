<?php

namespace App\Filament\Resources\SubscriptionPlansResource\Pages;

use App\Filament\Resources\SubscriptionPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriptionPlans extends CreateRecord
{
    protected static string $resource = SubscriptionPlansResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values based on plan type
        if (isset($data['plan_slug'])) {
            $planDefaults = match($data['plan_slug']) {
                'trial' => [
                    'plan_name' => 'Trial Plan',
                    'base_accommodation_limit' => 2,
                    'price_cents' => 0,
                    'billing_interval' => 'monthly',
                    'current_period_end' => now()->addDays(14),
                ],
                'starter' => [
                    'plan_name' => 'Starter Plan',
                    'base_accommodation_limit' => 5,
                    'price_cents' => 99900, // ₹999
                    'billing_interval' => 'monthly',
                    'current_period_end' => now()->addMonth(),
                ],
                'professional' => [
                    'plan_name' => 'Professional Plan',
                    'base_accommodation_limit' => 15,
                    'price_cents' => 199900, // ₹1999
                    'billing_interval' => 'monthly',
                    'current_period_end' => now()->addMonth(),
                ],
                default => [],
            };

            // Merge defaults with provided data (provided data takes precedence)
            $data = array_merge($planDefaults, $data);
        }

        // Set default currency if not provided
        $data['currency'] = $data['currency'] ?? 'INR';

        // Set default start date if not provided
        $data['start_at'] = $data['start_at'] ?? now();

        // Ensure addon_count is set
        $data['addon_count'] = $data['addon_count'] ?? 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create add-ons if provided
        if (isset($this->data['addons']) && is_array($this->data['addons'])) {
            foreach ($this->data['addons'] as $addonData) {
                if (isset($addonData['qty']) && isset($addonData['unit_price_cents'])) {
                    $this->record->addons()->create($addonData);
                    
                    // Update addon count
                    $this->record->increment('addon_count', $addonData['qty']);
                }
            }
        }

        // Log subscription creation
        activity()
            ->performedOn($this->record)
            ->causedBy(auth()->user())
            ->log('Subscription created');
    }
}
