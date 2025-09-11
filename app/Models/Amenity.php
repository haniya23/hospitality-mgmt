<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon'];

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_amenities');
    }
}