<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyLocation extends Model
{
    use HasFactory;

    protected $primaryKey = 'property_id';
    public $incrementing = false;

    protected $fillable = [
        'property_id',
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'pincode_id',
        'address',
        'latitude',
        'longitude',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function pincode()
    {
        return $this->belongsTo(Pincode::class);
    }
}