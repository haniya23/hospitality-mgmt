<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['district_id', 'name'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function pincodes()
    {
        return $this->hasMany(Pincode::class);
    }
}