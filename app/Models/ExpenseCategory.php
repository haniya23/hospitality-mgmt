<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all expense records for this category.
     */
    public function expenseRecords()
    {
        return $this->hasMany(ExpenseRecord::class);
    }

    /**
     * Scope for active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total expenses for this category within a date range.
     */
    public function getTotalExpenses($propertyId = null, $startDate = null, $endDate = null)
    {
        $query = $this->expenseRecords();

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        return $query->sum('amount');
    }
}
