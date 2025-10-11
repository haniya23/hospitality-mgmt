<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Get attendance history
     */
    public function index(Request $request)
    {
        $staff = $request->user()->staffMember;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        $query = StaffAttendance::where('staff_member_id', $staff->id);

        // Apply filters
        if ($request->has('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendance = $query->orderBy('date', 'desc')
            ->paginate($request->input('per_page', 30));

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'date' => $record->date->format('Y-m-d'),
                        'check_in_time' => $record->check_in_time,
                        'check_out_time' => $record->check_out_time,
                        'status' => $record->status,
                        'notes' => $record->notes,
                        'working_hours' => $record->working_hours,
                    ];
                }),
                'pagination' => [
                    'current_page' => $attendance->currentPage(),
                    'last_page' => $attendance->lastPage(),
                    'per_page' => $attendance->perPage(),
                    'total' => $attendance->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Get today's attendance
     */
    public function today(Request $request)
    {
        $staff = $request->user()->staffMember;

        $attendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->first();

        return response()->json([
            'success' => true,
            'data' => $attendance ? [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
                'notes' => $attendance->notes,
                'working_hours' => $attendance->working_hours,
            ] : null,
        ], 200);
    }

    /**
     * Check in
     */
    public function checkIn(Request $request)
    {
        $staff = $request->user()->staffMember;

        // Check if already checked in today
        $existingAttendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today',
            ], 400);
        }

        $attendance = StaffAttendance::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staff->id,
            'date' => today(),
            'check_in_time' => now()->format('H:i:s'),
            'status' => 'present',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully',
            'data' => [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_time,
                'status' => $attendance->status,
            ],
        ], 201);
    }

    /**
     * Check out
     */
    public function checkOut(Request $request)
    {
        $staff = $request->user()->staffMember;

        $attendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for today',
            ], 400);
        }

        $checkInTime = \Carbon\Carbon::parse($attendance->check_in_time);
        $checkOutTime = now();
        $workingHours = $checkInTime->diffInHours($checkOutTime, true);

        $attendance->update([
            'check_out_time' => $checkOutTime->format('H:i:s'),
            'working_hours' => round($workingHours, 2),
            'notes' => $request->notes ? $attendance->notes . ' | ' . $request->notes : $attendance->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'data' => [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'working_hours' => $attendance->working_hours,
            ],
        ], 200);
    }

    /**
     * Get leave requests
     */
    public function leaveRequests(Request $request)
    {
        $staff = $request->user()->staffMember;

        $query = StaffLeaveRequest::where('staff_member_id', $staff->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'leave_requests' => $leaveRequests->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'uuid' => $leave->uuid,
                        'leave_type' => $leave->leave_type,
                        'start_date' => $leave->start_date->format('Y-m-d'),
                        'end_date' => $leave->end_date->format('Y-m-d'),
                        'days_count' => $leave->days_count,
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                        'reviewed_at' => $leave->reviewed_at?->toIso8601String(),
                        'review_notes' => $leave->review_notes,
                    ];
                }),
                'pagination' => [
                    'current_page' => $leaveRequests->currentPage(),
                    'last_page' => $leaveRequests->lastPage(),
                    'per_page' => $leaveRequests->perPage(),
                    'total' => $leaveRequests->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Submit leave request
     */
    public function submitLeaveRequest(Request $request)
    {
        $staff = $request->user()->staffMember;

        $request->validate([
            'leave_type' => 'required|in:sick,casual,annual,unpaid,emergency',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysCount = $startDate->diffInDays($endDate) + 1;

        $leaveRequest = StaffLeaveRequest::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staff->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully',
            'data' => [
                'id' => $leaveRequest->id,
                'uuid' => $leaveRequest->uuid,
                'leave_type' => $leaveRequest->leave_type,
                'start_date' => $leaveRequest->start_date->format('Y-m-d'),
                'end_date' => $leaveRequest->end_date->format('Y-m-d'),
                'days_count' => $leaveRequest->days_count,
                'status' => $leaveRequest->status,
            ],
        ], 201);
    }
}

