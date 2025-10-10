<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\Task;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    /**
     * Display manager dashboard.
     */
    public function index()
    {
        $manager = auth()->user()->staffMember;
        
        if (!$manager || !$manager->isManager()) {
            abort(403, 'Manager access required');
        }

        // Get all staff in this property
        $allStaff = StaffMember::where('property_id', $manager->property_id)
            ->with('user', 'department')
            ->get();

        // Get supervisors
        $supervisors = $allStaff->where('staff_role', 'supervisor');
        
        // Get regular staff
        $staff = $allStaff->where('staff_role', 'staff');

        // Get tasks
        $tasks = Task::where('property_id', $manager->property_id)
            ->with('assignedStaff.user', 'department')
            ->latest()
            ->limit(10)
            ->get();

        // Calculate stats
        $stats = [
            'total_staff' => $allStaff->where('status', 'active')->count(),
            'supervisors_count' => $supervisors->where('status', 'active')->count(),
            'staff_count' => $staff->where('status', 'active')->count(),
            'total_tasks' => Task::where('property_id', $manager->property_id)->count(),
            'pending_tasks' => Task::where('property_id', $manager->property_id)
                ->whereIn('status', ['pending', 'assigned', 'in_progress'])->count(),
            'completed_tasks' => Task::where('property_id', $manager->property_id)
                ->whereIn('status', ['completed', 'verified'])->count(),
            'tasks_today' => Task::where('property_id', $manager->property_id)
                ->whereDate('scheduled_at', today())->count(),
        ];

        return view('staff.manager.dashboard', compact('manager', 'supervisors', 'staff', 'tasks', 'stats'));
    }

    /**
     * Display supervisors list.
     */
    public function supervisors()
    {
        $manager = auth()->user()->staffMember;

        $supervisors = StaffMember::where('property_id', $manager->property_id)
            ->supervisors()
            ->with(['user', 'department', 'subordinates.user'])
            ->withCount(['assignedTasks', 'delegatedTasks'])
            ->get();

        return view('staff.manager.supervisors', compact('supervisors'));
    }

    /**
     * Display all tasks.
     */
    public function tasks(Request $request)
    {
        $manager = auth()->user()->staffMember;

        $tasks = Task::where('property_id', $manager->property_id)
            ->with(['assignedStaff.user', 'assignedBy.user', 'department', 'creator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->assigned_to, fn($q) => $q->where('assigned_to', $request->assigned_to))
            ->latest('scheduled_at')
            ->paginate(20);

        return view('staff.manager.tasks', compact('tasks'));
    }

    /**
     * Display analytics and reports.
     */
    public function analytics()
    {
        $manager = auth()->user()->staffMember;

        // Task completion analytics
        $taskStats = [
            'total' => Task::where('property_id', $manager->property_id)->count(),
            'completed' => Task::where('property_id', $manager->property_id)->whereIn('status', ['completed', 'verified'])->count(),
            'pending' => Task::where('property_id', $manager->property_id)->whereIn('status', ['pending', 'assigned'])->count(),
            'in_progress' => Task::where('property_id', $manager->property_id)->where('status', 'in_progress')->count(),
            'overdue' => Task::where('property_id', $manager->property_id)->overdue()->count(),
        ];

        // Staff performance
        $staffPerformance = StaffMember::where('property_id', $manager->property_id)
            ->where('staff_role', 'staff')
            ->with('user')
            ->get()
            ->map(function ($staff) {
                return [
                    'staff' => $staff,
                    'completion_rate' => $staff->getTaskCompletionRate(30),
                    'total_tasks' => $staff->assignedTasks()->count(),
                    'completed_tasks' => $staff->assignedTasks()->whereIn('status', ['completed', 'verified'])->count(),
                ];
            })
            ->sortByDesc('completion_rate');

        // Department breakdown
        $departmentStats = Task::where('property_id', $manager->property_id)
            ->selectRaw('department_id, count(*) as total, 
                SUM(CASE WHEN status IN ("completed", "verified") THEN 1 ELSE 0 END) as completed')
            ->groupBy('department_id')
            ->with('department')
            ->get();

        return view('staff.manager.analytics', compact('taskStats', 'staffPerformance', 'departmentStats'));
    }
}
