<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function predefinedAccommodationTypes()
    {
        return $this->hasMany(PredefinedAccommodationType::class);
    }
}