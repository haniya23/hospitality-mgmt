<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FinancialAdjustment extends Model
{
    protected $fillable = [
        'uuid',
        'adjustable_type',
        'adjustable_id',
        'adjustment_type',
        'original_amount',
        'adjusted_amount',
        'adjustment_difference',
        'reason',
        'adjusted_by',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'adjusted_amount' => 'decimal:2',
        'adjustment_difference' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }

            // Calculate difference if not set
            if (is_null($model->adjustment_difference)) {
                $model->adjustment_difference = abs($model->adjusted_amount - $model->original_amount);
            }

            // Log to audit trail
            AuditLog::logAction(
                'financial_adjustment',
                $model->adjustable,
                ['amount' => $model->original_amount],
                ['amount' => $model->adjusted_amount],
                $model->reason
            );
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the adjustable model (IncomeRecord or ExpenseRecord).
     */
    public function adjustable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the adjustment.
     */
    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeCredits($query)
    {
        return $query->where('adjustment_type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('adjustment_type', 'debit');
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('adjustable_type', get_class($model))
            ->where('adjustable_id', $model->id);
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Create an adjustment for an income record.
     */
    public static function adjustIncome(IncomeRecord $income, $newAmount, $reason)
    {
        return static::create([
            'adjustable_type' => IncomeRecord::class,
            'adjustable_id' => $income->id,
            'adjustment_type' => $newAmount > $income->amount ? 'credit' : 'debit',
            'original_amount' => $income->amount,
            'adjusted_amount' => $newAmount,
            'reason' => $reason,
            'adjusted_by' => auth()->id(),
        ]);
    }

    /**
     * Create an adjustment for an expense record.
     */
    public static function adjustExpense(ExpenseRecord $expense, $newAmount, $reason)
    {
        return static::create([
            'adjustable_type' => ExpenseRecord::class,
            'adjustable_id' => $expense->id,
            'adjustment_type' => $newAmount > $expense->amount ? 'debit' : 'credit',
            'original_amount' => $expense->amount,
            'adjusted_amount' => $newAmount,
            'reason' => $reason,
            'adjusted_by' => auth()->id(),
        ]);
    }

    /**
     * Get formatted adjustment type for display.
     */
    public function getAdjustmentTypeLabelAttribute()
    {
        return $this->adjustment_type === 'credit' ? 'Credit (+)' : 'Debit (-)';
    }
}
