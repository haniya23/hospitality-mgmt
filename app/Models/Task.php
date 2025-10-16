<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory, SoftDeletes, HasCreatedUpdatedBy;

    protected $fillable = [
        'property_id',
        'department_id',
        'title',
        'description',
        'task_type',
        'priority',
        'status',
        'created_by',
        'assigned_to',
        'assigned_by',
        'scheduled_at',
        'due_at',
        'started_at',
        'completed_at',
        'verified_at',
        'completion_notes',
        'verified_by',
        'verification_notes',
        'rejection_reason',
        'location',
        'checklist_items',
        'requires_photo_proof',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'due_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
        'checklist_items' => 'array',
        'requires_photo_proof' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });

        static::created(function ($task) {
            // Log task creation
            $task->logs()->create([
                'uuid' => Str::uuid(),
                'user_id' => $task->created_by,
                'action' => 'created',
                'to_status' => $task->status,
                'performed_at' => now(),
            ]);
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function department()
    {
        return $this->belongsTo(StaffDepartment::class, 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(StaffMember::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(StaffMember::class, 'assigned_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(StaffMember::class, 'verified_by');
    }

    public function media()
    {
        return $this->hasMany(TaskMedia::class);
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
            ->whereNotIn('status', ['completed', 'verified', 'cancelled']);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForStaff($query, $staffMemberId)
    {
        return $query->where('assigned_to', $staffMemberId);
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->due_at && 
               $this->due_at->isPast() && 
               !in_array($this->status, ['completed', 'verified', 'cancelled']);
    }

    public function canBeStarted()
    {
        return in_array($this->status, ['pending', 'assigned']);
    }

    public function canBeCompleted()
    {
        return in_array($this->status, ['assigned', 'in_progress']);
    }

    public function canBeVerified()
    {
        return $this->status === 'completed';
    }

    public function start($staffMemberId = null)
    {
        if (!$this->canBeStarted()) {
            throw new \Exception('Task cannot be started from current status.');
        }

        $oldStatus = $this->status;
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staffMemberId,
            'action' => 'started',
            'from_status' => $oldStatus,
            'to_status' => 'in_progress',
            'performed_at' => now(),
        ]);

        return $this;
    }

    public function complete($staffMemberId, $notes = null, $mediaIds = [])
    {
        if (!$this->canBeCompleted()) {
            throw new \Exception('Task cannot be completed from current status.');
        }

        $oldStatus = $this->status;
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $notes,
        ]);

        $this->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staffMemberId,
            'action' => 'completed',
            'from_status' => $oldStatus,
            'to_status' => 'completed',
            'notes' => $notes,
            'metadata' => ['media_count' => count($mediaIds)],
            'performed_at' => now(),
        ]);

        // Notify supervisor
        if ($this->assignedStaff && $this->assignedStaff->supervisor) {
            StaffNotification::create([
                'uuid' => Str::uuid(),
                'staff_member_id' => $this->assignedStaff->supervisor->id,
                'from_user_id' => $this->assignedStaff->user_id,
                'task_id' => $this->id,
                'type' => 'task_completed',
                'title' => 'Task Completed - Verification Needed',
                'message' => "{$this->assignedStaff->getFullName()} has completed the task: {$this->title}",
                'priority' => 'medium',
            ]);
        }

        return $this;
    }

    public function verify($verifiedByStaffMemberId, $notes = null)
    {
        if (!$this->canBeVerified()) {
            throw new \Exception('Task cannot be verified from current status.');
        }

        $oldStatus = $this->status;
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedByStaffMemberId,
            'verification_notes' => $notes,
        ]);

        $this->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $verifiedByStaffMemberId,
            'action' => 'verified',
            'from_status' => $oldStatus,
            'to_status' => 'verified',
            'notes' => $notes,
            'performed_at' => now(),
        ]);

        // Notify assigned staff
        if ($this->assigned_to) {
            StaffNotification::create([
                'uuid' => Str::uuid(),
                'staff_member_id' => $this->assigned_to,
                'from_user_id' => StaffMember::find($verifiedByStaffMemberId)->user_id,
                'task_id' => $this->id,
                'type' => 'task_verified',
                'title' => 'Task Verified',
                'message' => "Your task '{$this->title}' has been verified and approved.",
                'priority' => 'low',
            ]);
        }

        return $this;
    }

    public function reject($rejectedByStaffMemberId, $reason)
    {
        $oldStatus = $this->status;
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $this->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $rejectedByStaffMemberId,
            'action' => 'rejected',
            'from_status' => $oldStatus,
            'to_status' => 'rejected',
            'notes' => $reason,
            'performed_at' => now(),
        ]);

        // Notify assigned staff
        if ($this->assigned_to) {
            StaffNotification::create([
                'uuid' => Str::uuid(),
                'staff_member_id' => $this->assigned_to,
                'from_user_id' => StaffMember::find($rejectedByStaffMemberId)->user_id,
                'task_id' => $this->id,
                'type' => 'task_rejected',
                'title' => 'Task Rejected - Rework Required',
                'message' => "Your task '{$this->title}' requires rework. Reason: {$reason}",
                'priority' => 'high',
            ]);
        }

        return $this;
    }

    public function getPriorityBadgeColor()
    {
        return match($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'assigned' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'verified' => 'bg-purple-100 text-purple-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
