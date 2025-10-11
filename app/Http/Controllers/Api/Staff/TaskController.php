<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Get all tasks for authenticated staff
     */
    public function index(Request $request)
    {
        $staff = $request->user()->staffMember;

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        $query = $staff->assignedTasks()
            ->with('department', 'assignedBy.user', 'media');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $tasks = $query->orderBy('priority', 'desc')
            ->orderBy('scheduled_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => $tasks->map(function ($task) {
                    return $this->formatTask($task);
                }),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Get specific task details
     */
    public function show(Request $request, $uuid)
    {
        $staff = $request->user()->staffMember;

        $task = Task::where('uuid', $uuid)
            ->where('assigned_to', $staff->id)
            ->with([
                'department',
                'assignedBy.user',
                'media',
                'logs.staffMember.user',
            ])
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatTaskDetailed($task),
        ], 200);
    }

    /**
     * Start a task
     */
    public function start(Request $request, $uuid)
    {
        $staff = $request->user()->staffMember;

        $task = Task::where('uuid', $uuid)
            ->where('assigned_to', $staff->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        if ($task->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Task cannot be started from current status: ' . $task->status,
            ], 400);
        }

        $task->start($staff->id);

        return response()->json([
            'success' => true,
            'message' => 'Task started successfully',
            'data' => $this->formatTask($task->fresh()),
        ], 200);
    }

    /**
     * Complete a task
     */
    public function complete(Request $request, $uuid)
    {
        $staff = $request->user()->staffMember;

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $task = Task::where('uuid', $uuid)
            ->where('assigned_to', $staff->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        if ($task->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Task must be in progress to complete',
            ], 400);
        }

        $task->complete($staff->id, $request->completion_notes);

        return response()->json([
            'success' => true,
            'message' => 'Task completed successfully. Waiting for verification.',
            'data' => $this->formatTask($task->fresh()),
        ], 200);
    }

    /**
     * Upload proof photos for a task
     */
    public function uploadProof(Request $request, $uuid)
    {
        $staff = $request->user()->staffMember;

        $request->validate([
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'caption' => 'nullable|string|max:255',
            'media_type' => 'required|in:proof,before,after,issue',
        ]);

        $task = Task::where('uuid', $uuid)
            ->where('assigned_to', $staff->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        $uploadedMedia = [];

        foreach ($request->file('photos') as $photo) {
            $filename = Str::uuid() . '.' . $photo->extension();
            $path = $photo->storeAs('tasks/' . $task->id, $filename, 'public');

            $media = TaskMedia::create([
                'uuid' => Str::uuid(),
                'task_id' => $task->id,
                'uploaded_by' => $request->user()->id,
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_type' => 'image',
                'mime_type' => $photo->getMimeType(),
                'file_size' => $photo->getSize(),
                'media_type' => $request->media_type,
                'caption' => $request->caption,
            ]);

            $uploadedMedia[] = [
                'id' => $media->id,
                'uuid' => $media->uuid,
                'file_name' => $media->file_name,
                'url' => Storage::url($media->file_path),
                'media_type' => $media->media_type,
            ];
        }

        // Log the upload
        $task->logs()->create([
            'uuid' => Str::uuid(),
            'staff_member_id' => $staff->id,
            'action' => 'commented',
            'notes' => "Uploaded " . count($uploadedMedia) . " proof photo(s)",
            'metadata' => ['media_count' => count($uploadedMedia)],
            'performed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($uploadedMedia) . ' photo(s) uploaded successfully',
            'data' => [
                'media' => $uploadedMedia,
            ],
        ], 201);
    }

    /**
     * Format task for API response
     */
    private function formatTask($task)
    {
        return [
            'id' => $task->id,
            'uuid' => $task->uuid,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'scheduled_at' => $task->scheduled_at?->toIso8601String(),
            'due_at' => $task->due_at?->toIso8601String(),
            'started_at' => $task->started_at?->toIso8601String(),
            'completed_at' => $task->completed_at?->toIso8601String(),
            'assigned_by' => $task->assignedBy ? [
                'name' => $task->assignedBy->user->name,
                'job_title' => $task->assignedBy->job_title,
            ] : null,
            'department' => $task->department ? [
                'id' => $task->department->id,
                'name' => $task->department->name,
            ] : null,
            'media_count' => $task->media->count(),
        ];
    }

    /**
     * Format task with detailed information
     */
    private function formatTaskDetailed($task)
    {
        return [
            'id' => $task->id,
            'uuid' => $task->uuid,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'scheduled_at' => $task->scheduled_at?->toIso8601String(),
            'due_at' => $task->due_at?->toIso8601String(),
            'started_at' => $task->started_at?->toIso8601String(),
            'completed_at' => $task->completed_at?->toIso8601String(),
            'verified_at' => $task->verified_at?->toIso8601String(),
            'completion_notes' => $task->completion_notes,
            'assigned_by' => $task->assignedBy ? [
                'uuid' => $task->assignedBy->uuid,
                'name' => $task->assignedBy->user->name,
                'job_title' => $task->assignedBy->job_title,
            ] : null,
            'department' => $task->department ? [
                'id' => $task->department->id,
                'uuid' => $task->department->uuid,
                'name' => $task->department->name,
            ] : null,
            'media' => $task->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'uuid' => $media->uuid,
                    'file_name' => $media->file_name,
                    'url' => Storage::url($media->file_path),
                    'media_type' => $media->media_type,
                    'caption' => $media->caption,
                    'uploaded_at' => $media->created_at->toIso8601String(),
                ];
            }),
            'logs' => $task->logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'notes' => $log->notes,
                    'performed_by' => $log->staffMember ? [
                        'name' => $log->staffMember->user->name,
                        'job_title' => $log->staffMember->job_title,
                    ] : null,
                    'performed_at' => $log->performed_at->toIso8601String(),
                ];
            }),
        ];
    }
}

