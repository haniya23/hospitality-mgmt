<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\StaffAssignment;
use App\Models\StaffTask;
use App\Models\StaffNotification;
use App\Models\CleaningChecklist;
use App\Models\StaffPermission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OwnerStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isOwner()) {
                abort(403, 'Access denied. Owner access required.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        });

        // Apply filters
        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $staffAssignments = $query->with(['user', 'property', 'role'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add task stats to each staff assignment
        $staffAssignments->each(function ($assignment) {
            $assignment->task_stats = [
                'total' => $assignment->staffTasks()->count(),
                'pending' => $assignment->staffTasks()->where('status', 'pending')->count(),
                'in_progress' => $assignment->staffTasks()->where('status', 'in_progress')->count(),
                'completed' => $assignment->staffTasks()->where('status', 'completed')->count(),
                'overdue' => $assignment->staffTasks()->where('scheduled_at', '<', now())
                                      ->whereIn('status', ['pending', 'in_progress'])
                                      ->count(),
            ];
            
            $assignment->completion_rate = $assignment->task_stats['total'] > 0 
                ? round(($assignment->task_stats['completed'] / $assignment->task_stats['total']) * 100, 2)
                : 0;
            
            $assignment->recent_activities = $assignment->staffActivityLogs()
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'description' => $log->getActionDescription(),
                        'time_ago' => $log->getTimeAgo(),
                    ];
                });
        });

        $properties = $user->properties()->where('status', 'active')->get();
        $roles = Role::whereIn('property_id', $properties->pluck('id'))->get();

        // Get overall stats
        $stats = [
            'total_staff' => $staffAssignments->count(),
            'active_staff' => $staffAssignments->where('status', 'active')->count(),
            'total_tasks' => $staffAssignments->sum('task_stats.total'),
            'completed_tasks' => $staffAssignments->sum('task_stats.completed'),
            'overdue_tasks' => $staffAssignments->sum('task_stats.overdue'),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'staffAssignments' => $staffAssignments,
                'properties' => $properties,
                'roles' => $roles,
                'stats' => $stats,
            ]);
        }

        return view('owner.staff.index', compact('staffAssignments', 'properties', 'roles', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        $properties = $user->properties()->where('status', 'active')->get();
        $roles = Role::whereIn('property_id', $properties->pluck('id'))->get();

        return view('owner.staff.create', compact('properties', 'roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile_number' => 'required|string|unique:users,mobile_number',
                'pin' => 'required|string|size:4',
                'property_id' => 'required|exists:properties,id',
                'role_id' => 'required|exists:roles,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after:start_date',
            ]);

            // Verify property ownership
            $property = Property::where('id', $request->property_id)
                               ->where('owner_id', Auth::id())
                               ->firstOrFail();

            // Verify role belongs to property
            $role = Role::where('id', $request->role_id)
                       ->where('property_id', $request->property_id)
                       ->firstOrFail();

            $result = DB::transaction(function () use ($request, $property, $role) {
                // Create staff user
                $staffUser = User::createStaff([
                    'name' => $request->name,
                    'mobile_number' => $request->mobile_number,
                    'pin_hash' => Hash::make($request->pin),
                    'user_type' => 'staff',
                    'is_staff' => true,
                    'is_active' => true,
                ]);

                // Create staff assignment
                $staffAssignment = StaffAssignment::create([
                    'user_id' => $staffUser->id,
                    'property_id' => $property->id,
                    'role_id' => $role->id,
                    'status' => 'active',
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

                // Create permissions based on frontend selection or defaults
                if ($request->has('permissions')) {
                    StaffPermission::createCustomPermissions($staffAssignment->id, $request->permissions);
                } else {
                    StaffPermission::createDefaultPermissions($staffAssignment->id);
                }

                // Send welcome notification
                StaffNotification::create([
                    'staff_assignment_id' => $staffAssignment->id,
                    'from_user_id' => Auth::id(),
                    'title' => 'Welcome to the Team!',
                    'message' => "Welcome {$staffUser->name}! You have been assigned to {$property->name}. Please check your dashboard for assigned tasks.",
                    'type' => 'general',
                    'priority' => 'medium',
                ]);

                return [
                    'staff_user' => $staffUser,
                    'staff_assignment' => $staffAssignment,
                ];
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Staff member created successfully!',
                    'data' => $result
                ]);
            }

            return redirect()->route('owner.staff.index')
                            ->with('success', 'Staff member added successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create staff member: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function show($staffAssignmentUuid)
    {
        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->with(['user', 'property', 'role', 'staffTasks', 'staffNotifications'])
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        $recentTasks = $staffAssignment->staffTasks()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentNotifications = $staffAssignment->staffNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $taskStats = [
            'total' => $staffAssignment->staffTasks()->count(),
            'pending' => $staffAssignment->staffTasks()->where('status', 'pending')->count(),
            'in_progress' => $staffAssignment->staffTasks()->where('status', 'in_progress')->count(),
            'completed' => $staffAssignment->staffTasks()->where('status', 'completed')->count(),
            'overdue' => $staffAssignment->staffTasks()->where('scheduled_at', '<', now())
                                          ->whereIn('status', ['pending', 'in_progress'])
                                          ->count(),
        ];

        $completionRate = $staffAssignment->staffTasks()->count() > 0 
            ? round(($taskStats['completed'] / $staffAssignment->staffTasks()->count()) * 100, 2)
            : 0;

        return view('owner.staff.show', compact(
            'staffAssignment',
            'recentTasks',
            'recentNotifications',
            'taskStats',
            'completionRate'
        ));
    }

    public function edit($staffAssignmentUuid)
    {
        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->with(['user', 'property', 'role'])
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        $user = Auth::user();
        $properties = $user->properties()->where('status', 'active')->get();
        $roles = Role::whereIn('property_id', $properties->pluck('id'))->get();

        return view('owner.staff.edit', compact('staffAssignment', 'properties', 'roles'));
    }

    public function update(Request $request, $staffAssignmentUuid)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string',
            'pin' => 'nullable|string|size:4',
            'property_id' => 'required|exists:properties,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->with('user')
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        // Verify property ownership
        $property = Property::where('id', $request->property_id)
                           ->where('owner_id', Auth::id())
                           ->firstOrFail();

        // Verify role belongs to property
        $role = Role::where('id', $request->role_id)
                   ->where('property_id', $request->property_id)
                   ->firstOrFail();

        DB::transaction(function () use ($request, $staffAssignment, $property, $role) {
            // Update staff user
            $staffAssignment->user->update([
                'name' => $request->name,
                'mobile_number' => $request->mobile_number,
            ]);

            if ($request->pin) {
                $staffAssignment->user->update([
                    'pin_hash' => Hash::make($request->pin),
                ]);
            }

            // Update staff assignment
            $staffAssignment->update([
                'property_id' => $property->id,
                'role_id' => $role->id,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
        });

        return redirect()->route('owner.staff.show', $staffAssignmentId)
                        ->with('success', 'Staff member updated successfully.');
    }

    public function destroy($staffAssignmentId)
    {
        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->with('user')
        ->findOrFail($staffAssignmentId);

        DB::transaction(function () use ($staffAssignment) {
            // Deactivate staff assignment
            $staffAssignment->update(['status' => 'inactive']);
            
            // Deactivate user account
            $staffAssignment->user->update(['is_active' => false]);
        });

        return redirect()->route('owner.staff.index')
                        ->with('success', 'Staff member deactivated successfully.');
    }

    public function assignTask(Request $request, $staffAssignmentUuid)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'task_type' => 'required|in:cleaning,maintenance,guest_service,check_in,check_out,inspection,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_at' => 'nullable|date',
        ]);

        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        $task = StaffTask::create([
            'staff_assignment_id' => $staffAssignment->id,
            'property_id' => $staffAssignment->property_id,
            'task_name' => $request->task_name,
            'description' => $request->description,
            'task_type' => $request->task_type,
            'priority' => $request->priority,
            'scheduled_at' => $request->scheduled_at,
            'assigned_by' => Auth::id(),
        ]);

        // Send notification to staff
        StaffNotification::createTaskAssignment(
            $staffAssignment->id,
            Auth::id(),
            $task->task_name,
            $task->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Task assigned successfully',
            'task' => $task
        ]);
    }

    public function sendNotification(Request $request, $staffAssignmentUuid)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:task_assignment,urgent_update,reminder,general',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        $notification = StaffNotification::create([
            'staff_assignment_id' => $staffAssignment->id,
            'from_user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'priority' => $request->priority,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
            'notification' => $notification
        ]);
    }

    public function updatePermissions(Request $request, $staffAssignmentUuid)
    {
        $staffAssignment = StaffAssignment::whereHas('property', function($q) {
            $q->where('owner_id', Auth::id());
        })
        ->where('uuid', $staffAssignmentUuid)
        ->firstOrFail();

        $permissions = $request->permissions ?? [];

        foreach ($permissions as $key => $permissionData) {
            if (isset($permissionData['granted']) && $permissionData['granted']) {
                StaffPermission::grantPermission($staffAssignment->id, $key, $permissionData['restrictions'] ?? []);
            } else {
                StaffPermission::denyPermission($staffAssignment->id, $key);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }

    public function getStaffStats()
    {
        $user = Auth::user();
        
        $totalStaff = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->count();

        $activeStaff = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'active')->count();

        $totalTasks = StaffTask::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->count();

        $completedTasks = StaffTask::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'completed')->count();

        $overdueTasks = StaffTask::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('scheduled_at', '<', now())
          ->whereIn('status', ['pending', 'in_progress'])
          ->count();

        return response()->json([
            'total_staff' => $totalStaff,
            'active_staff' => $activeStaff,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
        ]);
    }

    public function analytics(Request $request)
    {
        $user = Auth::user();
        $days = $request->get('days', 30);
        $propertyId = $request->get('property_id');
        
        $startDate = now()->subDays($days);
        
        // Base query for staff assignments
        $query = StaffAssignment::whereHas('property', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        });
        
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }
        
        $staffAssignments = $query->with(['user', 'property', 'staffTasks', 'staffActivityLogs'])->get();
        
        // Calculate analytics
        $analytics = [
            'active_staff' => $staffAssignments->where('status', 'active')->count(),
            'completed_tasks' => $staffAssignments->sum(function($assignment) use ($startDate) {
                return $assignment->staffTasks()
                    ->where('status', 'completed')
                    ->where('completed_at', '>=', $startDate)
                    ->count();
            }),
            'avg_task_time' => $this->calculateAverageTaskTime($staffAssignments, $startDate),
            'completion_rate' => $this->calculateCompletionRate($staffAssignments, $startDate),
        ];
        
        // Staff performance data
        $staffPerformance = $staffAssignments->map(function($assignment) use ($startDate) {
            $tasks = $assignment->staffTasks()->where('created_at', '>=', $startDate)->get();
            
            return [
                'id' => $assignment->id,
                'name' => $assignment->user->name,
                'property_name' => $assignment->property->name,
                'total_tasks' => $tasks->count(),
                'completed_tasks' => $tasks->where('status', 'completed')->count(),
                'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
                'overdue_tasks' => $tasks->where('scheduled_at', '<', now())
                                      ->whereIn('status', ['pending', 'in_progress'])
                                      ->count(),
                'completion_rate' => $tasks->count() > 0 
                    ? round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100, 2)
                    : 0,
            ];
        })->sortByDesc('completion_rate')->values();
        
        // Task type breakdown
        $taskTypeBreakdown = $this->getTaskTypeBreakdown($staffAssignments, $startDate);
        
        // Priority distribution
        $priorityDistribution = $this->getPriorityDistribution($staffAssignments, $startDate);
        
        // Recent activity
        $recentActivity = $this->getRecentActivity($staffAssignments, $startDate);
        
        if ($request->expectsJson()) {
            return response()->json([
                'analytics' => $analytics,
                'staffPerformance' => $staffPerformance,
                'taskTypeBreakdown' => $taskTypeBreakdown,
                'priorityDistribution' => $priorityDistribution,
                'recentActivity' => $recentActivity,
            ]);
        }
        
        return view('owner.staff.analytics', compact('analytics', 'staffPerformance', 'taskTypeBreakdown', 'priorityDistribution', 'recentActivity'));
    }
    
    private function calculateAverageTaskTime($staffAssignments, $startDate)
    {
        $completedTasks = collect();
        
        foreach ($staffAssignments as $assignment) {
            $tasks = $assignment->staffTasks()
                ->where('status', 'completed')
                ->where('completed_at', '>=', $startDate)
                ->whereNotNull('started_at')
                ->whereNotNull('completed_at')
                ->get();
            
            foreach ($tasks as $task) {
                $duration = $task->started_at->diffInMinutes($task->completed_at);
                $completedTasks->push($duration);
            }
        }
        
        if ($completedTasks->isEmpty()) {
            return '0h 0m';
        }
        
        $avgMinutes = $completedTasks->avg();
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;
        
        return $hours . 'h ' . round($minutes) . 'm';
    }
    
    private function calculateCompletionRate($staffAssignments, $startDate)
    {
        $totalTasks = 0;
        $completedTasks = 0;
        
        foreach ($staffAssignments as $assignment) {
            $tasks = $assignment->staffTasks()->where('created_at', '>=', $startDate)->get();
            $totalTasks += $tasks->count();
            $completedTasks += $tasks->where('status', 'completed')->count();
        }
        
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
    }
    
    private function getTaskTypeBreakdown($staffAssignments, $startDate)
    {
        $taskTypes = [
            'cleaning' => 'Cleaning',
            'maintenance' => 'Maintenance',
            'guest_service' => 'Guest Service',
            'check_in' => 'Check-in',
            'check_out' => 'Check-out',
            'inspection' => 'Inspection',
            'other' => 'Other',
        ];
        
        $breakdown = [];
        $totalTasks = 0;
        
        foreach ($staffAssignments as $assignment) {
            $tasks = $assignment->staffTasks()->where('created_at', '>=', $startDate)->get();
            $totalTasks += $tasks->count();
            
            foreach ($tasks as $task) {
                $type = $task->task_type;
                if (!isset($breakdown[$type])) {
                    $breakdown[$type] = 0;
                }
                $breakdown[$type]++;
            }
        }
        
        $result = [];
        foreach ($taskTypes as $key => $name) {
            $count = $breakdown[$key] ?? 0;
            $percentage = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 2) : 0;
            
            $result[] = [
                'type' => $key,
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        
        return collect($result)->sortByDesc('count')->values()->toArray();
    }
    
    private function getPriorityDistribution($staffAssignments, $startDate)
    {
        $priorities = [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
        
        $distribution = [];
        $totalTasks = 0;
        
        foreach ($staffAssignments as $assignment) {
            $tasks = $assignment->staffTasks()->where('created_at', '>=', $startDate)->get();
            $totalTasks += $tasks->count();
            
            foreach ($tasks as $task) {
                $priority = $task->priority;
                if (!isset($distribution[$priority])) {
                    $distribution[$priority] = 0;
                }
                $distribution[$priority]++;
            }
        }
        
        $result = [];
        foreach ($priorities as $key => $name) {
            $count = $distribution[$key] ?? 0;
            $percentage = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 2) : 0;
            
            $result[] = [
                'priority' => $key,
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
        
        return collect($result)->sortByDesc('count')->values()->toArray();
    }
    
    private function getRecentActivity($staffAssignments, $startDate)
    {
        $activities = collect();
        
        foreach ($staffAssignments as $assignment) {
            $logs = $assignment->staffActivityLogs()
                ->where('created_at', '>=', $startDate)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($logs as $log) {
                $activities->push([
                    'id' => $log->id,
                    'description' => $log->getActionDescription(),
                    'staff_name' => $assignment->user->name,
                    'time_ago' => $log->getTimeAgo(),
                    'created_at' => $log->created_at,
                ]);
            }
        }
        
        return $activities->sortByDesc('created_at')->take(20)->values()->toArray();
    }
}
