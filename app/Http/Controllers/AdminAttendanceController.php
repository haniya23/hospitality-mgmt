<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['staffAssignment.user', 'staffAssignment.property'])
            ->orderBy('date', 'desc');

        // Filters
        if ($request->property_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        if ($request->staff_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('user_id', $request->staff_id);
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendance = $query->paginate(20);
        
        // Get filter options
        $properties = Property::where('owner_id', Auth::id())->get();
        $staff = User::whereHas('staffAssignments', function($q) {
            $q->whereHas('property', function($p) {
                $p->where('owner_id', Auth::id());
            });
        })->get();

        // Get statistics
        $stats = $this->getAttendanceStats($request);

        return view('admin.attendance.index', compact(
            'attendance',
            'properties',
            'staff',
            'stats'
        ));
    }

    public function staffAttendance($staffUuid)
    {
        $staff = User::where('uuid', $staffUuid)->firstOrFail();
        
        // Verify staff belongs to owner's properties
        $staffAssignments = $staff->staffAssignments()
            ->whereHas('property', function($q) {
                $q->where('owner_id', Auth::id());
            })
            ->get();

        if ($staffAssignments->isEmpty()) {
            abort(403, 'You are not authorized to view this staff member\'s attendance.');
        }

        $attendanceStats = $staff->getAttendanceStats();
        $leaveStats = $staff->getLeaveStats();
        
        $recentAttendance = $staff->attendance()
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        $leaveRequests = $staff->leaveRequests()
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.attendance.staff', compact(
            'staff',
            'attendanceStats',
            'leaveStats',
            'recentAttendance',
            'leaveRequests'
        ));
    }

    public function leaveRequests(Request $request)
    {
        $query = LeaveRequest::with(['staffAssignment.user', 'staffAssignment.property', 'approver'])
            ->whereHas('staffAssignment.property', function($q) {
                $q->where('owner_id', Auth::id());
            })
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->property_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        if ($request->staff_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('user_id', $request->staff_id);
            });
        }

        $leaveRequests = $query->paginate(20);
        
        // Get filter options
        $properties = Property::where('owner_id', Auth::id())->get();
        $staff = User::whereHas('staffAssignments', function($q) {
            $q->whereHas('property', function($p) {
                $p->where('owner_id', Auth::id());
            });
        })->get();

        // Get statistics
        $stats = $this->getLeaveRequestStats($request);

        return view('admin.attendance.leave-requests', compact(
            'leaveRequests',
            'properties',
            'staff',
            'stats'
        ));
    }

    public function approveLeaveRequest(Request $request, $leaveRequestId)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $leaveRequest = LeaveRequest::whereHas('staffAssignment.property', function($q) {
                $q->where('owner_id', Auth::id());
            })->findOrFail($leaveRequestId);

            if (!$leaveRequest->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request cannot be approved.'
                ], 400);
            }

            $leaveRequest->approve(Auth::id(), $request->admin_notes);

            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully!',
                'leave_request' => $leaveRequest->fresh()
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
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $leaveRequest = LeaveRequest::whereHas('staffAssignment.property', function($q) {
                $q->where('owner_id', Auth::id());
            })->findOrFail($leaveRequestId);

            if (!$leaveRequest->canBeRejected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request cannot be rejected.'
                ], 400);
            }

            $leaveRequest->reject(Auth::id(), $request->admin_notes);

            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected.',
                'leave_request' => $leaveRequest->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAttendance(Request $request, $attendanceId)
    {
        $validator = Validator::make($request->all(), [
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $attendance = Attendance::whereHas('staffAssignment.property', function($q) {
                $q->where('owner_id', Auth::id());
            })->findOrFail($attendanceId);

            $attendance->update([
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            if ($request->check_in_time && $request->check_out_time) {
                $attendance->calculateHoursWorked();
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully!',
                'attendance' => $attendance->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getAttendanceStats($request)
    {
        $query = Attendance::whereHas('staffAssignment.property', function($q) {
            $q->where('owner_id', Auth::id());
        });

        // Apply same filters as main query
        if ($request->property_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        if ($request->staff_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('user_id', $request->staff_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendance = $query->get();

        return [
            'total_records' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'half_days' => $attendance->where('status', 'half_day')->count(),
            'leave_days' => $attendance->where('status', 'leave')->count(),
            'total_hours' => $attendance->sum('hours_worked'),
            'average_hours_per_day' => $attendance->where('status', 'present')->avg('hours_worked') ?? 0,
        ];
    }

    private function getLeaveRequestStats($request)
    {
        $query = LeaveRequest::whereHas('staffAssignment.property', function($q) {
            $q->where('owner_id', Auth::id());
        });

        // Apply same filters as main query
        if ($request->property_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        if ($request->staff_id) {
            $query->whereHas('staffAssignment', function($q) use ($request) {
                $q->where('user_id', $request->staff_id);
            });
        }

        $leaveRequests = $query->get();

        return [
            'total_requests' => $leaveRequests->count(),
            'pending_requests' => $leaveRequests->where('status', 'pending')->count(),
            'approved_requests' => $leaveRequests->where('status', 'approved')->count(),
            'rejected_requests' => $leaveRequests->where('status', 'rejected')->count(),
            'total_days_requested' => $leaveRequests->sum('total_days'),
            'total_days_approved' => $leaveRequests->where('status', 'approved')->sum('total_days'),
        ];
    }
}