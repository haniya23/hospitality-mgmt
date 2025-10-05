<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_assignment_id',
        'property_id',
        'task_name',
        'description',
        'task_type',
        'priority',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'completion_notes',
        'completion_photos',
        'assigned_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'completion_photos' => 'array',
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function staff()
    {
        return $this->hasOneThrough(User::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForStaff($query, $userId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeScheduledToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
                    ->whereIn('status', ['pending', 'in_progress']);
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->scheduled_at && 
               $this->scheduled_at->isPast() && 
               in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeStartedBy($userId)
    {
        return $this->staffAssignment->user_id === $userId && 
               $this->status === 'pending';
    }

    public function canBeCompletedBy($userId)
    {
        return $this->staffAssignment->user_id === $userId && 
               in_array($this->status, ['pending', 'in_progress']);
    }

    public function startTask()
    {
        if ($this->status === 'pending') {
            $this->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            
            $this->logActivity('task_started');
        }
    }

    public function completeTask($notes = null, $photos = [])
    {
        if (in_array($this->status, ['pending', 'in_progress'])) {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_notes' => $notes,
                'completion_photos' => $photos,
            ]);
            
            $this->logActivity('task_completed', [
                'notes' => $notes,
                'photos_count' => count($photos),
            ]);
        }
    }

    // Activity logging removed - using simple access control system
}
