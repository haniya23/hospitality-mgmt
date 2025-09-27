<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile_number',
        'pin_hash',
        'password',
        'email',
        'is_active',
        'is_admin',
        'subscription_status',
        'trial_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'properties_limit',
        'is_trial_active',
        'referral_code',
        'referred_by',
        'user_id',
    ];

    protected $hidden = [
        'pin_hash',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'is_trial_active' => 'boolean',
            'password' => 'hashed',
        ];
    }



    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function staffAssignments()
    {
        return $this->hasMany(StaffAssignment::class);
    }

    public function b2bPartnerContacts()
    {
        return $this->hasMany(B2bPartner::class, 'contact_user_id');
    }
    
    public function b2bPartners()
    {
        return $this->hasMany(B2bPartner::class, 'requested_by');
    }
    
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function approvedProperties()
    {
        return $this->hasMany(Property::class, 'approved_by');
    }

    public function getRemainingTrialDaysAttribute()
    {
        if (!$this->is_trial_active || !$this->trial_ends_at) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }

    public function isTrialExpired()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function canCreateProperty()
    {
        return $this->properties()->count() < $this->properties_limit;
    }

    public function getRemainingPropertiesAttribute()
    {
        return max(0, $this->properties_limit - $this->properties()->count());
    }

    public function hasFeature($feature)
    {
        // Professional trial: allow all features
        if ($this->subscription_status === 'trial' && $this->trial_plan === 'professional') {
            return true;
        }
        // Any paid plan: allow all features; property count is limited elsewhere
        if (in_array($this->subscription_status, ['starter', 'professional'])) {
            return true;
        }
        // Fallback (non-auth or unknown): restrict
        return false;
    }

    public function getPlanNameAttribute()
    {
        if ($this->subscription_status === 'trial') {
            // Always show Professional during trial in UI
            return 'Trial - Professional';
        }
        
        if (in_array($this->subscription_status, ['starter', 'professional'])) {
            return ucfirst($this->subscription_status);
        }
        
        return 'Trial - Starter';
    }

    public function getPlanName()
    {
        return $this->plan_name;
    }

    public function isOnTrial()
    {
        return $this->subscription_status === 'trial' && $this->is_trial_active && !$this->isTrialExpired();
    }

    public function getImageUploadLimitAttribute()
    {
        if ($this->subscription_status === 'trial') {
            return $this->trial_plan === 'professional' ? 5 : 1;
        }
        
        return $this->subscription_status === 'professional' ? 5 : 1;
    }

    public function subscriptionRequests()
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    public function hasPendingRequest()
    {
        return $this->subscriptionRequests()->where('status', 'pending')->exists();
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referralWithdrawals()
    {
        return $this->hasMany(ReferralWithdrawal::class);
    }

    public function getReferralEarningsAttribute()
    {
        return $this->referrals()->where('status', 'completed')->sum('reward_amount');
    }

    public function getCompletedReferralsCountAttribute()
    {
        return $this->referrals()->where('status', 'completed')->count();
    }

    public function canWithdrawReferralEarnings()
    {
        return $this->completed_referrals_count >= 4;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            // Generate referral code
            if (empty($model->referral_code)) {
                $model->referral_code = strtoupper(Str::random(8));
            }
            // Generate user_id
            if (empty($model->user_id)) {
                $model->user_id = 'USR' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT) . strtoupper(Str::random(3));
            }
            // Set mobile number for admin users if not provided
            if (empty($model->mobile_number)) {
                $model->mobile_number = '0000000000';
            }
            // Set pin_hash for admin users if not provided
            if (empty($model->pin_hash)) {
                $model->pin_hash = Hash::make('0000');
            }
            // Set 30-day trial for new users
            if (empty($model->trial_ends_at)) {
                $model->trial_ends_at = now()->addDays(30);
                $model->subscription_status = 'trial';
                $model->trial_plan = 'professional';
                $model->is_trial_active = true;
                $model->properties_limit = 1;
            }
        });
    }
}