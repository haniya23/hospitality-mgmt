<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffDashboardController extends Controller
{
    /**
     * Display staff dashboard.
     */
    public function index()
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff) {
            abort(403, 'Staff access required');
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
        $todayAttendance = \App\Models\StaffAttendance::where('staff_member_id', $staff->id)
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

        return view('staff.employee.dashboard', compact(
            'staff', 
            'myTasks', 
            'todaysTasks', 
            'overdueTasks', 
            'recentCompleted',
            'todayAttendance',
            'stats'
        ));
    }

    /**
     * Display my tasks list.
     */
    public function myTasks(Request $request)
    {
        $staff = auth()->user()->staffMember;

        $tasks = $staff->assignedTasks()
            ->with('department', 'assignedBy.user', 'media')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_at')
            ->paginate(20);

        return view('staff.employee.my-tasks', compact('tasks'));
    }

    /**
     * Show task details.
     */
    public function showTask(Task $task)
    {
        $this->authorize('view', $task);

        // Verify this task is assigned to the current staff member
        if ($task->assigned_to !== auth()->user()->staffMember->id) {
            abort(403, 'This task is not assigned to you.');
        }

        $task->load([
            'department',
            'assignedBy.user',
            'creator',
            'media',
            'logs.staffMember.user',
            'logs.user'
        ]);

        return view('staff.employee.task-detail', compact('task'));
    }

    /**
     * Start a task.
     */
    public function startTask(Task $task)
    {
        $this->authorize('start', $task);

        $task->start(auth()->user()->staffMember->id);

        return back()->with('success', 'Task started! Good luck!');
    }

    /**
     * Complete a task.
     */
    public function completeTask(Request $request, Task $task)
    {
        $this->authorize('complete', $task);

        $validated = $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $task->complete(
            auth()->user()->staffMember->id,
            $validated['completion_notes'] ?? null
        );

        return back()->with('success', 'Task completed! Waiting for verification.');
    }

    /**
     * Upload proof photos for a task.
     */
    public function uploadProof(Request $request, Task $task)
    {
        $this->authorize('uploadMedia', $task);

        $validated = $request->validate([
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'caption' => 'nullable|string|max:255',
            'media_type' => 'required|in:proof,before,after,issue',
        ]);

        $uploadedCount = 0;

        foreach ($validated['photos'] as $photo) {
            $filename = Str::uuid() . '.' . $photo->extension();
            $path = $photo->storeAs('tasks/' . $task->id, $filename, 'public');

            TaskMedia::create([
                'uuid' => Str::uuid(),
                'task_id' => $task->id,
                'uploaded_by' => auth()->id(),
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_type' => 'image',
                'mime_type' => $photo->getMimeType(),
                'file_size' => $photo->getSize(),
                'media_type' => $validated['media_type'],
                'caption' => $validated['caption'] ?? null,
            ]);

            $uploadedCount++;
        }

        // Log the upload
        $task->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => auth()->user()->staffMember->id,
            'action' => 'commented',
            'notes' => "Uploaded {$uploadedCount} proof photo(s)",
            'metadata' => ['media_count' => $uploadedCount],
            'performed_at' => now(),
        ]);

        return back()->with('success', "{$uploadedCount} photo(s) uploaded successfully!");
    }
}
