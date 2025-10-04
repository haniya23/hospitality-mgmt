<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class B2bPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_name',
        'partner_type',
        'contact_user_id',
        'email',
        'phone',
        'commission_rate',
        'default_discount_pct',
        'partnership_settings',
        'requested_by',
        'partnership_accepted_at',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'default_discount_pct' => 'decimal:2',
        'partnership_settings' => 'array',
        'partnership_accepted_at' => 'datetime',
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

    public function contactUser()
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'partner_id');
    }

    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }

    public function reservedCustomer()
    {
        return $this->hasOne(Guest::class, 'partner_id')->where('is_reserved', true);
    }

    public function getReservedCustomerAttribute()
    {
        return $this->reservedCustomer()->first();
    }

    public function sentRequests()
    {
        return $this->hasMany(B2bRequest::class, 'from_partner_id', 'contact_user_id');
    }

    // Partnership management methods
    public function acceptPartnership()
    {
        $this->update([
            'status' => 'active',
            'partnership_accepted_at' => now()
        ]);
    }

    public function rejectPartnership($reason = null)
    {
        $this->update(['status' => 'rejected']);
        
        if ($reason) {
            AuditLog::logAction('partnership_rejected', $this, null, ['reason' => $reason]);
        }
    }

    // Commission calculations
    public function getTotalCommissionEarned()
    {
        return $this->commissions()->where('status', 'paid')->sum('amount_paid');
    }

    public function getPendingCommission()
    {
        return $this->commissions()->where('status', 'pending')->sum('amount');
    }

    // Find partner by user mobile
    public static function findByUserMobile($mobile)
    {
        return static::whereHas('contactUser', function ($query) use ($mobile) {
            $query->where('mobile_number', $mobile);
        })->first();
    }

    // Create partnership request
    public static function createPartnershipRequest($requesterUserId, $partnerMobile, $partnerName = null)
    {
        // Find or create user for partner
        $partnerUser = User::where('mobile_number', $partnerMobile)->first();
        
        if (!$partnerUser) {
            $partnerUser = User::create([
                'name' => $partnerName ?? 'B2B Partner',
                'mobile_number' => $partnerMobile,
                'pin_hash' => bcrypt('0000'), // Default PIN
                'is_active' => false, // Inactive until they accept
                'user_type' => 'owner',
                'is_staff' => false,
            ]);
        }

        return static::create([
            'partner_name' => $partnerName ?? $partnerUser->name,
            'partner_type' => 'B2B Partner',
            'contact_user_id' => $partnerUser->id,
            'phone' => $partnerMobile,
            'commission_rate' => 10, // Default 10%
            'default_discount_pct' => 5, // Default 5% discount
            'requested_by' => $requesterUserId,
            'status' => 'pending',
        ]);
    }

    // Get or create reserved customer for this partner
    public function getOrCreateReservedCustomer()
    {
        if (!$this->reservedCustomer) {
            return Guest::createReservedCustomerForPartner($this);
        }
        
        return $this->reservedCustomer;
    }

    // Update reserved customer name when partner name changes
    public function updateReservedCustomerName()
    {
        if ($this->reservedCustomer) {
            $this->reservedCustomer->update([
                'name' => "Reserved – {$this->partner_name}"
            ]);
        }
    }
}