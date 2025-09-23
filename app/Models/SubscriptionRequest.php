<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requested_plan',
        'billing_cycle',
        'status',
        'admin_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}