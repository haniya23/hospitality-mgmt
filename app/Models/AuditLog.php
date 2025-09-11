<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            
            // Auto-fill IP and user agent if not provided
            if (empty($model->ip_address)) {
                $model->ip_address = request()->ip();
            }
            if (empty($model->user_agent)) {
                $model->user_agent = request()->userAgent();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Static method to log actions
    public static function logAction($action, $model, $oldValues = null, $newValues = null, $reason = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'reason' => $reason,
        ]);
    }

    // Log booking status changes
    public static function logBookingStatusChange($booking, $oldStatus, $newStatus, $reason = null)
    {
        return static::logAction(
            'status_changed',
            $booking,
            ['status' => $oldStatus],
            ['status' => $newStatus],
            $reason ?? "Status changed from {$oldStatus} to {$newStatus}"
        );
    }

    // Log price overrides
    public static function logPriceOverride($booking, $oldPrice, $newPrice, $reason = null)
    {
        return static::logAction(
            'price_override',
            $booking,
            ['total_amount' => $oldPrice],
            ['total_amount' => $newPrice],
            $reason ?? "Price overridden from ₹{$oldPrice} to ₹{$newPrice}"
        );
    }

    // Log commission payments
    public static function logCommissionPayment($commission, $amount, $reason = null)
    {
        return static::logAction(
            'commission_paid',
            $commission,
            ['amount_paid' => $commission->getOriginal('amount_paid')],
            ['amount_paid' => $amount],
            $reason ?? "Commission payment of ₹{$amount} recorded"
        );
    }
}