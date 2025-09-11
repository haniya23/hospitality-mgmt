<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'accommodation_id',
        'b2b_partner_id',
        'confirmation_number',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'total_amount',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->confirmation_number)) {
                $model->confirmation_number = 'RES' . strtoupper(Str::random(8));
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function b2bPartner()
    {
        return $this->belongsTo(B2bPartner::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'reservation_rooms');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function guestStay()
    {
        return $this->hasOne(GuestStay::class);
    }

    public function feedback()
    {
        return $this->hasOne(GuestFeedback::class);
    }
}