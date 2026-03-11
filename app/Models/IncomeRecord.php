<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class IncomeRecord extends Model
{
    use SoftDeletes, HasCreatedUpdatedBy;

    protected $fillable = [
        'uuid',
        'property_id',
        'accommodation_id',
        'b2b_partner_id',
        'reservation_id',
        'payment_id',
        'income_type',
        'amount',
        'transaction_date',
        'payment_status',
        'paid_amount',
        'reference_number',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'transaction_date' => 'date',
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

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function adjustments()
    {
        return $this->morphMany(FinancialAdjustment::class, 'adjustable');
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
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('income_type', $type);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getOutstandingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getAdjustedAmountAttribute()
    {
        $adjustmentTotal = $this->adjustments->sum(function ($adj) {
            return $adj->adjustment_type === 'credit'
                ? $adj->adjustment_difference
                : -$adj->adjustment_difference;
        });

        return $this->amount + $adjustmentTotal;
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Create an income record from a completed payment.
     */
    public static function createFromPayment(Payment $payment)
    {
        $invoice = $payment->invoice;
        $reservation = $invoice?->reservation;
        $accommodation = $reservation?->accommodation;

        if (!$accommodation) {
            return null;
        }

        return static::create([
            'property_id' => $accommodation->property_id,
            'accommodation_id' => $accommodation->id,
            'b2b_partner_id' => $reservation->b2b_partner_id,
            'reservation_id' => $reservation->id,
            'income_type' => 'booking',
            'amount' => $payment->amount,
            'transaction_date' => $payment->paid_at ?? now(),
            'payment_status' => 'paid',
            'paid_amount' => $payment->amount,
            'reference_number' => $payment->reference_number ?? $payment->payment_id,
            'notes' => "Auto-created from payment #{$payment->id}",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Mark the income as fully paid.
     */
    public function markAsPaid($referenceNumber = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_amount' => $this->amount,
            'reference_number' => $referenceNumber ?? $this->reference_number,
        ]);
    }

    /**
     * Record a partial payment.
     */
    public function recordPartialPayment($amount, $referenceNumber = null)
    {
        $newPaidAmount = $this->paid_amount + $amount;

        $this->update([
            'payment_status' => $newPaidAmount >= $this->amount ? 'paid' : 'partial',
            'paid_amount' => min($newPaidAmount, $this->amount),
            'reference_number' => $referenceNumber ?? $this->reference_number,
        ]);
    }

    /**
     * Get income type label for display.
     */
    public function getIncomeTypeLabelAttribute()
    {
        return match ($this->income_type) {
            'booking' => 'Booking Revenue',
            'rental' => 'Rental Income',
            'service' => 'Service Charge',
            'deposit' => 'Security Deposit',
            'penalty' => 'Penalty/Late Fee',
            'commission' => 'Commission',
            'other' => 'Other Income',
            default => ucfirst($this->income_type),
        };
    }
}
