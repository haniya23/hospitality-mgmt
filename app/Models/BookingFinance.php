<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingFinance extends Model
{
    use HasCreatedUpdatedBy;

    protected $fillable = [
        'uuid',
        'finance_number',
        'reservation_id',
        'property_id',
        'accommodation_id',
        'b2b_partner_id',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'total_amount',
        'advance_received',
        'balance_pending',
        'additional_charges',
        'refund_amount',
        'final_amount',
        'payment_status',
        'booking_status',
        'last_payment_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'advance_received' => 'decimal:2',
        'balance_pending' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'last_payment_date' => 'datetime',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->finance_number)) {
                $model->finance_number = static::generateFinanceNumber();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(PropertyAccommodation::class, 'accommodation_id');
    }

    public function b2bPartner()
    {
        return $this->belongsTo(B2bPartner::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('payment_status', ['unpaid', 'partial']);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByBookingStatus($query, $status)
    {
        return $query->where('booking_status', $status);
    }

    public function scopeWithOutstanding($query)
    {
        return $query->where('balance_pending', '>', 0);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getIsFullyPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getHasOutstandingAttribute()
    {
        return $this->balance_pending > 0;
    }

    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'unpaid' => 'Unpaid',
            'partial' => 'Partially Paid',
            'paid' => 'Fully Paid',
            'refunded' => 'Refunded',
            default => ucfirst($this->payment_status),
        };
    }

    public function getBookingStatusLabelAttribute()
    {
        return match ($this->booking_status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst($this->booking_status),
        };
    }

    public function getPaymentStatusColorAttribute()
    {
        return match ($this->payment_status) {
            'unpaid' => 'red',
            'partial' => 'yellow',
            'paid' => 'green',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    // ==========================================
    // STATIC METHODS
    // ==========================================

    /**
     * Generate a unique finance number.
     */
    public static function generateFinanceNumber(): string
    {
        do {
            $number = 'FIN-' . strtoupper(Str::random(8));
        } while (static::where('finance_number', $number)->exists());

        return $number;
    }

    /**
     * Create a BookingFinance record from a Reservation.
     */
    public static function createFromReservation(Reservation $reservation): static
    {
        $accommodation = $reservation->accommodation;

        if (!$accommodation) {
            throw new \Exception('Reservation has no accommodation');
        }

        $totalAmount = $reservation->total_amount ?? 0;
        $advanceReceived = $reservation->advance_paid ?? 0;
        $balancePending = $reservation->balance_pending ?? ($totalAmount - $advanceReceived);

        // Determine payment status
        $paymentStatus = 'unpaid';
        if ($advanceReceived >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'paid';
        } elseif ($advanceReceived > 0) {
            $paymentStatus = 'partial';
        }

        return static::create([
            'reservation_id' => $reservation->id,
            'property_id' => $accommodation->property_id,
            'accommodation_id' => $accommodation->id,
            'b2b_partner_id' => $reservation->b2b_partner_id,
            'booking_date' => $reservation->created_at->toDateString(),
            'check_in_date' => $reservation->check_in_date,
            'check_out_date' => $reservation->check_out_date,
            'total_amount' => $totalAmount,
            'advance_received' => $advanceReceived,
            'balance_pending' => $balancePending,
            'additional_charges' => 0,
            'refund_amount' => 0,
            'final_amount' => $totalAmount,
            'payment_status' => $paymentStatus,
            'booking_status' => $reservation->status ?? 'pending',
            'last_payment_date' => $advanceReceived > 0 ? now() : null,
            'created_by' => auth()->id(),
        ]);
    }

    // ==========================================
    // INSTANCE METHODS
    // ==========================================

    /**
     * Sync finance record from reservation changes.
     */
    public function updateFromReservation(): void
    {
        $reservation = $this->reservation;

        if (!$reservation) {
            return;
        }

        $totalAmount = $reservation->total_amount ?? $this->total_amount;
        $advanceReceived = $reservation->advance_paid ?? $this->advance_received;
        $balancePending = $reservation->balance_pending ?? ($totalAmount - $advanceReceived);

        // Determine payment status
        $paymentStatus = $this->payment_status;
        if ($advanceReceived >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'paid';
        } elseif ($advanceReceived > 0) {
            $paymentStatus = 'partial';
        } elseif ($advanceReceived == 0) {
            $paymentStatus = 'unpaid';
        }

        $this->update([
            'check_in_date' => $reservation->check_in_date,
            'check_out_date' => $reservation->check_out_date,
            'total_amount' => $totalAmount,
            'advance_received' => $advanceReceived,
            'balance_pending' => $balancePending,
            'final_amount' => $totalAmount + $this->additional_charges - $this->refund_amount,
            'payment_status' => $paymentStatus,
            'booking_status' => $reservation->status,
        ]);
    }

    /**
     * Record a payment.
     */
    public function recordPayment(float $amount, ?string $notes = null): void
    {
        $newAdvance = $this->advance_received + $amount;
        $newBalance = max(0, $this->final_amount - $newAdvance);

        // Determine new payment status
        $paymentStatus = 'partial';
        if ($newAdvance >= $this->final_amount) {
            $paymentStatus = 'paid';
            $newBalance = 0;
        } elseif ($newAdvance == 0) {
            $paymentStatus = 'unpaid';
        }

        $this->update([
            'advance_received' => $newAdvance,
            'balance_pending' => $newBalance,
            'payment_status' => $paymentStatus,
            'last_payment_date' => now(),
            'notes' => $notes ? ($this->notes ? $this->notes . "\n" . $notes : $notes) : $this->notes,
        ]);

        // Sync back to reservation
        if ($this->reservation) {
            $this->reservation->update([
                'advance_paid' => $newAdvance,
                'balance_pending' => $newBalance,
            ]);
        }
    }

    /**
     * Record additional charges.
     */
    public function recordAdditionalCharge(float $amount, ?string $reason = null): void
    {
        $newCharges = $this->additional_charges + $amount;
        $newFinal = $this->total_amount + $newCharges - $this->refund_amount;
        $newBalance = $newFinal - $this->advance_received;

        // Recalculate payment status
        $paymentStatus = $this->payment_status;
        if ($this->advance_received >= $newFinal && $newFinal > 0) {
            $paymentStatus = 'paid';
        } elseif ($this->advance_received > 0) {
            $paymentStatus = 'partial';
        }

        $note = $reason ? "Additional charge: ₹{$amount} - {$reason}" : "Additional charge: ₹{$amount}";

        $this->update([
            'additional_charges' => $newCharges,
            'final_amount' => $newFinal,
            'balance_pending' => max(0, $newBalance),
            'payment_status' => $paymentStatus,
            'notes' => $this->notes ? $this->notes . "\n" . $note : $note,
        ]);
    }

    /**
     * Record a refund.
     */
    public function recordRefund(float $amount, ?string $reason = null): void
    {
        $newRefund = $this->refund_amount + $amount;
        $newFinal = $this->total_amount + $this->additional_charges - $newRefund;
        $newBalance = max(0, $newFinal - $this->advance_received);

        $note = $reason ? "Refund: ₹{$amount} - {$reason}" : "Refund: ₹{$amount}";

        $this->update([
            'refund_amount' => $newRefund,
            'final_amount' => $newFinal,
            'balance_pending' => $newBalance,
            'payment_status' => $newBalance <= 0 ? 'refunded' : $this->payment_status,
            'notes' => $this->notes ? $this->notes . "\n" . $note : $note,
        ]);
    }

    /**
     * Recalculate all financial totals.
     */
    public function recalculate(): void
    {
        $this->final_amount = $this->total_amount + $this->additional_charges - $this->refund_amount;
        $this->balance_pending = max(0, $this->final_amount - $this->advance_received);

        if ($this->advance_received >= $this->final_amount && $this->final_amount > 0) {
            $this->payment_status = 'paid';
        } elseif ($this->advance_received > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }
}
