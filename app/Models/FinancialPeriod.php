<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FinancialPeriod extends Model
{
    protected $fillable = [
        'uuid',
        'property_id',
        'period_type',
        'start_date',
        'end_date',
        'status',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'locked_at' => 'datetime',
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

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function reports()
    {
        return $this->hasMany(FinancialReport::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeWeekly($query)
    {
        return $query->where('period_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getIsLockedAttribute()
    {
        return $this->status === 'locked' || $this->status === 'closed';
    }

    public function getIsCurrentAttribute()
    {
        return now()->between($this->start_date, $this->end_date);
    }

    public function getPeriodLabelAttribute()
    {
        if ($this->period_type === 'weekly') {
            return 'Week of ' . $this->start_date->format('M d, Y');
        }
        return $this->start_date->format('F Y');
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Lock the period (prevents modifications).
     */
    public function lock($userId = null)
    {
        if ($this->is_locked) {
            return false;
        }

        $this->update([
            'status' => 'locked',
            'locked_by' => $userId ?? auth()->id(),
            'locked_at' => now(),
        ]);

        return true;
    }

    /**
     * Unlock the period (admin only).
     */
    public function unlock()
    {
        $this->update([
            'status' => 'open',
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    /**
     * Get or create week period for a given date.
     */
    public static function getOrCreateWeekPeriod($date, $propertyId = null)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return static::firstOrCreate(
            [
                'property_id' => $propertyId,
                'period_type' => 'weekly',
                'start_date' => $startOfWeek,
            ],
            [
                'end_date' => $endOfWeek,
                'status' => 'open',
            ]
        );
    }

    /**
     * Get or create month period for a given date.
     */
    public static function getOrCreateMonthPeriod($date, $propertyId = null)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return static::firstOrCreate(
            [
                'property_id' => $propertyId,
                'period_type' => 'monthly',
                'start_date' => $startOfMonth,
            ],
            [
                'end_date' => $endOfMonth,
                'status' => 'open',
            ]
        );
    }
}
