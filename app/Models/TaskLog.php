<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaskLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'staff_member_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'metadata',
        'performed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'performed_at' => 'datetime',
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
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    // Helper methods
    public function getPerformerName()
    {
        if ($this->staffMember) {
            return $this->staffMember->getFullName();
        }
        if ($this->user) {
            return $this->user->name;
        }
        return 'System';
    }

    public function getActionLabel()
    {
        return match($this->action) {
            'created' => 'Created',
            'assigned' => 'Assigned',
            'started' => 'Started',
            'paused' => 'Paused',
            'resumed' => 'Resumed',
            'completed' => 'Completed',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'reassigned' => 'Reassigned',
            'cancelled' => 'Cancelled',
            'commented' => 'Commented',
            'updated' => 'Updated',
            default => ucfirst($this->action)
        };
    }

    public function getActionColor()
    {
        return match($this->action) {
            'created' => 'text-blue-600',
            'assigned' => 'text-indigo-600',
            'started' => 'text-yellow-600',
            'completed' => 'text-green-600',
            'verified' => 'text-purple-600',
            'rejected' => 'text-red-600',
            'cancelled' => 'text-gray-600',
            default => 'text-gray-600'
        };
    }
}
