<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExpenseRecord extends Model
{
    use SoftDeletes, HasCreatedUpdatedBy;

    protected $fillable = [
        'uuid',
        'property_id',
        'accommodation_id',
        'expense_category_id',
        'title',
        'amount',
        'transaction_date',
        'payment_method',
        'payment_status',
        'paid_amount',
        'notes',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
        'vendor_name',
        'receipt_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
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

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
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

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('expense_category_id', $categoryId);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
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
                ? -$adj->adjustment_difference
                : $adj->adjustment_difference;
        });

        return $this->amount + $adjustmentTotal;
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'upi' => 'UPI',
            'cheque' => 'Cheque',
            'other' => 'Other',
            default => ucfirst($this->payment_method),
        };
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Mark the expense as fully paid.
     */
    public function markAsPaid($receiptNumber = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_amount' => $this->amount,
            'receipt_number' => $receiptNumber ?? $this->receipt_number,
        ]);
    }

    /**
     * Generate the next recurring expense if applicable.
     */
    public function generateNextRecurrence()
    {
        if (!$this->is_recurring || !$this->recurring_frequency) {
            return null;
        }

        $nextDate = match ($this->recurring_frequency) {
            'daily' => $this->transaction_date->addDay(),
            'weekly' => $this->transaction_date->addWeek(),
            'monthly' => $this->transaction_date->addMonth(),
            'quarterly' => $this->transaction_date->addMonths(3),
            'yearly' => $this->transaction_date->addYear(),
            default => null,
        };

        if (!$nextDate || ($this->recurring_end_date && $nextDate->gt($this->recurring_end_date))) {
            return null;
        }

        return static::create([
            'property_id' => $this->property_id,
            'accommodation_id' => $this->accommodation_id,
            'expense_category_id' => $this->expense_category_id,
            'title' => $this->title,
            'amount' => $this->amount,
            'transaction_date' => $nextDate,
            'payment_method' => $this->payment_method,
            'payment_status' => 'unpaid',
            'paid_amount' => 0,
            'notes' => $this->notes,
            'is_recurring' => true,
            'recurring_frequency' => $this->recurring_frequency,
            'recurring_end_date' => $this->recurring_end_date,
            'vendor_name' => $this->vendor_name,
            'created_by' => auth()->id(),
        ]);
    }
}
