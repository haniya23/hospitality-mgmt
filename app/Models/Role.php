<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'description',
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function staffAssignments()
    {
        return $this->hasMany(StaffAssignment::class);
    }

    // Default roles for properties
    public static function createDefaultRoles($propertyId)
    {
        $defaultRoles = [
            [
                'name' => 'Manager',
                'description' => 'Full access to property management and staff oversight',
            ],
            [
                'name' => 'Supervisor',
                'description' => 'Oversees daily operations and staff coordination',
            ],
        ];

        foreach ($defaultRoles as $roleData) {
            static::create([
                'property_id' => $propertyId,
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'is_active' => true,
            ]);
        }
    }

}
