<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_assignment_id',
        'action',
        'model_type',
        'model_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
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

    public function staff()
    {
        return $this->hasOneThrough(User::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'user_id');
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForStaff($query, $userId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($propertyId) {
            $q->where('property_id', $propertyId);
        });
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // Helper methods
    public function getActionDescription()
    {
        $descriptions = [
            'task_started' => 'Started task',
            'task_completed' => 'Completed task',
            'checklist_started' => 'Started checklist',
            'checklist_completed' => 'Completed checklist',
            'photo_uploaded' => 'Uploaded photo',
            'note_added' => 'Added note',
            'issue_reported' => 'Reported issue',
            'status_updated' => 'Updated status',
        ];

        return $descriptions[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getModelName()
    {
        if ($this->model_type && $this->model_id) {
            $modelClass = $this->model_type;
            if (class_exists($modelClass)) {
                $model = $modelClass::find($this->model_id);
                if ($model) {
                    return $model->task_name ?? $model->name ?? "Item #{$this->model_id}";
                }
            }
        }
        return null;
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    // Static methods for logging
    public static function logTaskStart($staffAssignmentId, $taskId, $additionalData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'task_started',
            'model_type' => StaffTask::class,
            'model_id' => $taskId,
            'data' => $additionalData,
        ]);
    }

    public static function logTaskCompletion($staffAssignmentId, $taskId, $completionData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'task_completed',
            'model_type' => StaffTask::class,
            'model_id' => $taskId,
            'data' => $completionData,
        ]);
    }

    public static function logChecklistStart($staffAssignmentId, $checklistId, $additionalData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'checklist_started',
            'model_type' => ChecklistExecution::class,
            'model_id' => $checklistId,
            'data' => $additionalData,
        ]);
    }

    public static function logChecklistCompletion($staffAssignmentId, $checklistId, $completionData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'checklist_completed',
            'model_type' => ChecklistExecution::class,
            'model_id' => $checklistId,
            'data' => $completionData,
        ]);
    }

    public static function logPhotoUpload($staffAssignmentId, $modelType, $modelId, $photoData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'photo_uploaded',
            'model_type' => $modelType,
            'model_id' => $modelId,
            'data' => $photoData,
        ]);
    }

    public static function logIssueReport($staffAssignmentId, $issueData = [])
    {
        return self::create([
            'staff_assignment_id' => $staffAssignmentId,
            'action' => 'issue_reported',
            'data' => $issueData,
        ]);
    }
}
