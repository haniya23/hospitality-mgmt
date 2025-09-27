<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAccommodation extends Model
{
    protected $fillable = [
        'property_id',
        'predefined_accommodation_type_id',
        'custom_name',
        'max_occupancy',
        'base_price',
        'size',
        'description',
        'features',
        'is_active',
        'uuid',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'size' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function predefinedType()
    {
        return $this->belongsTo(PredefinedAccommodationType::class, 'predefined_accommodation_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'property_accommodation_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'accommodation_amenities', 'accommodation_id', 'amenity_id');
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class, 'accommodation_id');
    }

    public function getDisplayNameAttribute()
    {
        if ($this->predefinedType && $this->predefinedType->name === 'Custom') {
            return $this->custom_name ?: 'Custom Accommodation';
        }
        return $this->custom_name ?: $this->predefinedType->name;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid();
            }
        });
    }
}