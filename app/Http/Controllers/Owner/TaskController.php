<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Task;
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
            ->with(['property'])
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

        return view('owner.tasks.create', compact('properties'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_type' => 'required|in:cleaning,maintenance,inspection,delivery,customer_service,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:scheduled_at',
            'location' => 'nullable|string|max:255',
            'requires_photo_proof' => 'boolean',
        ]);

        // Verify property ownership
        $property = auth()->user()->properties()->findOrFail($validated['property_id']);

        $task = Task::create([
            'uuid' => Str::uuid(),
            'property_id' => $validated['property_id'],
            'department_id' => null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'task_type' => $validated['task_type'],
            'priority' => $validated['priority'],
            'status' => 'pending',
            'created_by' => auth()->id(),
            'assigned_to' => null,
            'assigned_by' => null,
            'scheduled_at' => $validated['scheduled_at'],
            'due_at' => $validated['due_at'],
            'location' => $validated['location'],
            'requires_photo_proof' => $request->boolean('requires_photo_proof'),
        ]);

        return redirect()->route('owner.tasks.show', $task)
            ->with('success', 'Task created successfully.');
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
            'creator',
            'media',
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

        return view('owner.tasks.edit', compact('task', 'properties'));
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
            'task_type' => 'required|in:cleaning,maintenance,indigo,delivery,customer_service,administrative,other', // Note: we allow task types
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:scheduled_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        // Let's make sure task_type matches original task types
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'task_type' => 'required|in:cleaning,maintenance,inspection,delivery,customer_service,administrative,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:scheduled_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
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
