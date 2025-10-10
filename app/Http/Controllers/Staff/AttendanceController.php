<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveRequest;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Display attendance page for staff member.
     */
    public function index()
    {
        $staff = auth()->user()->staffMember;

        // Get this month's attendance
        $attendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->thisMonth()
            ->orderBy('date', 'desc')
            ->get();

        // Get today's attendance
        $todayAttendance = $attendance->where('date', today())->first();

        // Calculate monthly stats
        $stats = [
            'present_days' => $attendance->where('status', 'present')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'total_hours' => $attendance->sum('hours_worked'),
            'average_hours' => $attendance->where('hours_worked', '>', 0)->avg('hours_worked'),
        ];

        return view('staff.employee.attendance', compact('attendance', 'todayAttendance', 'stats'));
    }

    /**
     * Check in.
     */
    public function checkIn(Request $request)
    {
        $staff = auth()->user()->staffMember;

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Check if already checked in today
        $existing = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->first();

        if ($existing && $existing->check_in_time) {
            return back()->with('error', 'You have already checked in today.');
        }

        $locationData = null;
        if (isset($validated['latitude']) && isset($validated['longitude'])) {
            $locationData = [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'checked_at' => now()->toDateTimeString(),
            ];
        }

        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_member_id' => $staff->id,
                'date' => today(),
            ],
            [
                'uuid' => Str::uuid(),
                'check_in_time' => now()->format('H:i'),
                'status' => 'present',
                'location_data' => $locationData,
            ]
        );

        // Check if late
        if ($attendance->isLate()) {
            $attendance->update(['status' => 'late']);
        }

        return back()->with('success', 'Checked in successfully at ' . now()->format('h:i A'));
    }

    /**
     * Check out.
     */
    public function checkOut(Request $request)
    {
        $staff = auth()->user()->staffMember;

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'You must check in first before checking out.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'You have already checked out today.');
        }

        $attendance->update([
            'check_out_time' => now()->format('H:i'),
            'notes' => $validated['notes'] ?? null,
        ]);

        $attendance->calculateHoursWorked();

        return back()->with('success', 'Checked out successfully at ' . now()->format('h:i A') . '. Total hours: ' . number_format($attendance->hours_worked, 2));
    }

    /**
     * Display leave requests for staff member.
     */
    public function leaveRequests()
    {
        $staff = auth()->user()->staffMember;

        $leaveRequests = StaffLeaveRequest::where('staff_member_id', $staff->id)
            ->with('reviewer')
            ->latest()
            ->get();

        $stats = [
            'pending' => $leaveRequests->where('status', 'pending')->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            'total_days_approved' => $leaveRequests->where('status', 'approved')->sum('total_days'),
        ];

        return view('staff.employee.leave-requests', compact('leaveRequests', 'stats'));
    }

    /**
     * Store a new leave request.
     */
    public function storeLeaveRequest(Request $request)
    {
        $staff = auth()->user()->staffMember;

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,vacation,personal,emergency,maternity,paternity,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->extension();
                $path = $file->storeAs('leave-attachments', $filename, 'public');
                $attachments[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                ];
            }
        }

        $leaveRequest = StaffLeaveRequest::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staff->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachments' => $attachments,
            'status' => 'pending',
        ]);

        $leaveRequest->calculateTotalDays();

        // Notify supervisor
        if ($staff->supervisor) {
            \App\Models\StaffNotification::create([
                'uuid' => Str::uuid(),
                'staff_member_id' => $staff->supervisor->id,
                'from_user_id' => auth()->id(),
                'type' => 'leave_request',
                'title' => 'New Leave Request',
                'message' => "{$staff->getFullName()} has requested leave from {$leaveRequest->start_date->format('M d')} to {$leaveRequest->end_date->format('M d')}",
                'priority' => 'medium',
            ]);
        }

        return back()->with('success', 'Leave request submitted successfully!');
    }

    /**
     * Display attendance management page for supervisors/managers.
     */
    public function management()
    {
        $user = auth()->user();
        $staffMember = $user->staffMember;

        // Get staff members based on role
        if ($staffMember->isManager()) {
            $staff = StaffMember::where('property_id', $staffMember->property_id)
                ->with('user', 'department')
                ->get();
        } else {
            $staff = $staffMember->subordinates()->with('user', 'department')->get();
        }

        // Get today's attendance
        $todayAttendance = StaffAttendance::whereIn('staff_member_id', $staff->pluck('id'))
            ->whereDate('date', today())
            ->with('staffMember.user')
            ->get();

        // Get pending leave requests
        $pendingLeaves = StaffLeaveRequest::whereIn('staff_member_id', $staff->pluck('id'))
            ->pending()
            ->with('staffMember.user')
            ->latest()
            ->get();

        return view('staff.attendance.management', compact('staff', 'todayAttendance', 'pendingLeaves'));
    }

    /**
     * Display individual staff attendance.
     */
    public function staffAttendance(StaffMember $staffMember)
    {
        $this->authorize('manageAttendance', $staffMember);

        $attendance = StaffAttendance::where('staff_member_id', $staffMember->id)
            ->thisMonth()
            ->orderBy('date', 'desc')
            ->get();

        $leaveRequests = StaffLeaveRequest::where('staff_member_id', $staffMember->id)
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'present_days' => $attendance->where('status', 'present')->count(),
            'late_days' => $attendance->where('status', 'late')->count(),
            'absent_days' => $attendance->where('status', 'absent')->count(),
            'total_hours' => $attendance->sum('hours_worked'),
        ];

        return view('staff.attendance.staff-detail', compact('staffMember', 'attendance', 'leaveRequests', 'stats'));
    }

    /**
     * Approve a leave request.
     */
    public function approveLeave(Request $request, StaffLeaveRequest $leaveRequest)
    {
        $staffMember = auth()->user()->staffMember;
        $this->authorize('reviewLeaveRequests', $leaveRequest->staffMember);

        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $leaveRequest->approve(auth()->id(), $validated['review_notes'] ?? null);

        return back()->with('success', 'Leave request approved successfully!');
    }

    /**
     * Reject a leave request.
     */
    public function rejectLeave(Request $request, StaffLeaveRequest $leaveRequest)
    {
        $staffMember = auth()->user()->staffMember;
        $this->authorize('reviewLeaveRequests', $leaveRequest->staffMember);

        $validated = $request->validate([
            'review_notes' => 'required|string|max:500',
        ]);

        $leaveRequest->reject(auth()->id(), $validated['review_notes']);

        return back()->with('success', 'Leave request rejected.');
    }
}
