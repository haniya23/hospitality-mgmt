<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'partner_id',
        'percentage',
        'amount',
        'amount_paid',
        'status',
        'paid_at',
        'paid_by',
        'payment_notes',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
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

    public function booking()
    {
        return $this->belongsTo(Reservation::class, 'booking_id');
    }

    public function partner()
    {
        return $this->belongsTo(B2bPartner::class, 'partner_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Commission management methods
    public function markAsPaid($amountPaid, $notes = null)
    {
        $this->update([
            'amount_paid' => $amountPaid,
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
            'payment_notes' => $notes,
        ]);
    }

    public function getOutstandingAmount()
    {
        return $this->amount - $this->amount_paid;
    }

    public function isFullyPaid()
    {
        return $this->amount_paid >= $this->amount;
    }

    // Calculate commission based on booking amount and partner rate
    public static function calculateForBooking(Reservation $booking)
    {
        if (!$booking->b2bPartner) {
            return null;
        }

        $commissionRate = $booking->b2bPartner->commission_rate;
        $commissionAmount = ($booking->total_amount * $commissionRate) / 100;

        return static::create([
            'booking_id' => $booking->id,
            'partner_id' => $booking->b2b_partner_id,
            'percentage' => $commissionRate,
            'amount' => $commissionAmount,
            'status' => 'pending',
        ]);
    }
}