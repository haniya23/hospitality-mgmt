<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelledBooking extends Model
{
    protected $fillable = [
        'reservation_id',
        'reason',
        'description',
        'refund_amount',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}