<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

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
        'referred_by',
        'user_id',
        'billing_cycle',
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

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('current_period_end', '>', now());
    }

    public function subscriptionRequests()
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    public function hasPendingRequest()
    {
        return $this->subscriptionRequests()->where('status', 'pending')->exists();
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

    public function getRemainingSubscriptionDaysAttribute()
    {
        if (!$this->subscription_ends_at) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->subscription_ends_at, false));
    }

    public function isTrialExpired()
    {
        return false;
    }

    public function canCreateProperty()
    {
        return true;
    }

    public function canCreateAccommodation($propertyId = null)
    {
        return true;
    }

    public function getMaxProperties()
    {
        return 999999;
    }

    public function getMaxAccommodations()
    {
        return 999999;
    }

    public function getUsagePercentage()
    {
        $propertiesUsed = $this->properties()->count();
        $accommodationsUsed = $this->properties()->withCount('accommodations')->get()->sum('accommodations_count');
        
        $maxProperties = $this->getMaxProperties();
        $maxAccommodations = $this->getMaxAccommodations();
        
        $propertiesPercentage = $maxProperties > 0 ? ($propertiesUsed / $maxProperties) * 100 : 0;
        $accommodationsPercentage = $maxAccommodations > 0 ? ($accommodationsUsed / $maxAccommodations) * 100 : 0;
        
        return [
            'properties' => [
                'used' => $propertiesUsed,
                'max' => $maxProperties,
                'percentage' => min(100, $propertiesPercentage)
            ],
            'accommodations' => [
                'used' => $accommodationsUsed,
                'max' => $maxAccommodations,
                'percentage' => min(100, $accommodationsPercentage)
            ]
        ];
    }

    public function getRemainingPropertiesAttribute()
    {
        return 999999;
    }

    public function getPropertyLimit()
    {
        return 999999;
    }

    public function getAccommodationLimit()
    {
        return 999999;
    }

    public function hasFeature($feature)
    {
        return true;
    }

    public function getPlanNameAttribute()
    {
        return 'Free';
    }

    public function getPlanName()
    {
        return 'Free';
    }

    public function isOnTrial()
    {
        return false;
    }

    public function getImageUploadLimitAttribute()
    {
        return 999999;
    }

    public function canUploadImages()
    {
        return true;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
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
            // Set default values for new users (only if not already set)
            if (empty($model->subscription_status)) {
                $model->subscription_status = 'free';
                $model->trial_plan = null;
                $model->trial_ends_at = null;
                $model->is_trial_active = false;
                $model->properties_limit = 999999;
            }
            // Set new users as owners by default
        });
    }

    /**
     * Check if user can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function isOwner()
    {
        return !$this->is_admin;
    }
}