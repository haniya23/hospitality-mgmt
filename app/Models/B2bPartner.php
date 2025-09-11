<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class B2bPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_name',
        'partner_type',
        'contact_user_id',
        'email',
        'phone',
        'commission_rate',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function contactUser()
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }
}