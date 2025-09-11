<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPolicy extends Model
{
    protected $table = 'property_policies';
    
    protected $fillable = [
        'property_id',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'house_rules',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}