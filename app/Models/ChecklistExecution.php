<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChecklistExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'cleaning_checklist_id',
        'staff_assignment_id',
        'property_accommodation_id',
        'reservation_id',
        'status',
        'completed_items',
        'notes',
        'photos',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'completed_items' => 'array',
        'photos' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function cleaningChecklist()
    {
        return $this->belongsTo(CleaningChecklist::class);
    }

    public function staffAssignment()
    {
        return $this->belongsTo(StaffAssignment::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(PropertyAccommodation::class, 'property_accommodation_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function staff()
    {
        return $this->hasOneThrough(User::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'user_id');
    }

    // Scopes
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

    // Helper methods
    public function completeItem($itemIndex)
    {
        $completedItems = $this->completed_items ?? [];
        if (!in_array($itemIndex, $completedItems)) {
            $completedItems[] = $itemIndex;
            $this->update(['completed_items' => $completedItems]);
        }
    }

    public function uncompleteItem($itemIndex)
    {
        $completedItems = $this->completed_items ?? [];
        $completedItems = array_filter($completedItems, function($index) use ($itemIndex) {
            return $index !== $itemIndex;
        });
        $this->update(['completed_items' => array_values($completedItems)]);
    }

    public function getCompletionPercentage()
    {
        $totalItems = count($this->cleaningChecklist->checklist_items);
        $completedItems = count($this->completed_items ?? []);
        
        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
    }

    public function isCompleted()
    {
        $totalItems = count($this->cleaningChecklist->checklist_items);
        $completedItems = count($this->completed_items ?? []);
        
        return $totalItems > 0 && $completedItems >= $totalItems;
    }

    public function completeExecution($notes = null, $photos = [])
    {
        if ($this->status === 'in_progress') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $notes,
                'photos' => $photos,
            ]);
            
            $this->logActivity('checklist_completed', [
                'notes' => $notes,
                'photos_count' => count($photos),
                'completion_percentage' => $this->getCompletionPercentage(),
            ]);
        }
    }

    // Activity logging removed - using simple access control system
}
