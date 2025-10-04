<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'staff_assignment_id',
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

    // Relationships
    public function staffAssignment()
    {
        return $this->belongsTo(StaffAssignment::class);
    }

    public function staff()
    {
        return $this->hasOneThrough(User::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'user_id');
    }

    public function property()
    {
        return $this->hasOneThrough(Property::class, StaffAssignment::class, 'id', 'id', 'staff_assignment_id', 'property_id');
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForStaff($query, $userId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->whereHas('staffAssignment', function($q) use ($propertyId) {
            $q->where('property_id', $propertyId);
        });
    }

    // Helper methods
    public function calculateHoursWorked()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            
            // Calculate hours worked
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            
            // Subtract break time (assuming 1 hour break for 8+ hour shifts)
            if ($hours >= 8) {
                $hours -= 1; // 1 hour break
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
        
        // Assuming work starts at 9:00 AM
        $expectedStartTime = Carbon::parse('09:00');
        $actualStartTime = Carbon::parse($this->check_in_time);
        
        return $actualStartTime->gt($expectedStartTime->addMinutes(15)); // 15 minutes grace period
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'half_day' => 'blue',
            'leave' => 'purple',
            default => 'gray'
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'present' => 'fas fa-check-circle',
            'absent' => 'fas fa-times-circle',
            'late' => 'fas fa-clock',
            'half_day' => 'fas fa-clock-half',
            'leave' => 'fas fa-calendar-times',
            default => 'fas fa-question-circle'
        };
    }

    // Static methods
    public static function markAttendance($staffAssignmentId, $date, $checkInTime = null, $checkOutTime = null, $status = 'present', $notes = null)
    {
        $attendance = self::updateOrCreate(
            [
                'staff_assignment_id' => $staffAssignmentId,
                'date' => $date,
            ],
            [
                'check_in_time' => $checkInTime,
                'check_out_time' => $checkOutTime,
                'status' => $status,
                'notes' => $notes,
            ]
        );

        if ($checkInTime && $checkOutTime) {
            $attendance->calculateHoursWorked();
        }

        return $attendance;
    }

    public static function getStaffAttendanceStats($staffAssignmentId, $startDate = null, $endDate = null)
    {
        $query = self::where('staff_assignment_id', $staffAssignmentId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $attendance = $query->get();

        return [
            'total_days' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'half_days' => $attendance->where('status', 'half_day')->count(),
            'leave_days' => $attendance->where('status', 'leave')->count(),
            'total_hours' => $attendance->sum('hours_worked'),
            'average_hours_per_day' => $attendance->where('status', 'present')->avg('hours_worked') ?? 0,
            'attendance_percentage' => $attendance->count() > 0 ? 
                round(($attendance->whereIn('status', ['present', 'late', 'half_day'])->count() / $attendance->count()) * 100, 2) : 0,
        ];
    }
}