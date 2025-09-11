<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'mobile_number',
        'date_of_birth',
        'gender',
        'address',
        'id_type',
        'id_number',
        'loyalty_points',
        'total_stays',
        'last_stay_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_stay_at' => 'datetime',
        'loyalty_points' => 'integer',
        'total_stays' => 'integer',
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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Loyalty system methods
    public function addLoyaltyPoints($points)
    {
        $this->increment('loyalty_points', $points);
    }

    public function redeemLoyaltyPoints($points)
    {
        if ($this->loyalty_points >= $points) {
            $this->decrement('loyalty_points', $points);
            return true;
        }
        return false;
    }

    public function updateStayHistory()
    {
        $this->increment('total_stays');
        $this->update(['last_stay_at' => now()]);
    }

    // Find customer by mobile (primary identifier)
    public static function findByMobile($mobile)
    {
        return static::where('mobile_number', $mobile)->first();
    }

    // Check if this is a repeat customer
    public function isRepeatCustomer()
    {
        return $this->total_stays > 0;
    }

    // Calculate loyalty discount based on points
    public function calculateLoyaltyDiscount($bookingAmount, $pointsToRedeem = null)
    {
        $pointsToRedeem = $pointsToRedeem ?? min($this->loyalty_points, floor($bookingAmount * 0.1)); // Max 10% of booking
        $discountAmount = $pointsToRedeem * 0.01; // 1 point = ₹0.01
        return min($discountAmount, $bookingAmount * 0.2); // Max 20% discount
    }
}