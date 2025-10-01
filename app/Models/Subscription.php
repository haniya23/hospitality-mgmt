<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_slug',
        'plan_name',
        'status',
        'base_accommodation_limit',
        'addon_count',
        'start_at',
        'current_period_end',
        'billing_interval',
        'price_cents',
        'currency',
        'cashfree_order_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(SubscriptionAddon::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalAccommodationsAttribute(): int
    {
        return $this->base_accommodation_limit + $this->addon_count;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->current_period_end->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->current_period_end->isPast();
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    public function getTotalAddonAmountAttribute(): float
    {
        return $this->addons()->where('cycle_end', '>', now())->get()->sum(function($addon) {
            return $addon->qty * $addon->unit_price;
        });
    }

    public function getPriceAttribute(): float
    {
        return $this->price_cents / 100; // Convert cents to rupees
    }

    public function getTotalSubscriptionAmountAttribute(): float
    {
        return $this->price + $this->total_addon_amount;
    }

    public function getActiveAddonsAttribute()
    {
        return $this->addons()->where('cycle_end', '>', now())->get();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('current_period_end', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('current_period_end', '<', now());
    }

    public function scopeByPlan($query, string $plan)
    {
        return $query->where('plan_slug', $plan);
    }
}
