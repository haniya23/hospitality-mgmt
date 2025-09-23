<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile_number',
        'pin_hash',
        'email',
        'is_active',
        'is_admin',
        'subscription_status',
        'trial_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'properties_limit',
        'is_trial_active',
    ];

    protected $hidden = [
        'pin_hash',
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
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            // Set 30-day trial for new users
            if (empty($model->trial_ends_at)) {
                $model->trial_ends_at = now()->addDays(30);
                $model->subscription_status = 'trial';
                $model->trial_plan = 'starter';
                $model->is_trial_active = true;
                $model->properties_limit = 1;
            }
        });
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
        // Trial users have limited features
        if ($this->subscription_status === 'trial') {
            return in_array($feature, ['basic_bookings', 'basic_customers', 'basic_properties']);
        }
        
        // Starter plan features
        if ($this->properties_limit <= 3) {
            return in_array($feature, ['basic_bookings', 'basic_customers', 'basic_properties', 'basic_pricing']);
        }
        
        // Professional plan - all features
        return true;
    }

    public function getPlanNameAttribute()
    {
        if ($this->subscription_status === 'trial') {
            return 'Trial - ' . ucfirst($this->trial_plan);
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
}