<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\StaffMember;
use App\Models\StaffDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $propertyIds = $user->properties()->pluck('id');

        $tasks = Task::whereIn('property_id', $propertyIds)
            ->with(['assignedStaff.user', 'assignedBy.user', 'department', 'property'])
            ->when($request->property_id, fn($q) => $q->where('property_id', $request->property_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->latest()
            ->paginate(20);

        $properties = $user->properties;

        return view('owner.tasks.index', compact('tasks', 'properties'));
    }

    /**
     * Show the form for creating a new task
     */
    public function create()
    {
        $user = auth()->user();
        $properties = $user->properties;
        $departments = StaffDepartment::active()->get();
        
        // Get all staff from owner's properties
        $staffMembers = StaffMember::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'active')
            ->with(['user', 'property', 'department'])
            ->orderBy('property_id')
            ->orderBy('staff_role')
            ->get();

        return view('owner.tasks.create', compact('properties', 'departments', 'staffMembers'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'department_id' => 'nullable|exists:staff_departments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_type' => 'required|in:cleaning,maintenance,inspection,delivery,customer_service,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'required|exists:staff_members,id',
            'scheduled_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:scheduled_at',
            'location' => 'nullable|string|max:255',
            'requires_photo_proof' => 'boolean',
        ]);

        // Verify property ownership
        $property = auth()->user()->properties()->findOrFail($validated['property_id']);

        // Verify staff belongs to the property
        $staff = StaffMember::where('id', $validated['assigned_to'])
            ->where('property_id', $property->id)
            ->firstOrFail();

        $task = Task::create([
            'uuid' => Str::uuid(),
            'property_id' => $validated['property_id'],
            'department_id' => $validated['department_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'task_type' => $validated['task_type'],
            'priority' => $validated['priority'],
            'status' => 'assigned',
            'created_by' => auth()->id(),
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => null, // Owner directly assigned (no staff member)
            'scheduled_at' => $validated['scheduled_at'],
            'due_at' => $validated['due_at'],
            'location' => $validated['location'],
            'requires_photo_proof' => $request->boolean('requires_photo_proof'),
        ]);

        return redirect()->route('owner.tasks.show', $task)
            ->with('success', 'Task assigned successfully to ' . $staff->user->name);
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        // Verify owner can access this task
        $propertyIds = auth()->user()->properties()->pluck('id');
        
        if (!$propertyIds->contains($task->property_id)) {
            abort(403, 'This task does not belong to your properties.');
        }

        $task->load([
            'property',
            'department',
            'assignedStaff.user',
            'assignedBy.user',
            'creator',
            'media',
            'logs.staffMember.user',
            'logs.user'
        ]);

        return view('owner.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the task
     */
    public function edit(Task $task)
    {
        // Verify owner can access this task
        $propertyIds = auth()->user()->properties()->pluck('id');
        
        if (!$propertyIds->contains($task->property_id)) {
            abort(403, 'This task does not belong to your properties.');
        }

        $user = auth()->user();
        $properties = $user->properties;
        $departments = StaffDepartment::active()->get();
        
        $staffMembers = StaffMember::where('property_id', $task->property_id)
            ->where('status', 'active')
            ->with(['user', 'property', 'department'])
            ->get();

        return view('owner.tasks.edit', compact('task', 'properties', 'departments', 'staffMembers'));
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task)
    {
        // Verify owner can access this task
        $propertyIds = auth()->user()->properties()->pluck('id');
        
        if (!$propertyIds->contains($task->property_id)) {
            abort(403, 'This task does not belong to your properties.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_type' => 'required|in:cleaning,maintenance,inspection,delivery,customer_service,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'required|exists:staff_members,id',
            'scheduled_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:scheduled_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:assigned,in_progress,completed,verified,cancelled',
        ]);

        $task->update($validated);

        return redirect()->route('owner.tasks.show', $task)
            ->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task
     */
    public function destroy(Task $task)
    {
        // Verify owner can access this task
        $propertyIds = auth()->user()->properties()->pluck('id');
        
        if (!$propertyIds->contains($task->property_id)) {
            abort(403, 'This task does not belong to your properties.');
        }

        $task->delete();

        return redirect()->route('owner.tasks.index')
            ->with('success', 'Task deleted successfully');
    }
}

