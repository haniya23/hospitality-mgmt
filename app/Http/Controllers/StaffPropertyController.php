<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\CleaningChecklist;
use App\Models\ChecklistExecution;
use App\Models\StaffAssignment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffPropertyController extends Controller
{
    public function __construct()
    {
        // Staff middleware handles authentication and staff verification
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get assigned properties with related data
        $assignedProperties = $user->staffAssignments()
            ->where('status', 'active')
            ->with([
                'property.category',
                'property.location',
                'property.propertyAccommodations',
                'property.cleaningChecklists' => function($query) {
                    $query->where('is_active', true);
                },
                'role'
            ])
            ->get()
            ->map(function ($assignment) {
                $property = $assignment->property;
                
                // Get property statistics
                $totalAccommodations = $property->propertyAccommodations->count();
                $activeChecklists = $property->cleaningChecklists->where('is_active', true)->count();
                
                // Get today's bookings
                $todaysBookings = Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
                    $q->where('property_id', $property->id);
                })
                ->whereDate('check_in_date', today())
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count();
                
                // Get upcoming bookings (next 7 days)
                $upcomingBookings = Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
                    $q->where('property_id', $property->id);
                })
                ->where('check_in_date', '>=', today())
                ->where('check_in_date', '<=', today()->addDays(7))
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count();
                
                // Get active checklist executions
                $activeChecklistExecutions = ChecklistExecution::whereHas('staffAssignment', function($q) use ($assignment) {
                    $q->where('property_id', $assignment->property_id);
                })
                ->where('status', 'in_progress')
                ->count();
                
                return [
                    'assignment' => $assignment,
                    'property' => $property,
                    'role' => $assignment->role,
                    'stats' => [
                        'total_accommodations' => $totalAccommodations,
                        'active_checklists' => $activeChecklists,
                        'todays_bookings' => $todaysBookings,
                        'upcoming_bookings' => $upcomingBookings,
                        'active_checklist_executions' => $activeChecklistExecutions,
                    ]
                ];
            });

        return view('staff.properties.index', compact('assignedProperties'));
    }

    public function show(Property $property)
    {
        $user = Auth::user();
        
        // Verify staff has access to this property
        $assignment = $user->staffAssignments()
            ->where('property_id', $property->id)
            ->where('status', 'active')
            ->with(['role', 'property.category', 'property.location'])
            ->first();
            
        if (!$assignment) {
            abort(403, 'You do not have access to this property.');
        }

        // Get property details
        $property->load([
            'category',
            'location',
            'propertyAccommodations',
            'cleaningChecklists' => function($query) {
                $query->where('is_active', true);
            }
        ]);

        // Get recent bookings
        $recentBookings = Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })
        ->with(['guest', 'propertyAccommodation'])
        ->orderBy('check_in_date', 'desc')
        ->limit(10)
        ->get();

        // Get active checklist executions
        $activeChecklistExecutions = ChecklistExecution::whereHas('staffAssignment', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })
        ->where('status', 'in_progress')
        ->with(['cleaningChecklist', 'staffAssignment.user'])
        ->get();

        // Get property statistics
        $stats = [
            'total_accommodations' => $property->propertyAccommodations->count(),
            'active_checklists' => $property->cleaningChecklists->count(),
            'todays_checkins' => Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
                $q->where('property_id', $property->id);
            })
            ->whereDate('check_in_date', today())
            ->where('status', 'confirmed')
            ->count(),
            'todays_checkouts' => Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
                $q->where('property_id', $property->id);
            })
            ->whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->count(),
            'upcoming_bookings' => Reservation::whereHas('propertyAccommodation', function($q) use ($property) {
                $q->where('property_id', $property->id);
            })
            ->where('check_in_date', '>=', today())
            ->where('check_in_date', '<=', today()->addDays(7))
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count(),
        ];

        return view('staff.properties.show', compact('property', 'assignment', 'recentBookings', 'activeChecklistExecutions', 'stats'));
    }

    public function accommodations(Property $property)
    {
        $user = Auth::user();
        
        // Verify staff has access to this property
        $assignment = $user->staffAssignments()
            ->where('property_id', $property->id)
            ->where('status', 'active')
            ->first();
            
        if (!$assignment) {
            abort(403, 'You do not have access to this property.');
        }

        // Get accommodations with related data
        $accommodations = $property->propertyAccommodations()
            ->with([
                'reservations' => function($query) {
                    $query->where('check_in_date', '>=', today())
                          ->where('check_in_date', '<=', today()->addDays(30))
                          ->with('guest');
                },
            ])
            ->get()
            ->map(function ($accommodation) {
                // Get current booking
                $currentBooking = $accommodation->reservations
                    ->where('check_in_date', '<=', today())
                    ->where('check_out_date', '>=', today())
                    ->whereIn('status', ['checked_in', 'confirmed'])
                    ->first();
                
                // Get next booking
                $nextBooking = $accommodation->reservations
                    ->where('check_in_date', '>', today())
                    ->where('status', 'confirmed')
                    ->sortBy('check_in_date')
                    ->first();
                
                // Get checklist executions for this accommodation
                $checklistExecutions = ChecklistExecution::whereHas('staffAssignment', function($q) use ($accommodation) {
                    $q->where('property_id', $accommodation->property_id);
                })
                ->where('property_accommodation_id', $accommodation->id)
                ->where('status', 'in_progress')
                ->with('cleaningChecklist')
                ->get();
                
                return [
                    'accommodation' => $accommodation,
                    'current_booking' => $currentBooking,
                    'next_booking' => $nextBooking,
                    'checklist_executions' => $checklistExecutions,
                    'upcoming_bookings_count' => $accommodation->reservations->count(),
                ];
            });

        return view('staff.properties.accommodations', compact('property', 'assignment', 'accommodations'));
    }

    public function checklists(Property $property)
    {
        $user = Auth::user();
        
        // Verify staff has access to this property
        $assignment = $user->staffAssignments()
            ->where('property_id', $property->id)
            ->where('status', 'active')
            ->first();
            
        if (!$assignment) {
            abort(403, 'You do not have access to this property.');
        }

        // Get checklists with execution data
        $checklists = $property->cleaningChecklists()
            ->where('is_active', true)
            ->with([
                'executions' => function($query) use ($assignment) {
                    $query->where('staff_assignment_id', $assignment->id)
                          ->orderBy('started_at', 'desc');
                }
            ])
            ->get()
            ->map(function ($checklist) use ($assignment) {
                // Get latest execution
                $latestExecution = $checklist->executions->first();
                
                // Get execution statistics
                $totalExecutions = $checklist->executions->count();
                $completedExecutions = $checklist->executions->where('status', 'completed')->count();
                $inProgressExecutions = $checklist->executions->where('status', 'in_progress')->count();
                
                return [
                    'checklist' => $checklist,
                    'latest_execution' => $latestExecution,
                    'stats' => [
                        'total_executions' => $totalExecutions,
                        'completed_executions' => $completedExecutions,
                        'in_progress_executions' => $inProgressExecutions,
                        'completion_rate' => $totalExecutions > 0 ? round(($completedExecutions / $totalExecutions) * 100, 1) : 0,
                    ]
                ];
            });

        return view('staff.properties.checklists', compact('property', 'assignment', 'checklists'));
    }

    public function staffAssignments(Property $property)
    {
        $user = Auth::user();
        
        // Verify staff has access to this property
        $assignment = $user->staffAssignments()
            ->where('property_id', $property->id)
            ->where('status', 'active')
            ->first();
            
        if (!$assignment) {
            abort(403, 'You do not have access to this property.');
        }

        // Get all staff assignments for this property
        $staffAssignments = StaffAssignment::where('property_id', $property->id)
            ->with([
                'user',
                'role',
                'staffTasks' => function($query) {
                    $query->orderBy('scheduled_at', 'desc');
                },
                'staffNotifications' => function($query) {
                    $query->where('is_read', false)
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->get()
            ->map(function ($staffAssignment) {
                // Get task statistics
                $totalTasks = $staffAssignment->staffTasks->count();
                $completedTasks = $staffAssignment->staffTasks->where('status', 'completed')->count();
                $pendingTasks = $staffAssignment->staffTasks->whereIn('status', ['pending', 'in_progress'])->count();
                $overdueTasks = $staffAssignment->staffTasks
                    ->where('scheduled_at', '<', now())
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count();
                
                // Get unread notifications count
                $unreadNotifications = $staffAssignment->staffNotifications->count();
                
                return [
                    'assignment' => $staffAssignment,
                    'user' => $staffAssignment->user,
                    'role' => $staffAssignment->role,
                    'stats' => [
                        'total_tasks' => $totalTasks,
                        'completed_tasks' => $completedTasks,
                        'pending_tasks' => $pendingTasks,
                        'overdue_tasks' => $overdueTasks,
                        'unread_notifications' => $unreadNotifications,
                        'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                    ]
                ];
            });

        return view('staff.properties.staff-assignments', compact('property', 'assignment', 'staffAssignments'));
    }
}
