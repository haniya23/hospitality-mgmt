<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CheckOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'guest_id',
        'staff_id',
        'check_in_id',
        'guest_name',
        'room_number',
        'check_out_time',
        'services_used',
        'late_checkout_charges',
        'service_notes',
        'final_bill',
        'deposit_refund',
        'payment_status',
        'payment_notes',
        'rating',
        'feedback_comments',
        'guest_signature',
        'staff_signature',
        'status',
        'room_marked_clean',
        'uuid',
    ];

    protected $casts = [
        'check_out_time' => 'datetime',
        'services_used' => 'array',
        'late_checkout_charges' => 'decimal:2',
        'final_bill' => 'decimal:2',
        'deposit_refund' => 'decimal:2',
        'room_marked_clean' => 'boolean',
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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    // Status management methods
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markRoomAsClean()
    {
        $this->update(['room_marked_clean' => true]);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function getTotalChargesAttribute()
    {
        return $this->final_bill + $this->late_checkout_charges;
    }

    public function getNetAmountAttribute()
    {
        return $this->total_charges - $this->deposit_refund;
    }
}
