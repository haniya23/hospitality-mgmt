<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'start_date',
        'end_date',
        'total_days',
        'leave_type',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'attachments',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            
            // Auto-calculate total days
            $model->calculateTotalDays();
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', today());
    }

    // Helper methods
    public function calculateTotalDays()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        $totalDays = 0;
        $current = $start->copy();
        
        while ($current->lte($end)) {
            // Skip weekends
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $totalDays++;
            }
            $current->addDay();
        }
        
        $this->total_days = $totalDays;
        
        return $totalDays;
    }

    public function approve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        // Mark attendance as on_leave for approved dates
        $this->markAttendanceAsLeave();

        // Notify staff member
        StaffNotification::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $this->staff_member_id,
            'from_user_id' => $reviewerId,
            'type' => 'leave_approved',
            'title' => 'Leave Request Approved',
            'message' => "Your leave request from {$this->start_date->format('M d')} to {$this->end_date->format('M d')} has been approved.",
            'priority' => 'medium',
        ]);

        return $this;
    }

    public function reject($reviewerId, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        // Notify staff member
        StaffNotification::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $this->staff_member_id,
            'from_user_id' => $reviewerId,
            'type' => 'leave_rejected',
            'title' => 'Leave Request Rejected',
            'message' => "Your leave request from {$this->start_date->format('M d')} to {$this->end_date->format('M d')} has been rejected. Reason: {$notes}",
            'priority' => 'high',
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
                StaffAttendance::updateOrCreate(
                    [
                        'staff_member_id' => $this->staff_member_id,
                        'date' => $start->toDateString(),
                    ],
                    [
                        'uuid' => Str::uuid(),
                        'status' => 'on_leave',
                        'notes' => "Leave approved: {$this->leave_type}",
                    ]
                );
            }
            $start->addDay();
        }
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getLeaveTypeIcon()
    {
        return match($this->leave_type) {
            'sick' => 'fas fa-thermometer-half',
            'vacation' => 'fas fa-umbrella-beach',
            'personal' => 'fas fa-user',
            'emergency' => 'fas fa-exclamation-triangle',
            'maternity' => 'fas fa-baby',
            'paternity' => 'fas fa-baby-carriage',
            default => 'fas fa-calendar-alt'
        };
    }
}
