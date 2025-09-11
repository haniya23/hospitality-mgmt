<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['country_id', 'name', 'code'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}