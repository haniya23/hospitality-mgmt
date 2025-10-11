<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get staff dashboard data
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $staff = $user->staffMember;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        // Get my tasks
        $myTasks = $staff->assignedTasks()
            ->with('department', 'assignedBy.user')
            ->whereIn('status', ['assigned', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->orderBy('due_at')
            ->get();

        // Get today's tasks
        $todaysTasks = $staff->getTodaysTasks();

        // Get overdue tasks
        $overdueTasks = $staff->getOverdueTasks();

        // Get recent completed tasks
        $recentCompleted = $staff->assignedTasks()
            ->whereIn('status', ['completed', 'verified'])
            ->latest('completed_at')
            ->limit(5)
            ->get();

        // Get today's attendance
        $todayAttendance = StaffAttendance::where('staff_member_id', $staff->id)
            ->whereDate('date', today())
            ->first();

        // Calculate stats
        $stats = [
            'total_tasks' => $staff->assignedTasks()->count(),
            'pending_tasks' => $myTasks->count(),
            'completed_today' => $staff->assignedTasks()
                ->whereDate('completed_at', today())
                ->count(),
            'overdue_count' => $overdueTasks->count(),
            'completion_rate' => $staff->getTaskCompletionRate(30),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'my_tasks' => $myTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'uuid' => $task->uuid,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'due_at' => $task->due_at?->toIso8601String(),
                        'scheduled_at' => $task->scheduled_at?->toIso8601String(),
                        'assigned_by' => [
                            'name' => $task->assignedBy->user->name,
                            'job_title' => $task->assignedBy->job_title,
                        ],
                    ];
                }),
                'todays_tasks' => $todaysTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'uuid' => $task->uuid,
                        'title' => $task->title,
                        'status' => $task->status,
                        'priority' => $task->priority,
                    ];
                }),
                'overdue_tasks' => $overdueTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'uuid' => $task->uuid,
                        'title' => $task->title,
                        'due_at' => $task->due_at?->toIso8601String(),
                        'priority' => $task->priority,
                    ];
                }),
                'recent_completed' => $recentCompleted->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'uuid' => $task->uuid,
                        'title' => $task->title,
                        'completed_at' => $task->completed_at?->toIso8601String(),
                    ];
                }),
                'attendance' => $todayAttendance ? [
                    'id' => $todayAttendance->id,
                    'date' => $todayAttendance->date->format('Y-m-d'),
                    'check_in_time' => $todayAttendance->check_in_time,
                    'check_out_time' => $todayAttendance->check_out_time,
                    'status' => $todayAttendance->status,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Get staff performance metrics
     */
    public function performance(Request $request)
    {
        $user = $request->user();
        $staff = $user->staffMember;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        $days = $request->input('days', 30);

        $metrics = [
            'completion_rate' => $staff->getTaskCompletionRate($days),
            'total_tasks' => $staff->assignedTasks()
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'completed_tasks' => $staff->assignedTasks()
                ->where('created_at', '>=', now()->subDays($days))
                ->whereIn('status', ['completed', 'verified'])
                ->count(),
            'overdue_tasks' => $staff->assignedTasks()
                ->where('due_at', '<', now())
                ->whereNotIn('status', ['completed', 'verified', 'cancelled'])
                ->count(),
            'attendance_rate' => $this->calculateAttendanceRate($staff, $days),
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ], 200);
    }

    /**
     * Calculate attendance rate
     */
    private function calculateAttendanceRate($staff, $days)
    {
        $workingDays = $days; // Simplified - should exclude weekends/holidays
        $attended = StaffAttendance::where('staff_member_id', $staff->id)
            ->where('date', '>=', now()->subDays($days))
            ->whereIn('status', ['present', 'late'])
            ->count();

        return $workingDays > 0 ? round(($attended / $workingDays) * 100, 2) : 0;
    }
}

