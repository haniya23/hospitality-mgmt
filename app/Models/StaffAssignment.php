<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'role_id',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function staffTasks()
    {
        return $this->hasMany(StaffTask::class);
    }

    public function staffNotifications()
    {
        return $this->hasMany(StaffNotification::class);
    }

    public function staffPermissions()
    {
        return $this->hasMany(StaffPermission::class);
    }

    public function staffActivityLogs()
    {
        return $this->hasMany(StaffActivityLog::class);
    }

    public function checklistExecutions()
    {
        return $this->hasMany(ChecklistExecution::class);
    }

    // Helper methods
    public function hasPermission($permissionKey)
    {
        return $this->staffPermissions()
                    ->where('permission_key', $permissionKey)
                    ->where('is_granted', true)
                    ->exists();
    }

    public function grantPermission($permissionKey, $restrictions = [])
    {
        return StaffPermission::grantPermission($this->id, $permissionKey, $restrictions);
    }

    public function denyPermission($permissionKey)
    {
        return StaffPermission::denyPermission($this->id, $permissionKey);
    }

    public function getTodaysTasks()
    {
        return $this->staffTasks()
                    ->whereDate('scheduled_at', today())
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->orderBy('priority', 'desc')
                    ->get();
    }

    public function getOverdueTasks()
    {
        return $this->staffTasks()
                    ->where('scheduled_at', '<', now())
                    ->whereIn('status', ['pending', 'in_progress'])
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
}