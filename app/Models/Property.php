<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'property_category_id',
        'name',
        'description',
        'status',
        'wizard_step_completed',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(PropertyCategory::class, 'property_category_id');
    }

    public function location()
    {
        return $this->hasOne(PropertyLocation::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function staffAssignments()
    {
        return $this->hasMany(StaffAssignment::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function policy()
    {
        return $this->hasOne(PropertyPolicy::class);
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    public function propertyAccommodations()
    {
        return $this->hasMany(PropertyAccommodation::class);
    }

    public function accommodations()
    {
        return $this->hasMany(PropertyAccommodation::class);
    }

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, PropertyAccommodation::class);
    }

    public function deleteRequests()
    {
        return $this->hasMany(PropertyDeleteRequest::class);
    }

    public function pendingDeleteRequest()
    {
        return $this->hasOne(PropertyDeleteRequest::class)->where('status', 'pending');
    }

    public function hasPendingDeleteRequest(): bool
    {
        return $this->pendingDeleteRequest()->exists();
    }

    public function hasBookings(): bool
    {
        return $this->reservations()->exists();
    }

    public function canBeDeleted(): bool
    {
        return !$this->hasBookings();
    }
}