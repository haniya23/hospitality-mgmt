<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'from_user_id',
        'task_id',
        'type',
        'title',
        'message',
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

    // Relationships
    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
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

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
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
}
