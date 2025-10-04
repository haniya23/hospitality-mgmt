<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CleaningChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'checklist_items',
        'is_active',
        'is_template',
        'created_by',
    ];

    protected $casts = [
        'checklist_items' => 'array',
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executions()
    {
        return $this->hasMany(ChecklistExecution::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Helper methods
    public function createExecution($staffAssignmentId, $accommodationId = null, $reservationId = null)
    {
        return ChecklistExecution::create([
            'cleaning_checklist_id' => $this->id,
            'staff_assignment_id' => $staffAssignmentId,
            'property_accommodation_id' => $accommodationId,
            'reservation_id' => $reservationId,
            'started_at' => now(),
        ]);
    }

    public function getCompletionRate($staffAssignmentId = null)
    {
        $query = $this->executions();
        
        if ($staffAssignmentId) {
            $query->where('staff_assignment_id', $staffAssignmentId);
        }
        
        $total = $query->count();
        $completed = $query->where('status', 'completed')->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
}
