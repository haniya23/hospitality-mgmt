<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class, 'department_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'department_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getStaffCount()
    {
        return $this->staffMembers()->where('status', 'active')->count();
    }

    public function getPendingTasksCount()
    {
        return $this->tasks()->whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
    }
}
