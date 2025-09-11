<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredefinedAccommodationType extends Model
{
    protected $fillable = [
        'property_category_id',
        'name',
        'description',
    ];

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function propertyAccommodations()
    {
        return $this->hasMany(PropertyAccommodation::class);
    }
}