<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\StaffDepartment;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $staffMember = $user->staffMember;

        // Determine accessible property
        $propertyId = $user->isOwner() 
            ? $request->property_id ?? $user->properties->first()?->id
            : $staffMember->property_id;

        if (!$propertyId) {
            return redirect()->back()->with('error', 'No property found.');
        }

        $tasks = Task::where('property_id', $propertyId)
            ->with(['assignedStaff.user', 'assignedBy.user', 'department', 'creator'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->assigned_to, fn($q) => $q->where('assigned_to', $request->assigned_to))
            ->latest('scheduled_at')
            ->paginate(20);

        $departments = StaffDepartment::active()->get();
        $staff = StaffMember::where('property_id', $propertyId)
            ->where('staff_role', 'staff')
            ->with('user')
            ->get();

        return view('staff.tasks.index', compact('tasks', 'departments', 'staff'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $user = auth()->user();
        $staffMember = $user->staffMember;

        $propertyId = $user->isOwner()
            ? $user->properties->first()?->id
            : $staffMember->property_id;

        $departments = StaffDepartment::active()->get();
        
        // Get staff members who can be assigned tasks
        $availableStaff = StaffMember::where('property_id', $propertyId)
            ->where('staff_role', 'staff')
            ->where('status', 'active')
            ->with('user', 'department')
            ->get();

        return view('staff.tasks.create', compact('departments', 'availableStaff'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $staffMember = $user->staffMember;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_type' => 'required|in:cleaning,maintenance,guest_service,inspection,delivery,setup,inventory,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'department_id' => 'nullable|exists:staff_departments,id',
            'assigned_to' => 'nullable|exists:staff_members,id',
            'scheduled_at' => 'required|date',
            'due_at' => 'nullable|date|after:scheduled_at',
            'location' => 'nullable|string|max:255',
            'requires_photo_proof' => 'boolean',
            'checklist_items' => 'nullable|array',
        ]);

        $propertyId = $user->isOwner()
            ? $user->properties->first()->id
            : $staffMember->property_id;

        $task = Task::create([
            'uuid' => Str::uuid(),
            'property_id' => $propertyId,
            'department_id' => $validated['department_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'task_type' => $validated['task_type'],
            'priority' => $validated['priority'],
            'status' => $validated['assigned_to'] ? 'assigned' : 'pending',
            'created_by' => $user->id,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => $staffMember?->id,
            'scheduled_at' => $validated['scheduled_at'],
            'due_at' => $validated['due_at'],
            'location' => $validated['location'],
            'requires_photo_proof' => $validated['requires_photo_proof'] ?? false,
            'checklist_items' => $validated['checklist_items'],
        ]);

        // If assigned, notify the staff member
        if ($task->assigned_to) {
            \App\Models\StaffNotification::create([
                'uuid' => Str::uuid(),
                'staff_member_id' => $task->assigned_to,
                'from_user_id' => $user->id,
                'task_id' => $task->id,
                'type' => 'task_assigned',
                'title' => 'New Task Assigned',
                'message' => "You have been assigned a new task: {$task->title}",
                'priority' => $task->priority === 'urgent' ? 'urgent' : 'medium',
            ]);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully!');
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_type' => 'required|in:cleaning,maintenance,guest_service,inspection,delivery,setup,inventory,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'department_id' => 'nullable|exists:staff_departments,id',
            'scheduled_at' => 'required|date',
            'due_at' => 'nullable|date|after:scheduled_at',
            'location' => 'nullable|string|max:255',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully!');
    }
}
