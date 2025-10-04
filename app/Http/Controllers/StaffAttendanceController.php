<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StaffAttendanceController extends Controller
{
    public function __construct()
    {
        // Staff middleware handles authentication and staff verification
    }

    public function index()
    {
        $user = Auth::user();
        
        $attendanceStats = $user->getAttendanceStats();
        $leaveStats = $user->getLeaveStats();
        
        // Get recent attendance records
        $recentAttendance = $user->attendance()
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Get pending leave requests
        $pendingLeaveRequests = $user->getPendingLeaveRequests();
        
        // Get upcoming approved leave
        $upcomingLeave = $user->getUpcomingLeaveRequests();
        
        // Get today's attendance
        $todaysAttendance = $user->getTodaysAttendance();

        return view('staff.attendance', compact(
            'attendanceStats',
            'leaveStats',
            'recentAttendance',
            'pendingLeaveRequests',
            'upcomingLeave',
            'todaysAttendance'
        ));
    }

    public function checkIn(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if already checked in today
            $todaysAttendance = $user->getTodaysAttendance();
            if ($todaysAttendance && $todaysAttendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked in today.'
                ], 400);
            }

            $locationData = null;
            if ($request->has('latitude') && $request->has('longitude')) {
                $locationData = [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => $request->accuracy ?? null,
                    'timestamp' => now()->toISOString(),
                ];
            }

            $attendance = $user->markCheckIn($locationData);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'attendance' => $attendance,
                'is_late' => $attendance->isLate()
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
            
            $attendance = $user->markCheckOut($request->notes);

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

            $leaveRequest = $user->createLeaveRequest(
                $request->leave_type,
                $request->start_date,
                $request->end_date,
                $request->reason,
                $attachments
            );

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
            
            $leaveRequest = LeaveRequest::whereHas('staffAssignment', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->findOrFail($leaveRequestId);

            if (!$leaveRequest->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request cannot be cancelled.'
                ], 400);
            }

            $leaveRequest->cancel();

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
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        $attendance = $user->attendance()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'stats' => $user->getAttendanceStats($startDate, $endDate)
        ]);
    }

    public function getLeaveHistory(Request $request)
    {
        $user = Auth::user();
        
        $year = $request->get('year', now()->year);
        
        $leaveRequests = $user->leaveRequests()
            ->whereYear('start_date', $year)
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'leave_requests' => $leaveRequests,
            'stats' => $user->getLeaveStats($year)
        ]);
    }
}