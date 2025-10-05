<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    protected $fillable = [
        'uuid',
        'staff_assignment_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'admin_notes',
        'status',
        'approved_by',
        'approved_at',
        'rejected_at',
        'attachments',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attachments' => 'array',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', today());
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', today())
                   ->where('end_date', '>=', today());
    }

    // Helper methods
    public function calculateTotalDays()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        // Calculate working days (excluding weekends)
        $totalDays = 0;
        $current = $start->copy();
        
        while ($current->lte($end)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $totalDays++;
            }
            $current->addDay();
        }
        
        $this->total_days = $totalDays;
        $this->save();
        
        return $totalDays;
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'pending' => 'fas fa-clock',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban',
            default => 'fas fa-question-circle'
        };
    }

    public function getLeaveTypeColor()
    {
        return match($this->leave_type) {
            'sick' => 'red',
            'personal' => 'blue',
            'vacation' => 'green',
            'emergency' => 'orange',
            'other' => 'gray',
            default => 'gray'
        };
    }

    public function getLeaveTypeIcon()
    {
        return match($this->leave_type) {
            'sick' => 'fas fa-thermometer-half',
            'personal' => 'fas fa-user',
            'vacation' => 'fas fa-umbrella-beach',
            'emergency' => 'fas fa-exclamation-triangle',
            'other' => 'fas fa-calendar-alt',
            default => 'fas fa-calendar'
        };
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending' && $this->start_date > today();
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function canBeRejected()
    {
        return $this->status === 'pending';
    }

    // Static methods
    public static function createRequest($staffAssignmentId, $leaveType, $startDate, $endDate, $reason, $attachments = [])
    {
        $request = self::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'staff_assignment_id' => $staffAssignmentId,
            'leave_type' => $leaveType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
            'attachments' => $attachments,
            'status' => 'pending',
        ]);

        $request->calculateTotalDays();
        
        return $request;
    }

    public function approve($approvedBy, $adminNotes = null)
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('This leave request cannot be approved.');
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        // Mark attendance as leave for the requested dates
        $this->markAttendanceAsLeave();

        return $this;
    }

    public function reject($approvedBy, $adminNotes = null)
    {
        if (!$this->canBeRejected()) {
            throw new \Exception('This leave request cannot be rejected.');
        }

        $this->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'rejected_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        return $this;
    }

    public function cancel()
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('This leave request cannot be cancelled.');
        }

        $this->update([
            'status' => 'cancelled',
        ]);

        return $this;
    }

    private function markAttendanceAsLeave()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        while ($start->lte($end)) {
            // Skip weekends
            if ($start->dayOfWeek !== 0 && $start->dayOfWeek !== 6) {
                Attendance::updateOrCreate(
                    [
                        'staff_assignment_id' => $this->staff_assignment_id,
                        'date' => $start->toDateString(),
                    ],
                    [
                        'status' => 'leave',
                        'notes' => "Leave approved: {$this->leave_type}",
                    ]
                );
            }
            $start->addDay();
        }
    }

    public static function getStaffLeaveStats($staffAssignmentId, $year = null)
    {
        $year = $year ?? now()->year;
        
        $query = self::where('staff_assignment_id', $staffAssignmentId)
                    ->whereYear('start_date', $year);

        $requests = $query->get();

        return [
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'approved_requests' => $requests->where('status', 'approved')->count(),
            'rejected_requests' => $requests->where('status', 'rejected')->count(),
            'total_days_requested' => $requests->sum('total_days'),
            'total_days_approved' => $requests->where('status', 'approved')->sum('total_days'),
            'leave_by_type' => $requests->groupBy('leave_type')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'days' => $group->sum('total_days'),
                ];
            }),
        ];
    }
}