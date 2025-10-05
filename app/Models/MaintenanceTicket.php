<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MaintenanceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_accommodation_id',
        'reported_by',
        'assigned_to',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'estimated_cost',
        'actual_cost',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'resolved_at' => 'datetime',
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

    public function propertyAccommodation()
    {
        return $this->belongsTo(PropertyAccommodation::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function property()
    {
        return $this->hasOneThrough(Property::class, PropertyAccommodation::class, 'id', 'id', 'property_accommodation_id', 'property_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isHighPriority()
    {
        return in_array($this->priority, ['high', 'urgent']);
    }

    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'red',
            'in_progress' => 'yellow',
            'resolved' => 'blue',
            'closed' => 'green',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getDaysOpenAttribute()
    {
        if ($this->resolved_at) {
            return $this->created_at->diffInDays($this->resolved_at);
        }
        
        return $this->created_at->diffInDays(now());
    }

    public function markAsResolved($notes = null, $actualCost = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
            'actual_cost' => $actualCost ?? $this->actual_cost,
        ]);
    }

    public function markAsClosed()
    {
        $this->update(['status' => 'closed']);
    }

    public function assignTo($userId)
    {
        $this->update(['assigned_to' => $userId]);
    }
}
