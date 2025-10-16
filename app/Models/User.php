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

    // New Staff Hierarchy Relationships
    public function staffMember()
    {
        return $this->hasOne(StaffMember::class);
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get all staff members across all properties owned by this user
     */
    public function allStaff()
    {
        if (!$this->isOwner()) {
            return collect();
        }

        return StaffMember::whereIn('property_id', $this->properties()->pluck('id'))
            ->with('user', 'property', 'department');
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
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function canCreateProperty()
    {
        $maxProperties = $this->getMaxProperties();
        return $this->properties()->count() < $maxProperties;
    }

    public function canCreateAccommodation($propertyId = null)
    {
        $maxAccommodations = $this->getMaxAccommodations();
        $currentAccommodations = $this->properties()->withCount('accommodations')->get()->sum('accommodations_count');
        
        if ($propertyId) {
            $propertyAccommodations = $this->properties()->where('id', $propertyId)->withCount('accommodations')->first();
            if ($propertyAccommodations && $propertyAccommodations->accommodations_count >= 3) {
                return false; // Max 3 accommodations per property
            }
        }
        
        return $currentAccommodations < $maxAccommodations;
    }

    public function getMaxProperties()
    {
        switch ($this->subscription_status) {
            case 'starter':
                return 1;
            case 'professional':
                return 5;
            case 'trial':
                return 5; // Trial users get professional limits
            default:
                return 1;
        }
    }

    public function getMaxAccommodations()
    {
        $baseLimit = 3; // Default for starter
        
        switch ($this->subscription_status) {
            case 'starter':
                $baseLimit = 3;
                break;
            case 'professional':
                $baseLimit = 15;
                break;
            case 'trial':
                $baseLimit = 15; // Trial users get professional limits
                break;
            default:
                $baseLimit = 3;
        }
        
        // Add active accommodation addons
        $activeSubscription = $this->activeSubscription;
        if ($activeSubscription) {
            $activeAddons = $activeSubscription->addons()->where('cycle_end', '>', now())->sum('qty');
            $baseLimit += $activeAddons;
        }
        
        return $baseLimit;
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
        $maxProperties = $this->getMaxProperties();
        return max(0, $maxProperties - $this->properties()->count());
    }

    public function getPropertyLimit()
    {
        return $this->getMaxProperties();
    }

    public function getAccommodationLimit()
    {
        return $this->getMaxAccommodations();
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
            return 0; // No image uploads during trial
        }
        
        return $this->subscription_status === 'professional' ? 999 : 1;
    }

    public function canUploadImages()
    {
        return $this->subscription_status !== 'trial';
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
                $model->subscription_status = 'trial';
                $model->trial_plan = 'professional';
                $model->trial_ends_at = now()->addDays(30);
                $model->is_trial_active = true;
                $model->properties_limit = 1;
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

    // User role helpers
    public function isOwner()
    {
        return !$this->is_admin && !$this->staffMember;
    }
}