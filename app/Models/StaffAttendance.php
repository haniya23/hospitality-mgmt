<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'date',
        'check_in_time',
        'check_out_time',
        'hours_worked',
        'status',
        'notes',
        'location_data',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'location_data' => 'array',
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

    // Relationships
    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    // Helper methods
    public function calculateHoursWorked()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            
            // Subtract break time (1 hour for 8+ hour shifts)
            if ($hours >= 8) {
                $hours -= 1;
            }
            
            $this->hours_worked = max(0, $hours);
            $this->save();
            
            return $this->hours_worked;
        }
        
        return 0;
    }

    public function isLate()
    {
        if (!$this->check_in_time) {
            return false;
        }
        
        $expectedStartTime = Carbon::parse('09:00');
        $actualStartTime = Carbon::parse($this->check_in_time);
        
        return $actualStartTime->gt($expectedStartTime->addMinutes(15));
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'present' => 'bg-green-100 text-green-800',
            'absent' => 'bg-red-100 text-red-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'half_day' => 'bg-blue-100 text-blue-800',
            'on_leave' => 'bg-purple-100 text-purple-800',
            'holiday' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
