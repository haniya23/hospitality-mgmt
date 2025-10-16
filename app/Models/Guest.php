<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory, HasCreatedUpdatedBy;

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
        'partner_id',
        'accommodation_id',
        'is_reserved',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_stay_at' => 'datetime',
        'loyalty_points' => 'integer',
        'total_stays' => 'integer',
        'is_reserved' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });

        // Prevent manual deletion of reserved customers
        static::deleting(function ($model) {
            if ($model->is_reserved && !app()->runningInConsole()) {
                throw new \Exception('Reserved customers cannot be deleted manually. Delete the associated B2B partner instead.');
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

    public function partner()
    {
        return $this->belongsTo(B2bPartner::class, 'partner_id');
    }

    public function accommodation()
    {
        return $this->belongsTo(PropertyAccommodation::class, 'accommodation_id');
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

    // Reserved customer methods
    public function isReservedCustomer()
    {
        return $this->is_reserved;
    }

    public function scopeRegularCustomers($query)
    {
        return $query->where('is_reserved', false);
    }

    public function scopeReservedCustomers($query)
    {
        return $query->where('is_reserved', true);
    }

    // Create reserved customer for B2B partner
    public static function createReservedCustomerForPartner(B2bPartner $partner)
    {
        return static::create([
            'name' => "Reserved – {$partner->partner_name}",
            'email' => "reserved-{$partner->id}@b2b-partner.local",
            'phone' => $partner->phone,
            'mobile_number' => $partner->phone,
            'partner_id' => $partner->id,
            'is_reserved' => true,
            'id_type' => 'aadhar',
            'id_number' => "RESERVED-{$partner->id}",
        ]);
    }

    // Create reserved customer for accommodation
    public static function createReservedCustomerForAccommodation(PropertyAccommodation $accommodation)
    {
        return static::create([
            'name' => "Reserved – {$accommodation->display_name}",
            'email' => "reserved-acc-{$accommodation->id}@accommodation.local",
            'phone' => "0000000000", // Default phone for accommodation reserved customers
            'mobile_number' => "0000000000",
            'accommodation_id' => $accommodation->id,
            'is_reserved' => true,
            'id_type' => 'aadhar',
            'id_number' => "RESERVED-ACC-{$accommodation->id}",
        ]);
    }
}