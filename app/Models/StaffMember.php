<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StaffMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'department_id',
        'staff_role',
        'job_title',
        'reports_to',
        'employment_type',
        'status',
        'join_date',
        'end_date',
        'phone',
        'emergency_contact',
        'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
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

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function department()
    {
        return $this->belongsTo(StaffDepartment::class, 'department_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(StaffMember::class, 'reports_to');
    }

    public function subordinates()
    {
        return $this->hasMany(StaffMember::class, 'reports_to');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function delegatedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function notifications()
    {
        return $this->hasMany(StaffNotification::class);
    }

    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(StaffLeaveRequest::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(StaffPerformanceReview::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeManagers($query)
    {
        return $query->where('staff_role', 'manager');
    }

    public function scopeSupervisors($query)
    {
        return $query->where('staff_role', 'supervisor');
    }

    public function scopeStaff($query)
    {
        return $query->where('staff_role', 'staff');
    }

    // Helper methods
    public function isManager()
    {
        return $this->staff_role === 'manager';
    }

    public function isSupervisor()
    {
        return $this->staff_role === 'supervisor';
    }

    public function isStaff()
    {
        return $this->staff_role === 'staff';
    }

    public function canAssignTasks()
    {
        return in_array($this->staff_role, ['manager', 'supervisor']);
    }

    public function canVerifyTasks()
    {
        return in_array($this->staff_role, ['manager', 'supervisor']);
    }

    public function getTodaysTasks()
    {
        return $this->assignedTasks()
            ->whereDate('scheduled_at', today())
            ->whereIn('status', ['assigned', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->get();
    }

    public function getOverdueTasks()
    {
        return $this->assignedTasks()
            ->where('due_at', '<', now())
            ->whereNotIn('status', ['completed', 'verified', 'cancelled'])
            ->orderBy('due_at')
            ->get();
    }

    public function getTaskCompletionRate($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $total = $this->assignedTasks()
            ->where('created_at', '>=', $startDate)
            ->count();
        
        $completed = $this->assignedTasks()
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['completed', 'verified'])
            ->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    public function getFullName()
    {
        return $this->user->name;
    }

    public function getRoleBadgeColor()
    {
        return match($this->staff_role) {
            'manager' => 'bg-purple-100 text-purple-800',
            'supervisor' => 'bg-blue-100 text-blue-800',
            'staff' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'on_leave' => 'bg-yellow-100 text-yellow-800',
            'suspended' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
