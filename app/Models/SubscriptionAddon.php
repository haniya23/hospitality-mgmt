<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionAddon extends Model
{
    protected $fillable = [
        'subscription_id',
        'qty',
        'unit_price',
        'cycle_start',
        'cycle_end',
    ];

    protected $casts = [
        'cycle_start' => 'datetime',
        'cycle_end' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->qty * $this->unit_price;
    }
}
