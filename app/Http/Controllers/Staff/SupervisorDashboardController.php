<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupervisorDashboardController extends Controller
{
    /**
     * Display supervisor dashboard.
     */
    public function index()
    {
        $supervisor = auth()->user()->staffMember;
        
        if (!$supervisor || !$supervisor->isSupervisor()) {
            abort(403, 'Supervisor access required');
        }

        // Get my team
        $myTeam = $supervisor->subordinates()
            ->with('user', 'department')
            ->where('status', 'active')
            ->get();

        // Get tasks for my team
        $teamTasks = Task::whereIn('assigned_to', $myTeam->pluck('id'))
            ->with('assignedStaff.user')
            ->latest('scheduled_at')
            ->limit(10)
            ->get();

        // Get tasks I need to verify
        $tasksToVerify = Task::whereIn('assigned_to', $myTeam->pluck('id'))
            ->where('status', 'completed')
            ->with('assignedStaff.user', 'media')
            ->latest('completed_at')
            ->get();

        // Calculate stats
        $stats = [
            'team_size' => $myTeam->count(),
            'total_tasks' => Task::whereIn('assigned_to', $myTeam->pluck('id'))->count(),
            'pending_verification' => $tasksToVerify->count(),
            'tasks_today' => Task::whereIn('assigned_to', $myTeam->pluck('id'))
                ->whereDate('scheduled_at', today())->count(),
            'overdue_tasks' => Task::whereIn('assigned_to', $myTeam->pluck('id'))
                ->overdue()->count(),
        ];

        return view('staff.supervisor.dashboard', compact('supervisor', 'myTeam', 'teamTasks', 'tasksToVerify', 'stats'));
    }

    /**
     * Display my team members.
     */
    public function myTeam()
    {
        $supervisor = auth()->user()->staffMember;

        $team = $supervisor->subordinates()
            ->with(['user', 'department'])
            ->withCount([
                'assignedTasks',
                'assignedTasks as completed_tasks_count' => fn($q) => $q->whereIn('status', ['completed', 'verified']),
                'assignedTasks as pending_tasks_count' => fn($q) => $q->whereIn('status', ['assigned', 'in_progress']),
            ])
            ->get()
            ->map(function ($staff) {
                $staff->completion_rate = $staff->getTaskCompletionRate(30);
                return $staff;
            });

        return view('staff.supervisor.my-team', compact('team'));
    }

    /**
     * Display tasks.
     */
    public function tasks(Request $request)
    {
        $supervisor = auth()->user()->staffMember;
        $myTeam = $supervisor->subordinates()->pluck('id');

        $tasks = Task::where(function($q) use ($myTeam) {
                $q->whereIn('assigned_to', $myTeam)
                  ->orWhere('assigned_by', auth()->user()->staffMember->id);
            })
            ->with(['assignedStaff.user', 'department', 'creator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->assigned_to, fn($q) => $q->where('assigned_to', $request->assigned_to))
            ->latest('scheduled_at')
            ->paginate(20);

        return view('staff.supervisor.tasks', compact('tasks', 'myTeam'));
    }

    /**
     * Assign task to a staff member.
     */
    public function assignTask(Request $request, Task $task)
    {
        $this->authorize('assign', $task);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:staff_members,id',
        ]);

        $supervisor = auth()->user()->staffMember;

        // Verify the staff member is in supervisor's team
        if (!$supervisor->subordinates()->where('id', $validated['assigned_to'])->exists()) {
            abort(403, 'You can only assign tasks to your team members.');
        }

        $oldStatus = $task->status;
        $task->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => $supervisor->id,
            'status' => 'assigned',
        ]);

        // Log the assignment
        $task->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $supervisor->id,
            'action' => 'assigned',
            'from_status' => $oldStatus,
            'to_status' => 'assigned',
            'metadata' => ['assigned_to' => $validated['assigned_to']],
            'performed_at' => now(),
        ]);

        // Notify staff member
        \App\Models\StaffNotification::create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $validated['assigned_to'],
            'from_user_id' => auth()->id(),
            'task_id' => $task->id,
            'type' => 'task_assigned',
            'title' => 'New Task Assigned',
            'message' => "You have been assigned a new task: {$task->title}",
            'priority' => $task->priority === 'urgent' ? 'urgent' : 'medium',
        ]);

        return back()->with('success', 'Task assigned successfully!');
    }

    /**
     * Verify a completed task.
     */
    public function verifyTask(Request $request, Task $task)
    {
        $this->authorize('verify', $task);

        $validated = $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $task->verify(
            auth()->user()->staffMember->id,
            $validated['verification_notes'] ?? null
        );

        return back()->with('success', 'Task verified successfully!');
    }

    /**
     * Reject a completed task.
     */
    public function rejectTask(Request $request, Task $task)
    {
        $this->authorize('reject', $task);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $task->reject(
            auth()->user()->staffMember->id,
            $validated['rejection_reason']
        );

        return back()->with('success', 'Task rejected. Staff member has been notified to rework.');
    }
}
