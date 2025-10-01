<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionAddon;
use App\Models\SubscriptionHistory;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Create a new subscription for a user
     */
    public function createSubscription(User $user, string $planSlug, array $options = []): Subscription
    {
        return DB::transaction(function () use ($user, $planSlug, $options) {
            // Get plan configuration
            $planConfig = $this->getPlanConfig($planSlug);
            
            // Calculate start and end dates
            $startAt = $options['start_at'] ?? now();
            $endAt = $this->calculateEndDate($startAt, $planConfig['interval'], $planConfig['interval_count']);
            
            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_slug' => $planSlug,
                'plan_name' => $planConfig['name'],
                'status' => $options['status'] ?? 'active',
                'base_accommodation_limit' => $planConfig['accommodation_limit'],
                'addon_count' => 0,
                'start_at' => $startAt,
                'current_period_end' => $endAt,
                'billing_interval' => $planConfig['interval'],
                'price_cents' => $planConfig['amount'] * 100,
                'currency' => $planConfig['currency'],
                'cashfree_order_id' => $options['cashfree_order_id'] ?? null,
            ]);
            
            // Log subscription creation
            $this->logSubscriptionAction($subscription, 'created', [
                'plan' => $planSlug,
                'start_at' => $startAt,
                'end_at' => $endAt,
            ], $options['performed_by'] ?? 'system');
            
            return $subscription;
        });
    }
    
    /**
     * Upgrade a subscription to a new plan
     */
    public function upgradeSubscription(Subscription $subscription, string $newPlanSlug, array $options = []): Subscription
    {
        return DB::transaction(function () use ($subscription, $newPlanSlug, $options) {
            $oldPlan = $subscription->plan_slug;
            $newPlanConfig = $this->getPlanConfig($newPlanSlug);
            
            // Update subscription
            $subscription->update([
                'plan_slug' => $newPlanSlug,
                'plan_name' => $newPlanConfig['name'],
                'base_accommodation_limit' => $newPlanConfig['accommodation_limit'],
                'price_cents' => $newPlanConfig['amount'] * 100,
                'cashfree_order_id' => $options['cashfree_order_id'] ?? $subscription->cashfree_order_id,
            ]);
            
            // Log upgrade
            $this->logSubscriptionAction($subscription, 'upgraded', [
                'from_plan' => $oldPlan,
                'to_plan' => $newPlanSlug,
                'old_limit' => $subscription->base_accommodation_limit,
                'new_limit' => $newPlanConfig['accommodation_limit'],
            ], $options['performed_by'] ?? 'system');
            
            return $subscription->fresh();
        });
    }
    
    /**
     * Add accommodations to a subscription
     */
    public function addAccommodations(Subscription $subscription, int $quantity, array $options = []): SubscriptionAddon
    {
        return DB::transaction(function () use ($subscription, $quantity, $options) {
            $unitPrice = 9900; // ₹99 in cents
            $cycleStart = $options['cycle_start'] ?? now();
            $cycleEnd = $this->calculateEndDate($cycleStart, 'month', 1);
            
            // Create addon
            $addon = SubscriptionAddon::create([
                'subscription_id' => $subscription->id,
                'qty' => $quantity,
                'unit_price_cents' => $unitPrice,
                'cycle_start' => $cycleStart,
                'cycle_end' => $cycleEnd,
            ]);
            
            // Update subscription addon count
            $subscription->increment('addon_count', $quantity);
            
            // Log addon addition
            $this->logSubscriptionAction($subscription, 'addon_added', [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ], $options['performed_by'] ?? 'system');
            
            return $addon;
        });
    }
    
    /**
     * Extend a subscription
     */
    public function extendSubscription(Subscription $subscription, int $months, array $options = []): Subscription
    {
        return DB::transaction(function () use ($subscription, $months, $options) {
            $oldEndDate = $subscription->current_period_end;
            $newEndDate = $oldEndDate->copy()->addMonths($months);
            
            $subscription->update([
                'current_period_end' => $newEndDate,
            ]);
            
            // Log extension
            $this->logSubscriptionAction($subscription, 'extended', [
                'months' => $months,
                'old_end_date' => $oldEndDate,
                'new_end_date' => $newEndDate,
            ], $options['performed_by'] ?? 'admin');
            
            return $subscription->fresh();
        });
    }
    
    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Subscription $subscription, array $options = []): Subscription
    {
        return DB::transaction(function () use ($subscription, $options) {
            $subscription->update([
                'status' => 'cancelled',
            ]);
            
            // Log cancellation
            $this->logSubscriptionAction($subscription, 'cancelled', [
                'reason' => $options['reason'] ?? 'User requested',
            ], $options['performed_by'] ?? 'system');
            
            return $subscription->fresh();
        });
    }
    
    /**
     * Get plan configuration
     */
    private function getPlanConfig(string $planSlug): array
    {
        $plans = [
            'trial' => [
                'name' => 'Trial Plan',
                'amount' => 0,
                'currency' => 'INR',
                'interval' => 'month',
                'interval_count' => 1,
                'accommodation_limit' => 3,
            ],
            'starter' => [
                'name' => 'Starter Plan',
                'amount' => 299,
                'currency' => 'INR',
                'interval' => 'month',
                'interval_count' => 1,
                'accommodation_limit' => 3,
            ],
            'professional' => [
                'name' => 'Professional Plan',
                'amount' => 999,
                'currency' => 'INR',
                'interval' => 'month',
                'interval_count' => 1,
                'accommodation_limit' => 15,
            ],
        ];
        
        return $plans[$planSlug] ?? $plans['trial'];
    }
    
    /**
     * Calculate end date based on interval
     */
    private function calculateEndDate(Carbon $startDate, string $interval, int $count): Carbon
    {
        return match ($interval) {
            'month' => $startDate->copy()->addMonths($count),
            'year' => $startDate->copy()->addYears($count),
            default => $startDate->copy()->addMonths($count),
        };
    }
    
    /**
     * Log subscription action
     */
    private function logSubscriptionAction(Subscription $subscription, string $action, array $data = [], string $performedBy = 'system'): void
    {
        SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'action' => $action,
            'data' => $data,
            'performed_by' => $performedBy,
        ]);
    }
    
    /**
     * Get user's active subscription
     */
    public function getActiveSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->first();
    }
    
    /**
     * Check if user can create property
     */
    public function canCreateProperty(User $user): bool
    {
        $subscription = $this->getActiveSubscription($user);
        
        if (!$subscription) {
            return false;
        }
        
        $propertyCount = $user->properties()->count();
        $maxProperties = $this->getMaxPropertiesForPlan($subscription->plan_slug);
        
        return $propertyCount < $maxProperties;
    }
    
    /**
     * Check if user can create accommodation
     */
    public function canCreateAccommodation(User $user): bool
    {
        $subscription = $this->getActiveSubscription($user);
        
        if (!$subscription) {
            return false;
        }
        
        $accommodationCount = $user->properties()->withCount('accommodations')->get()->sum('accommodations_count');
        $maxAccommodations = $subscription->total_accommodations;
        
        return $accommodationCount < $maxAccommodations;
    }
    
    /**
     * Get max properties for plan
     */
    private function getMaxPropertiesForPlan(string $planSlug): int
    {
        return match ($planSlug) {
            'trial' => 1,
            'starter' => 1,
            'professional' => 5,
            default => 1,
        };
    }
}
