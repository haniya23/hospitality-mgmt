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
        
        static::created(function ($model) {
            // Create default roles for the property
            Role::createDefaultRoles($model->id);
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

    public function staffTasks()
    {
        return $this->hasMany(StaffTask::class);
    }

    public function cleaningChecklists()
    {
        return $this->hasMany(CleaningChecklist::class);
    }

    public function activeStaff()
    {
        return $this->staffAssignments()
                    ->where('status', 'active')
                    ->with('user');
    }

    public function getActiveStaffCount()
    {
        return $this->staffAssignments()
                    ->where('status', 'active')
                    ->count();
    }

    public function getTodaysTasksCount()
    {
        return $this->staffTasks()
                    ->whereDate('scheduled_at', today())
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count();
    }

    public function getOverdueTasksCount()
    {
        return $this->staffTasks()
                    ->where('scheduled_at', '<', now())
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count();
    }

    public function getTaskCompletionRate($days = 7)
    {
        $startDate = now()->subDays($days);
        
        $totalTasks = $this->staffTasks()
                           ->where('created_at', '>=', $startDate)
                           ->count();
        
        $completedTasks = $this->staffTasks()
                               ->where('created_at', '>=', $startDate)
                               ->where('status', 'completed')
                               ->count();
        
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
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