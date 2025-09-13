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
        'property_accommodation_id',
        'b2b_partner_id',
        'confirmation_number',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'total_amount',
        'advance_paid',
        'balance_pending',
        'rate_override',
        'override_reason',
        'status',
        'special_requests',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'balance_pending' => 'decimal:2',
        'rate_override' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
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
        return $this->belongsTo(PropertyAccommodation::class, 'property_accommodation_id');
    }

    public function property()
    {
        return $this->hasOneThrough(Property::class, PropertyAccommodation::class, 'id', 'id', 'property_accommodation_id', 'property_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function commission()
    {
        return $this->hasOne(Commission::class, 'booking_id');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // Status management methods
    public function markAsConfirmed()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
    }

    public function checkIn()
    {
        $this->update([
            'status' => 'checked_in',
            'checked_in_at' => now()
        ]);
    }

    public function checkOut()
    {
        $this->update([
            'status' => 'checked_out',
            'checked_out_at' => now()
        ]);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    // Calculate balance
    public function calculateBalance()
    {
        $this->balance_pending = $this->total_amount - $this->advance_paid;
        $this->save();
    }

    // Check if booking is for B2B partner
    public function isB2bBooking()
    {
        return !is_null($this->b2b_partner_id);
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

    public function cancelledBooking()
    {
        return $this->hasOne(CancelledBooking::class);
    }
}