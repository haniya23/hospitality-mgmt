<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OwnerAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all staff assignments for user's properties
        $staffAssignments = StaffAssignment::whereHas('property', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->with(['user', 'property', 'role'])->get();
        
        // Get today's attendance for all staff
        $todaysAttendance = Attendance::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->whereDate('date', today())
        ->with(['staffAssignment.user', 'staffAssignment.property'])
        ->get();
        
        // Get attendance stats for current month
        $attendanceStats = $this->getOverallAttendanceStats($user->id);
        
        // Get pending leave requests
        $pendingLeaveRequests = LeaveRequest::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->where('status', 'pending')
        ->with(['staffAssignment.user', 'staffAssignment.property'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('owner.attendance', compact(
            'staffAssignments',
            'todaysAttendance',
            'attendanceStats',
            'pendingLeaveRequests'
        ));
    }

    public function staffAttendance(Request $request, $staffUuid)
    {
        $user = Auth::user();
        
        $staffAssignment = StaffAssignment::whereHas('property', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->where('uuid', $staffUuid)
        ->with(['user', 'property', 'role'])
        ->firstOrFail();
        
        // Get attendance records for the staff member
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        $attendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        // Get leave requests
        $leaveRequests = LeaveRequest::where('staff_assignment_id', $staffAssignment->id)
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        
        // Get stats
        $stats = $this->getStaffAttendanceStats($staffAssignment->id, $startDate, $endDate);

        return view('owner.staff-attendance', compact(
            'staffAssignment',
            'attendance',
            'leaveRequests',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    public function getAttendance(Request $request, $attendanceId)
    {
        $user = Auth::user();
        
        $attendance = Attendance::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->with(['staffAssignment.user', 'staffAssignment.property'])
        ->findOrFail($attendanceId);

        return response()->json([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    public function updateAttendance(Request $request, $attendanceId)
    {
        $user = Auth::user();
        
        $attendance = Attendance::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->findOrFail($attendanceId);

        $validator = Validator::make($request->all(), [
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $attendance->update([
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Recalculate hours if both times are provided
            if ($request->check_in_time && $request->check_out_time) {
                $checkIn = Carbon::parse($request->check_in_time);
                $checkOut = Carbon::parse($request->check_out_time);
                $hours = $checkOut->diffInMinutes($checkIn) / 60;
                
                if ($hours >= 8) {
                    $hours -= 1; // 1 hour break
                }
                
                $attendance->update(['hours_worked' => max(0, $hours)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully!',
                'attendance' => $attendance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function leaveRequests()
    {
        $user = Auth::user();
        
        $leaveRequests = LeaveRequest::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->with(['staffAssignment.user', 'staffAssignment.property'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('owner.leave-requests', compact('leaveRequests'));
    }

    public function approveLeaveRequest(Request $request, $leaveRequestId)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->findOrFail($leaveRequestId);

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This leave request cannot be approved.'
            ], 400);
        }

        try {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Mark attendance as leave for the requested dates
            $this->markAttendanceAsLeave($leaveRequest);

            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectLeaveRequest(Request $request, $leaveRequestId)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::whereHas('staffAssignment', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->findOrFail($leaveRequestId);

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This leave request cannot be rejected.'
            ], 400);
        }

        try {
            $leaveRequest->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'rejected_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getOverallAttendanceStats($ownerId)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $attendance = Attendance::whereHas('staffAssignment', function($query) use ($ownerId) {
            $query->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });
        })->whereBetween('date', [$startDate, $endDate])
        ->get();

        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $totalHours = $attendance->sum('hours_worked');
        $totalDays = $attendance->count();
        
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        return [
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'total_hours' => round($totalHours, 1),
            'attendance_percentage' => $attendancePercentage,
            'total_days' => $totalDays,
            'total_staff' => StaffAssignment::whereHas('property', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })->count(),
        ];
    }

    private function getStaffAttendanceStats($staffAssignmentId, $startDate, $endDate)
    {
        $attendance = Attendance::where('staff_assignment_id', $staffAssignmentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $totalHours = $attendance->sum('hours_worked');
        $totalDays = $attendance->count();
        
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        return [
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'total_hours' => round($totalHours, 1),
            'attendance_percentage' => $attendancePercentage,
            'total_days' => $totalDays,
        ];
    }

    private function markAttendanceAsLeave($leaveRequest)
    {
        $start = Carbon::parse($leaveRequest->start_date);
        $end = Carbon::parse($leaveRequest->end_date);
        
        while ($start->lte($end)) {
            // Skip weekends
            if ($start->dayOfWeek !== 0 && $start->dayOfWeek !== 6) {
                Attendance::updateOrCreate(
                    [
                        'staff_assignment_id' => $leaveRequest->staff_assignment_id,
                        'date' => $start->toDateString(),
                    ],
                    [
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'status' => 'leave',
                        'notes' => "Leave approved: {$leaveRequest->leave_type}",
                    ]
                );
            }
            $start->addDay();
        }
    }
}