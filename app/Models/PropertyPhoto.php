<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPhoto extends Model
{
    protected $fillable = [
        'property_id',
        'accommodation_id',
        'file_path',
        'caption',
        'sort_order',
        'is_main',
        'file_size',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(PropertyAccommodation::class, 'accommodation_id');
    }
}