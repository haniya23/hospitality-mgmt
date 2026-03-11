<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FinancialReport extends Model
{
    protected $fillable = [
        'uuid',
        'property_id',
        'financial_period_id',
        'report_type',
        'report_number',
        'total_income',
        'total_expenses',
        'net_profit',
        'outstanding_receivables',
        'outstanding_payables',
        'summary_data',
        'status',
        'generated_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'outstanding_receivables' => 'decimal:2',
        'outstanding_payables' => 'decimal:2',
        'summary_data' => 'array',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }

            if (empty($model->report_number)) {
                $model->report_number = static::generateReportNumber($model->report_type);
            }

            // Calculate net profit if not set
            if (is_null($model->net_profit)) {
                $model->net_profit = $model->total_income - $model->total_expenses;
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

    public function period()
    {
        return $this->belongsTo(FinancialPeriod::class, 'financial_period_id');
    }

    public function items()
    {
        return $this->hasMany(FinancialReportItem::class);
    }

    public function incomeItems()
    {
        return $this->items()->where('item_type', 'income');
    }

    public function expenseItems()
    {
        return $this->items()->where('item_type', 'expense');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeWeekly($query)
    {
        return $query->where('report_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('report_type', 'monthly');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getIsLockedAttribute()
    {
        return $this->status === 'locked';
    }

    public function getIsApprovedAttribute()
    {
        return in_array($this->status, ['approved', 'locked']);
    }

    public function getCanBeEditedAttribute()
    {
        return $this->status === 'draft';
    }

    public function getProfitMarginAttribute()
    {
        if ($this->total_income == 0) {
            return 0;
        }
        return round(($this->net_profit / $this->total_income) * 100, 2);
    }

    public function getReportTitleAttribute()
    {
        $period = $this->period;
        if (!$period) {
            return $this->report_number;
        }

        if ($this->report_type === 'weekly') {
            return 'Weekly Report - ' . $period->start_date->format('M d') . ' to ' . $period->end_date->format('M d, Y');
        }

        return 'Monthly Report - ' . $period->start_date->format('F Y');
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Generate a unique report number.
     */
    public static function generateReportNumber($type)
    {
        $prefix = $type === 'weekly' ? 'WR' : 'MR';
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;

        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Approve the report.
     */
    public function approve($userId = null)
    {
        if ($this->is_approved) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Lock the report (final state).
     */
    public function lock()
    {
        if ($this->is_locked) {
            return false;
        }

        // Lock the period as well
        $this->period?->lock();

        $this->update(['status' => 'locked']);

        return true;
    }

    /**
     * Add a line item to the report.
     */
    public function addItem($type, $category, $amount, $transactionCount = 1, $breakdown = null)
    {
        return $this->items()->create([
            'item_type' => $type,
            'category' => $category,
            'amount' => $amount,
            'transaction_count' => $transactionCount,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Recalculate totals from items.
     */
    public function recalculateTotals()
    {
        $this->total_income = $this->incomeItems()->sum('amount');
        $this->total_expenses = $this->expenseItems()->sum('amount');
        $this->net_profit = $this->total_income - $this->total_expenses;
        $this->save();
    }
}
