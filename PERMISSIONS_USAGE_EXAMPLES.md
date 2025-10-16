# Staff Permissions - Usage Examples

This document provides practical examples of how to use the staff permissions system in your code.

## Table of Contents
1. [Blade Template Examples](#blade-template-examples)
2. [Controller Examples](#controller-examples)
3. [Middleware Examples](#middleware-examples)
4. [Model Examples](#model-examples)

---

## Blade Template Examples

### Using Custom Blade Directives

```blade
{{-- Check if staff has specific permission --}}
@staffCan('can_view_payments')
    <a href="{{ route('payments.index') }}" class="btn">View Payments</a>
@endstaffCan

{{-- Check if user is a manager --}}
@isManager
    <div class="admin-panel">
        <h3>Manager Controls</h3>
        <a href="{{ route('staff.permissions.index') }}">Manage Access</a>
    </div>
@endisManager

{{-- Check if user is a supervisor --}}
@isSupervisor
    <button onclick="assignTask()">Assign Task</button>
@endisSupervisor

{{-- Check if user is regular staff --}}
@isStaff
    <p>You have basic access. Contact your supervisor for more permissions.</p>
@endisStaff

{{-- Combined checks --}}
@staffCan('can_edit_reservations')
    <button class="btn-edit">Edit Reservation</button>
@else
    <span class="text-gray-400">View Only</span>
@endstaffCan
```

### Using Auth Helper in Blade

```blade
{{-- Check permission directly --}}
@if(auth()->user()->staffMember->hasPermission('can_create_tasks'))
    <a href="{{ route('tasks.create') }}">Create New Task</a>
@endif

{{-- Check role --}}
@if(auth()->user()->staffMember->isManager())
    <div class="manager-tools">
        <!-- Manager-specific content -->
    </div>
@endif

{{-- Show created/updated by information --}}
<div class="audit-info">
    <p>Created by: {{ $reservation->creator->name }}</p>
    <p>Last updated by: {{ $reservation->updater->name ?? 'N/A' }}</p>
</div>
```

---

## Controller Examples

### Basic Permission Checking

```php
<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $staff = auth()->user()->staffMember;
        
        // Check permission
        if (!$staff->hasPermission('can_view_reservations')) {
            abort(403, 'You do not have permission to view reservations.');
        }
        
        // Get reservations based on hierarchy
        if ($staff->isManager()) {
            // Managers see all reservations in their property
            $reservations = Reservation::whereHas('accommodation', function($query) use ($staff) {
                $query->where('property_id', $staff->property_id);
            })->get();
        } elseif ($staff->isSupervisor()) {
            // Supervisors see reservations created by their team
            $teamIds = $staff->getAllSubordinates()->pluck('user_id')->push($staff->user_id);
            $reservations = Reservation::whereIn('created_by', $teamIds)->get();
        } else {
            // Staff only see their own reservations
            $reservations = Reservation::where('created_by', $staff->user_id)->get();
        }
        
        return view('reservations.index', compact('reservations'));
    }
    
    public function create()
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_create_reservations')) {
            abort(403, 'You do not have permission to create reservations.');
        }
        
        return view('reservations.create');
    }
    
    public function edit(Reservation $reservation)
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_edit_reservations')) {
            abort(403, 'You do not have permission to edit reservations.');
        }
        
        // Additional check: can they edit THIS specific reservation?
        if (!$staff->isManager() && $reservation->created_by !== $staff->user_id) {
            abort(403, 'You can only edit your own reservations.');
        }
        
        return view('reservations.edit', compact('reservation'));
    }
    
    public function destroy(Reservation $reservation)
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_delete_reservations')) {
            abort(403, 'You do not have permission to delete reservations.');
        }
        
        $reservation->delete();
        
        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}
```

### Task Controller with Hierarchy

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_view_tasks')) {
            abort(403, 'You do not have permission to view tasks.');
        }
        
        // Get tasks based on hierarchy
        if ($staff->isManager()) {
            $tasks = Task::where('property_id', $staff->property_id)->get();
        } elseif ($staff->isSupervisor()) {
            $teamIds = $staff->getAllSubordinates()->pluck('id')->push($staff->id);
            $tasks = Task::whereIn('assigned_to', $teamIds)->get();
        } else {
            $tasks = Task::where('assigned_to', $staff->id)->get();
        }
        
        return view('tasks.index', compact('tasks'));
    }
    
    public function store(Request $request)
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_create_tasks')) {
            abort(403, 'You do not have permission to create tasks.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:staff_members,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_at' => 'required|date',
        ]);
        
        // Check if staff can assign to this person
        $assignee = StaffMember::find($request->assigned_to);
        if (!$staff->canManage($assignee) && !$staff->isManager()) {
            abort(403, 'You can only assign tasks to your direct reports.');
        }
        
        $task = Task::create(array_merge($validated, [
            'property_id' => $staff->property_id,
            'assigned_by' => $staff->id,
            // created_by and updated_by are automatically set by HasCreatedUpdatedBy trait
        ]));
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }
    
    public function verify(Task $task)
    {
        $staff = auth()->user()->staffMember;
        
        if (!$staff->hasPermission('can_verify_tasks')) {
            abort(403, 'You do not have permission to verify tasks.');
        }
        
        // Check if the task was assigned by them or they're a manager
        if (!$staff->isManager() && $task->assigned_by !== $staff->id) {
            abort(403, 'You can only verify tasks you assigned.');
        }
        
        $task->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Task verified successfully.');
    }
}
```

---

## Middleware Examples

### Using Permission Middleware in Routes

```php
// routes/web.php

// Single permission
Route::get('/payments', [PaymentController::class, 'index'])
    ->middleware(['auth', 'staff.permission:can_view_payments']);

// Multiple permissions (use multiple middleware)
Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware(['auth', 'staff.permission:can_create_payments']);

// Role-based access
Route::middleware(['auth', 'staff.role:manager'])->group(function () {
    Route::get('/reports/financial', [ReportController::class, 'financial']);
    Route::get('/settings', [SettingsController::class, 'index']);
});

// Combined: Role + Permission
Route::middleware(['auth', 'staff.role:supervisor'])->group(function () {
    Route::get('/team', [TeamController::class, 'index'])
        ->middleware('staff.permission:can_view_staff');
    
    Route::post('/tasks/assign', [TaskController::class, 'assign'])
        ->middleware('staff.permission:can_assign_tasks');
});
```

### Custom Middleware Example

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckReservationAccess
{
    public function handle(Request $request, Closure $next)
    {
        $staff = auth()->user()->staffMember;
        $reservation = $request->route('reservation');
        
        // Managers can access everything
        if ($staff->isManager()) {
            return $next($request);
        }
        
        // Check if staff created this reservation or is supervisor of creator
        if ($reservation->created_by === $staff->user_id) {
            return $next($request);
        }
        
        if ($staff->isSupervisor()) {
            $teamUserIds = $staff->getAllSubordinates()->pluck('user_id');
            if ($teamUserIds->contains($reservation->created_by)) {
                return $next($request);
            }
        }
        
        abort(403, 'You do not have access to this reservation.');
    }
}
```

---

## Model Examples

### Using HasCreatedUpdatedBy Trait

```php
<?php

namespace App\Models;

use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasCreatedUpdatedBy; // Automatically tracks created_by and updated_by
    
    protected $fillable = [
        'reservation_id',
        'amount',
        'status',
        // No need to add created_by or updated_by to fillable
    ];
    
    // The trait automatically provides:
    // - creator() relationship
    // - updater() relationship
    // - Auto-sets created_by on create
    // - Auto-sets updated_by on update
}

// Usage:
$invoice = Invoice::create([
    'reservation_id' => 1,
    'amount' => 1000,
    'status' => 'pending',
]);
// created_by is automatically set to auth()->id()

$invoice->update(['status' => 'paid']);
// updated_by is automatically set to auth()->id()

// Get who created it
echo $invoice->creator->name;

// Get who last updated it
echo $invoice->updater->name;
```

### Scoping Queries by Hierarchy

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Reservation extends Model
{
    /**
     * Scope query to only show accessible reservations based on staff hierarchy
     */
    public function scopeAccessibleBy(Builder $query, StaffMember $staff)
    {
        if ($staff->isManager()) {
            // Managers see all in their property
            return $query->whereHas('accommodation', function($q) use ($staff) {
                $q->where('property_id', $staff->property_id);
            });
        }
        
        if ($staff->isSupervisor()) {
            // Supervisors see their team's reservations
            $teamUserIds = $staff->getAllSubordinates()
                ->pluck('user_id')
                ->push($staff->user_id);
            
            return $query->whereIn('created_by', $teamUserIds);
        }
        
        // Staff see only their own
        return $query->where('created_by', $staff->user_id);
    }
}

// Usage in controller:
$reservations = Reservation::accessibleBy($staff)->get();
```

---

## Quick Reference

### Available Permissions

- `can_view_reservations`
- `can_create_reservations`
- `can_edit_reservations`
- `can_delete_reservations`
- `can_view_guests`
- `can_create_guests`
- `can_edit_guests`
- `can_delete_guests`
- `can_view_properties`
- `can_edit_properties`
- `can_view_accommodations`
- `can_edit_accommodations`
- `can_view_payments`
- `can_create_payments`
- `can_edit_payments`
- `can_view_invoices`
- `can_create_invoices`
- `can_view_tasks`
- `can_create_tasks`
- `can_edit_tasks`
- `can_delete_tasks`
- `can_assign_tasks`
- `can_verify_tasks`
- `can_view_staff`
- `can_create_staff`
- `can_edit_staff`
- `can_delete_staff`
- `can_view_reports`
- `can_view_financial_reports`
- `can_manage_permissions`

### Available Blade Directives

- `@staffCan('permission_name')` / `@endstaffCan`
- `@isManager` / `@endisManager`
- `@isSupervisor` / `@endisSupervisor`
- `@isStaff` / `@endisStaff`

### Available Middleware

- `staff.role:manager` - Requires manager role
- `staff.role:supervisor` - Requires supervisor or manager role
- `staff.role:staff` - Requires any staff role
- `staff.permission:permission_name` - Requires specific permission

### StaffMember Helper Methods

- `$staff->hasPermission('permission_name')` - Check if has permission
- `$staff->isManager()` - Check if manager
- `$staff->isSupervisor()` - Check if supervisor
- `$staff->isStaff()` - Check if regular staff
- `$staff->canManage($otherStaff)` - Check if can manage another staff member
- `$staff->getAccessibleStaff()` - Get all staff members they can see
- `$staff->getAllSubordinates()` - Get all direct and indirect reports

---

## Best Practices

1. **Always check permissions in controllers** - Don't rely only on hiding UI elements
2. **Use middleware for route protection** - Prevents direct URL access
3. **Implement hierarchy checks** - Staff should only see their own data
4. **Use the HasCreatedUpdatedBy trait** - Automatically track who made changes
5. **Provide clear error messages** - Tell users why they can't access something
6. **Log permission denials** - Help debug and identify permission issues
7. **Test with different roles** - Ensure each role sees only what they should

---

**Last Updated:** October 12, 2025



