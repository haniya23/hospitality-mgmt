<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'rule_name',
        'rule_type',
        'start_date',
        'end_date',
        'rate_adjustment',
        'percentage_adjustment',
        'min_stay_nights',
        'max_stay_nights',
        'applicable_days',
        'b2b_partner_id',
        'promo_code',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rate_adjustment' => 'decimal:2',
        'percentage_adjustment' => 'decimal:2',
        'applicable_days' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function b2bPartner()
    {
        return $this->belongsTo(B2bPartner::class);
    }

    // Check if rule applies to given dates and conditions
    public function appliesTo($checkIn, $checkOut, $stayNights = null, $b2bPartnerId = null)
    {
        if (!$this->is_active) {
            return false;
        }

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $stayNights = $stayNights ?? $checkInDate->diffInDays($checkOutDate);

        // Check date range
        if ($checkInDate->lt($this->start_date) || $checkInDate->gt($this->end_date)) {
            return false;
        }

        // Check minimum stay
        if ($this->min_stay_nights && $stayNights < $this->min_stay_nights) {
            return false;
        }

        // Check maximum stay
        if ($this->max_stay_nights && $stayNights > $this->max_stay_nights) {
            return false;
        }

        // Check applicable days of week
        if ($this->applicable_days && !in_array($checkInDate->dayOfWeek, $this->applicable_days)) {
            return false;
        }

        // Check B2B partner specific rules
        if ($this->rule_type === 'b2b_contract' && $this->b2b_partner_id !== $b2bPartnerId) {
            return false;
        }

        return true;
    }

    // Calculate adjusted rate
    public function calculateAdjustedRate($baseRate)
    {
        if ($this->rate_adjustment) {
            return $baseRate + $this->rate_adjustment;
        }

        if ($this->percentage_adjustment) {
            return $baseRate * (1 + ($this->percentage_adjustment / 100));
        }

        return $baseRate;
    }

    // Get applicable pricing rules for a property and date range
    public static function getApplicableRules($propertyId, $checkIn, $checkOut, $b2bPartnerId = null)
    {
        return static::where('property_id', $propertyId)
            ->where('is_active', true)
            ->where('start_date', '<=', $checkIn)
            ->where('end_date', '>=', $checkIn)
            ->when($b2bPartnerId, function ($query) use ($b2bPartnerId) {
                $query->where(function ($q) use ($b2bPartnerId) {
                    $q->whereNull('b2b_partner_id')
                      ->orWhere('b2b_partner_id', $b2bPartnerId);
                });
            })
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(function ($rule) use ($checkIn, $checkOut, $b2bPartnerId) {
                return $rule->appliesTo($checkIn, $checkOut, null, $b2bPartnerId);
            });
    }
}