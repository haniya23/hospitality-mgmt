<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_assignment_id',
        'from_user_id',
        'title',
        'message',
        'type',
        'priority',
        'is_read',
        'read_at',
        'action_data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'action_data' => 'array',
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

    public function staffAssignment()
    {
        return $this->belongsTo(StaffAssignment::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function staff()
    {
        return $this->hasOneThrough(User::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForStaff($query, $userId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    // Helper methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    // Static methods for creating notifications
    public static function createTaskAssignment($staffAssignmentId, $fromUserId, $taskName, $taskId = null)
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'from_user_id' => $fromUserId,
            'title' => 'New Task Assigned',
            'message' => "You have been assigned a new task: {$taskName}",
            'type' => 'task_assignment',
            'priority' => 'medium',
            'action_data' => [
                'task_id' => $taskId,
                'action_url' => $taskId ? "/staff/tasks/{$taskId}" : null,
            ],
        ]);
    }

    public static function createUrgentUpdate($staffAssignmentId, $fromUserId, $title, $message, $actionData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'from_user_id' => $fromUserId,
            'title' => $title,
            'message' => $message,
            'type' => 'urgent_update',
            'priority' => 'urgent',
            'action_data' => $actionData,
        ]);
    }

    public static function createReminder($staffAssignmentId, $fromUserId, $title, $message, $actionData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'from_user_id' => $fromUserId,
            'title' => $title,
            'message' => $message,
            'type' => 'reminder',
            'priority' => 'medium',
            'action_data' => $actionData,
        ]);
    }
}
