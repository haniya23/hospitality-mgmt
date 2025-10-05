<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StaffAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active staff assignment
        $staffAssignment = $user->getActiveStaffAssignments()->first();
        
        if (!$staffAssignment) {
            return redirect()->route('staff.dashboard')->with('error', 'No active staff assignment found.');
        }
        
        // Get today's attendance
        $todaysAttendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
            ->whereDate('date', today())
            ->first();
        
        // Get attendance stats for current month
        $attendanceStats = $this->getAttendanceStats($staffAssignment->id);
        
        // Get recent attendance records
        $recentAttendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Get pending leave requests
        $pendingLeaveRequests = LeaveRequest::where('staff_assignment_id', $staffAssignment->id)
            ->where('status', 'pending')
            ->get();
        
        // Get upcoming approved leave
        $upcomingLeave = LeaveRequest::where('staff_assignment_id', $staffAssignment->id)
            ->where('start_date', '>=', today())
            ->where('status', 'approved')
            ->get();

        return view('staff.attendance', compact(
            'todaysAttendance',
            'attendanceStats',
            'recentAttendance',
            'pendingLeaveRequests',
            'upcomingLeave'
        ));
    }

    public function checkIn(Request $request)
    {
        try {
            $user = Auth::user();
            $staffAssignment = $user->getActiveStaffAssignments()->first();
            
            if (!$staffAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active staff assignment found.'
                ], 400);
            }
            
            // Check if already checked in today
            $existingAttendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
                ->whereDate('date', today())
                ->first();
                
            if ($existingAttendance && $existingAttendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked in today.'
                ], 400);
            }

            // Prepare location data
            $locationData = null;
            if ($request->has('latitude') && $request->has('longitude')) {
                $locationData = [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => $request->accuracy ?? null,
                    'timestamp' => now()->toISOString(),
                ];
            }

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'staff_assignment_id' => $staffAssignment->id,
                    'date' => today(),
                ],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'check_in_time' => now()->format('H:i:s'),
                    'status' => 'present',
                    'location_data' => $locationData,
                ]
            );

            // Check if late (assuming work starts at 9:00 AM)
            $checkInTime = Carbon::parse($attendance->check_in_time);
            $expectedStartTime = Carbon::parse('09:00:00');
            
            if ($checkInTime->gt($expectedStartTime->addMinutes(15))) {
                $attendance->update(['status' => 'late']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'attendance' => $attendance,
                'is_late' => $attendance->status === 'late'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $user = Auth::user();
            $staffAssignment = $user->getActiveStaffAssignments()->first();
            
            if (!$staffAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active staff assignment found.'
                ], 400);
            }
            
            $attendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
                ->whereDate('date', today())
                ->first();
                
            if (!$attendance || !$attendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'No check-in found for today.'
                ], 400);
            }

            $attendance->update([
                'check_out_time' => now()->format('H:i:s'),
                'notes' => $request->notes,
            ]);

            // Calculate hours worked
            $checkIn = Carbon::parse($attendance->check_in_time);
            $checkOut = Carbon::parse($attendance->check_out_time);
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            
            // Subtract break time for 8+ hour shifts
            if ($hours >= 8) {
                $hours -= 1;
            }
            
            $attendance->update(['hours_worked' => max(0, $hours)]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out successful!',
                'attendance' => $attendance,
                'hours_worked' => $attendance->hours_worked
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createLeaveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|in:sick,personal,vacation,emergency,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $staffAssignment = $user->getActiveStaffAssignments()->first();
            
            if (!$staffAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active staff assignment found.'
                ], 400);
            }
            
            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('leave-attachments', 'public');
                    $attachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
            }

            // Calculate total days
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = 0;
            $current = $startDate->copy();
            
            while ($current->lte($endDate)) {
                // Skip weekends
                if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                    $totalDays++;
                }
                $current->addDay();
            }

            $leaveRequest = LeaveRequest::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'staff_assignment_id' => $staffAssignment->id,
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'attachments' => $attachments,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully!',
                'leave_request' => $leaveRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cancelLeaveRequest(Request $request, $leaveRequestId)
    {
        try {
            $user = Auth::user();
            $staffAssignment = $user->getActiveStaffAssignments()->first();
            
            if (!$staffAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active staff assignment found.'
                ], 400);
            }
            
            $leaveRequest = LeaveRequest::where('staff_assignment_id', $staffAssignment->id)
                ->findOrFail($leaveRequestId);

            if ($leaveRequest->status !== 'pending' || $leaveRequest->start_date <= today()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request cannot be cancelled.'
                ], 400);
            }

            $leaveRequest->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Leave request cancelled successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceHistory(Request $request)
    {
        $user = Auth::user();
        $staffAssignment = $user->getActiveStaffAssignments()->first();
        
        if (!$staffAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'No active staff assignment found.'
            ], 400);
        }
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        $attendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'stats' => $this->getAttendanceStats($staffAssignment->id, $startDate, $endDate)
        ]);
    }

    private function getAttendanceStats($staffAssignmentId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

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
}

