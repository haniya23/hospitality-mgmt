<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportItem extends Model
{
    protected $fillable = [
        'financial_report_id',
        'item_type',
        'category',
        'amount',
        'transaction_count',
        'breakdown',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'breakdown' => 'array',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function report()
    {
        return $this->belongsTo(FinancialReport::class, 'financial_report_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeIncome($query)
    {
        return $query->where('item_type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('item_type', 'expense');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getIsIncomeAttribute()
    {
        return $this->item_type === 'income';
    }

    public function getIsExpenseAttribute()
    {
        return $this->item_type === 'expense';
    }

    public function getAveragePerTransactionAttribute()
    {
        if ($this->transaction_count === 0) {
            return 0;
        }
        return round($this->amount / $this->transaction_count, 2);
    }
}
