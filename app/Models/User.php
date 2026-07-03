<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'mobile_number',
        'pin_hash',
        'password',
        'email',
        'profile_photo_path',
        'is_active',
        'is_admin',
        'referred_by',
        'user_id',
    ];

    protected $appends = [
        'profile_photo_url',
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

    public function approvedProperties()
    {
        return $this->hasMany(Property::class, 'approved_by');
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

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2E3E2A&color=fff';
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
        });
    }

    public function isOwner()
    {
        return !$this->is_admin;
    }
}