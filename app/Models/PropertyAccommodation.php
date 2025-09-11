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
        'description',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
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

    public function getDisplayNameAttribute()
    {
        if ($this->predefinedType && $this->predefinedType->name === 'Custom') {
            return $this->custom_name ?: 'Custom Accommodation';
        }
        return $this->custom_name ?: $this->predefinedType->name;
    }
}