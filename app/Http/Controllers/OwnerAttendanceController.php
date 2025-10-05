<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get staff assignments for the owner's properties
        $staffAssignments = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
        ->with(['user', 'property'])
        ->get();

        $query = Attendance::with(['staffAssignment.user', 'staffAssignment.property'])
            ->whereHas('staffAssignment', function($q) use ($user) {
                $q->whereHas('property', function($propertyQuery) use ($user) {
                    $propertyQuery->where('owner_id', $user->id);
                });
            })
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

        // Get properties for filter dropdown
        $properties = $user->properties()->where('status', 'active')->get();

        // Get staff for filter dropdown
        $staff = $staffAssignments->pluck('user')->unique('id')->values();

        return view('owner.attendance.index', compact('attendance', 'properties', 'staff'));
    }

    public function staffAttendance($staffUuid)
    {
        $user = Auth::user();
        
        $staffAssignment = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
        ->where('uuid', $staffUuid)
        ->with(['user', 'property'])
        ->firstOrFail();

        $attendance = Attendance::where('staff_assignment_id', $staffAssignment->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('owner.attendance.staff', compact('staffAssignment', 'attendance'));
    }

    public function getAttendance($attendanceId)
    {
        $user = Auth::user();
        
        $attendance = Attendance::whereHas('staffAssignment', function($q) use ($user) {
            $q->whereHas('property', function($propertyQuery) use ($user) {
                $propertyQuery->where('owner_id', $user->id);
            });
        })
        ->findOrFail($attendanceId);

        return response()->json([
            'id' => $attendance->id,
            'status' => $attendance->status,
            'check_in_time' => $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '',
            'check_out_time' => $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '',
            'notes' => $attendance->notes,
        ]);
    }

    public function updateAttendance(Request $request, $attendanceId)
    {
        $user = Auth::user();
        
        $attendance = Attendance::whereHas('staffAssignment', function($q) use ($user) {
            $q->whereHas('property', function($propertyQuery) use ($user) {
                $propertyQuery->where('owner_id', $user->id);
            });
        })
        ->findOrFail($attendanceId);

        $request->validate([
            'status' => 'required|in:present,absent,late,partial',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'notes' => $request->notes,
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully'
        ]);
    }

    public function leaveRequests(Request $request)
    {
        $user = Auth::user();
        
        $query = LeaveRequest::with(['staffAssignment.user', 'staffAssignment.property'])
            ->whereHas('staffAssignment', function($q) use ($user) {
                $q->whereHas('property', function($propertyQuery) use ($user) {
                    $propertyQuery->where('owner_id', $user->id);
                });
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

        // Get staff assignments for the owner's properties
        $staffAssignments = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
        ->with(['user', 'property'])
        ->get();

        // Get properties for filter dropdown
        $properties = $user->properties()->where('status', 'active')->get();

        // Get staff for filter dropdown
        $staff = $staffAssignments->pluck('user')->unique('id')->values();

        return view('owner.leave-requests.index', compact('leaveRequests', 'properties', 'staff'));
    }

    public function approveLeaveRequest(Request $request, $leaveRequestId)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::whereHas('staffAssignment', function($q) use ($user) {
            $q->whereHas('property', function($propertyQuery) use ($user) {
                $propertyQuery->where('owner_id', $user->id);
            });
        })
        ->findOrFail($leaveRequestId);

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully'
        ]);
    }

    public function rejectLeaveRequest(Request $request, $leaveRequestId)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::whereHas('staffAssignment', function($q) use ($user) {
            $q->whereHas('property', function($propertyQuery) use ($user) {
                $propertyQuery->where('owner_id', $user->id);
            });
        })
        ->findOrFail($leaveRequestId);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected successfully'
        ]);
    }
}