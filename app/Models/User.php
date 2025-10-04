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
        'user_type',
        'is_staff',
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
            'is_staff' => 'boolean',
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

    public function staffTasks()
    {
        return $this->hasManyThrough(StaffTask::class, StaffAssignment::class, 'user_id', 'staff_assignment_id');
    }

    public function staffNotifications()
    {
        return $this->hasManyThrough(StaffNotification::class, StaffAssignment::class, 'user_id', 'staff_assignment_id');
    }

    public function staffActivityLogs()
    {
        return $this->hasManyThrough(StaffActivityLog::class, StaffAssignment::class, 'user_id', 'staff_assignment_id');
    }

    public function checklistExecutions()
    {
        return $this->hasManyThrough(ChecklistExecution::class, StaffAssignment::class, 'user_id', 'staff_assignment_id');
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
                $model->trial_ends_at = now()->addDays(15);
                $model->is_trial_active = true;
                $model->properties_limit = 1;
            }
            // Set new users as owners by default
            if (empty($model->user_type)) {
                $model->user_type = 'owner';
            }
            if (is_null($model->is_staff)) {
                $model->is_staff = false;
            }
        });
    }

    /**
     * Check if user can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    // Staff-related methods
    public function isStaff()
    {
        return $this->is_staff || $this->user_type === 'staff';
    }

    public function isOwner()
    {
        return $this->user_type === 'owner' || (!$this->is_staff && !$this->is_admin);
    }

    public function getAssignedProperties()
    {
        return $this->staffAssignments()
                    ->with('property')
                    ->where('status', 'active')
                    ->get()
                    ->pluck('property');
    }

    public function getActiveStaffAssignments()
    {
        return $this->staffAssignments()
                    ->where('status', 'active')
                    ->with(['property', 'role'])
                    ->get();
    }

    public function getTodaysTasks()
    {
        return $this->staffTasks()
                    ->whereDate('scheduled_at', today())
                    ->whereIn('staff_tasks.status', ['pending', 'in_progress'])
                    ->orderBy('priority', 'desc')
                    ->orderBy('scheduled_at')
                    ->get();
    }

    public function getOverdueTasks()
    {
        return $this->staffTasks()
                    ->where('scheduled_at', '<', now())
                    ->whereIn('staff_tasks.status', ['pending', 'in_progress'])
                    ->orderBy('scheduled_at')
                    ->get();
    }

    public function getUnreadNotifications()
    {
        return $this->staffNotifications()
                    ->where('is_read', false)
                    ->orderBy('priority', 'desc')
                    ->orderBy('staff_notifications.created_at', 'desc')
                    ->get();
    }

    public function getUnreadNotificationsCount()
    {
        return $this->staffNotifications()
                    ->where('is_read', false)
                    ->count();
    }

    public function getUrgentNotificationsCount()
    {
        return $this->staffNotifications()
                    ->where('is_read', false)
                    ->where('priority', 'urgent')
                    ->count();
    }

    public function hasPermission($permissionKey, $propertyId = null)
    {
        $query = StaffPermission::whereHas('staffAssignment', function($q) {
            $q->where('user_id', $this->id)
              ->where('status', 'active');
        })->where('permission_key', $permissionKey);

        if ($propertyId) {
            $query->whereHas('staffAssignment', function($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            });
        }

        return $query->where('is_granted', true)->exists();
    }

    public function getPermissions($propertyId = null)
    {
        $query = StaffPermission::whereHas('staffAssignment', function($q) {
            $q->where('user_id', $this->id)
              ->where('status', 'active');
        })->where('is_granted', true);

        if ($propertyId) {
            $query->whereHas('staffAssignment', function($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            });
        }

        return $query->pluck('permission_key')->toArray();
    }

    public function getTodaysActivity()
    {
        return $this->staffActivityLogs()
                    ->whereDate('staff_activity_logs.created_at', today())
                    ->orderBy('staff_activity_logs.created_at', 'desc')
                    ->get();
    }

    public function getTaskCompletionRate($days = 7)
    {
        $startDate = now()->subDays($days);
        
        $totalTasks = $this->staffTasks()
                           ->where('staff_tasks.created_at', '>=', $startDate)
                           ->count();
        
        $completedTasks = $this->staffTasks()
                               ->where('staff_tasks.created_at', '>=', $startDate)
                               ->where('staff_tasks.status', 'completed')
                               ->count();
        
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
    }

    // Static method to create staff user
    public static function createStaff($data)
    {
        $data['user_type'] = 'staff';
        $data['is_staff'] = true;
        $data['is_active'] = true;
        
        return self::create($data);
    }

}