<?php

namespace App\Http\Controllers;

use App\Models\StaffTask;
use App\Models\StaffNotification;
use App\Models\ChecklistExecution;
use App\Models\CleaningChecklist;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function __construct()
    {
        // Staff middleware handles authentication and staff verification
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $assignedProperties = $user->getAssignedProperties();
        $todaysTasks = $user->getTodaysTasks();
        $overdueTasks = $user->getOverdueTasks();
        $unreadNotifications = $user->getUnreadNotifications();
        $urgentNotifications = $user->staffNotifications()
            ->where('is_read', false)
            ->where('priority', 'urgent')
            ->orderBy('staff_notifications.created_at', 'desc')
            ->get();
        
        $upcomingBookings = Reservation::whereHas('propertyAccommodation', function($q) use ($assignedProperties) {
            $q->whereIn('property_id', $assignedProperties->pluck('id'));
        })
        ->where('check_in_date', '>=', today())
        ->where('check_in_date', '<=', today()->addDays(7))
        ->whereIn('status', ['confirmed', 'checked_in'])
        ->orderBy('check_in_date')
        ->get();

        // Get today's check-ins and check-outs for guest service
        $todaysCheckins = Reservation::whereHas('propertyAccommodation', function($q) use ($assignedProperties) {
            $q->whereIn('property_id', $assignedProperties->pluck('id'));
        })
        ->whereDate('check_in_date', today())
        ->where('status', 'confirmed')
        ->whereDoesntHave('checkInRecord')
        ->with(['guest', 'propertyAccommodation.property'])
        ->orderBy('check_in_date')
        ->get()
        ->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'guest_name' => $reservation->guest->name ?? 'Guest',
                'property_name' => $reservation->propertyAccommodation->property->name ?? 'Property',
                'check_in_time' => \Carbon\Carbon::parse($reservation->check_in_date)->format('H:i'),
                'status' => $reservation->status
            ];
        });

        $todaysCheckouts = Reservation::whereHas('propertyAccommodation', function($q) use ($assignedProperties) {
            $q->whereIn('property_id', $assignedProperties->pluck('id'));
        })
        ->whereDate('check_out_date', today())
        ->where('status', 'checked_in')
        ->whereDoesntHave('checkOutRecord')
        ->with(['guest', 'propertyAccommodation.property'])
        ->orderBy('check_out_date')
        ->get()
        ->map(function($reservation) {
            return [
                'id' => $reservation->id,
                'guest_name' => $reservation->guest->name ?? 'Guest',
                'property_name' => $reservation->propertyAccommodation->property->name ?? 'Property',
                'check_out_time' => \Carbon\Carbon::parse($reservation->check_out_date)->format('H:i'),
                'status' => $reservation->status
            ];
        });

        $activeChecklists = ChecklistExecution::whereHas('staffAssignment', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'active');
        })
        ->where('status', 'in_progress')
        ->orderBy('started_at', 'desc')
        ->get();

        $availableChecklists = CleaningChecklist::whereIn('property_id', $assignedProperties->pluck('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $taskCompletionRate = $user->getTaskCompletionRate(7);
        $todaysActivity = $user->getTodaysActivity();

        return view('staff.dashboard', compact(
            'assignedProperties',
            'todaysTasks',
            'overdueTasks',
            'unreadNotifications',
            'urgentNotifications',
            'upcomingBookings',
            'todaysCheckins',
            'todaysCheckouts',
            'activeChecklists',
            'availableChecklists',
            'taskCompletionRate',
            'todaysActivity'
        ));
    }

    public function tasks(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->staffTasks()->orderBy('priority', 'desc')->orderBy('scheduled_at');

        // Filters
        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->task_type && $request->task_type !== 'all') {
            $query->where('task_type', $request->task_type);
        }
        if ($request->priority && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('staff_tasks.status', $request->status);
        } elseif (!$request->show_completed) {
            $query->whereIn('staff_tasks.status', ['pending', 'in_progress']);
        }

        $tasks = $query->paginate(15);
        $assignedProperties = $user->getAssignedProperties();

        // Task stats
        $taskStats = [
            'total' => $user->staffTasks()->count(),
            'pending' => $user->staffTasks()->where('staff_tasks.status', 'pending')->count(),
            'in_progress' => $user->staffTasks()->where('staff_tasks.status', 'in_progress')->count(),
            'completed' => $user->staffTasks()->where('staff_tasks.status', 'completed')->count(),
            'overdue' => $user->staffTasks()->where('scheduled_at', '<', now())
                              ->whereIn('staff_tasks.status', ['pending', 'in_progress'])
                              ->count(),
        ];

        return view('staff.tasks', compact('tasks', 'assignedProperties', 'taskStats'));
    }

    public function startTask(Request $request, $taskId)
    {
        $task = StaffTask::findOrFail($taskId);
        
        if (!$task->canBeStartedBy(Auth::id())) {
            return response()->json(['error' => 'You are not authorized to start this task.'], 403);
        }

        $task->startTask();
        
        return response()->json([
            'success' => true,
            'message' => 'Task started successfully',
            'task' => $task->fresh()
        ]);
    }

    public function completeTask(Request $request, $taskId)
    {
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
            'completion_photos.*' => 'image|max:2048',
        ]);

        $task = StaffTask::findOrFail($taskId);
        
        if (!$task->canBeCompletedBy(Auth::id())) {
            return response()->json(['error' => 'You are not authorized to complete this task.'], 403);
        }

        // Upload photos if any
        $photoUrls = [];
        if ($request->hasFile('completion_photos')) {
            foreach ($request->file('completion_photos') as $photo) {
                $path = $photo->store('staff-task-photos', 'public');
                $photoUrls[] = Storage::url($path);
            }
        }

        $task->completeTask($request->completion_notes, $photoUrls);
        
        return response()->json([
            'success' => true,
            'message' => 'Task completed successfully',
            'task' => $task->fresh()
        ]);
    }

    public function cancelTask(Request $request, $taskId)
    {
        $task = StaffTask::findOrFail($taskId);
        
        if ($task->staffAssignment->user_id !== Auth::id()) {
            return response()->json(['error' => 'You are not authorized to cancel this task.'], 403);
        }

        $task->update(['status' => 'cancelled']);
        
        return response()->json([
            'success' => true,
            'message' => 'Task cancelled successfully',
            'task' => $task->fresh()
        ]);
    }

    public function notifications()
    {
        $user = Auth::user();
        
        $notifications = $user->staffNotifications()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.notifications', compact('notifications'));
    }

    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $notification = StaffNotification::findOrFail($notificationId);
        
        if ($notification->staffAssignment->user_id !== Auth::id()) {
            return response()->json(['error' => 'You are not authorized to mark this notification as read.'], 403);
        }

        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->staffNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function checklists()
    {
        $user = Auth::user();
        
        $activeChecklists = ChecklistExecution::whereHas('staffAssignment', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'active');
        })
        ->where('status', 'in_progress')
        ->orderBy('started_at', 'desc')
        ->get();

        $availableChecklists = CleaningChecklist::whereIn('property_id', $user->getAssignedProperties()->pluck('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('staff.checklists', compact('activeChecklists', 'availableChecklists'));
    }

    public function startChecklist(Request $request, $checklistId)
    {
        $checklist = CleaningChecklist::findOrFail($checklistId);
        
        // Simple access control - all staff can execute checklists

        $staffAssignment = Auth::user()->staffAssignments()
            ->where('property_id', $checklist->property_id)
            ->where('status', 'active')
            ->first();

        if (!$staffAssignment) {
            return response()->json(['error' => 'No active staff assignment found for this property.'], 403);
        }

        $execution = $checklist->createExecution(
            $staffAssignment->id,
            $request->accommodation_id,
            $request->reservation_id
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Checklist started successfully',
            'execution_id' => $execution->id,
            'redirect_url' => route('staff.checklist.execute', $execution->uuid)
        ]);
    }

    public function executeChecklist($executionUuid)
    {
        $execution = ChecklistExecution::where('uuid', $executionUuid)->firstOrFail();
        
        if ($execution->staffAssignment->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to execute this checklist.');
        }

        return view('staff.checklist-execute', compact('execution'));
    }

    public function updateChecklistItem(Request $request, $executionId)
    {
        $execution = ChecklistExecution::findOrFail($executionId);
        
        if ($execution->staffAssignment->user_id !== Auth::id()) {
            return response()->json(['error' => 'You are not authorized to update this checklist.'], 403);
        }

        $itemIndex = $request->item_index;
        $completed = $request->completed;

        if ($completed) {
            $execution->completeItem($itemIndex);
        } else {
            $execution->uncompleteItem($itemIndex);
        }

        return response()->json([
            'success' => true,
            'completion_percentage' => $execution->getCompletionPercentage(),
            'is_completed' => $execution->isCompleted()
        ]);
    }

    public function completeChecklist(Request $request, $executionId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'photos.*' => 'image|max:2048',
        ]);

        $execution = ChecklistExecution::findOrFail($executionId);
        
        if ($execution->staffAssignment->user_id !== Auth::id()) {
            return response()->json(['error' => 'You are not authorized to complete this checklist.'], 403);
        }

        // Upload photos if any
        $photoUrls = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('checklist-photos', 'public');
                $photoUrls[] = Storage::url($path);
            }
        }

        $execution->completeExecution($request->notes, $photoUrls);
        
        return response()->json([
            'success' => true,
            'message' => 'Checklist completed successfully',
            'redirect_url' => route('staff.checklists')
        ]);
    }

    public function activity()
    {
        $user = Auth::user();
        
        // Activity logging removed - using simple access control system
        // Return empty collection since we don't track detailed activities anymore
        $activities = collect([]);

        return view('staff.activity', compact('activities'));
    }

    public function getUnreadNotificationsCount()
    {
        $count = Auth::user()->getUnreadNotificationsCount();
        $urgentCount = Auth::user()->getUrgentNotificationsCount();
        
        return response()->json([
            'total' => $count,
            'urgent' => $urgentCount
        ]);
    }
}
