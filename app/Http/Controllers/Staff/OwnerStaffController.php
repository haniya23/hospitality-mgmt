<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\StaffDepartment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerStaffController extends Controller
{
    /**
     * Display a listing of staff members across all properties.
     */
    public function index(Request $request)
    {
        $properties = auth()->user()->properties()->with('staffMembers.department', 'staffMembers.user')->get();
        $departments = StaffDepartment::active()->get();
        
        // Get all staff members for the owner's properties
        $staffMembers = StaffMember::whereIn('property_id', $properties->pluck('id'))
            ->with(['user', 'department', 'property', 'supervisor'])
            ->when($request->property_id, fn($q) => $q->where('property_id', $request->property_id))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->role, fn($q) => $q->where('staff_role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);
        
        return view('staff.owner.index', compact('staffMembers', 'properties', 'departments'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $properties = auth()->user()->properties()->get();
        $departments = StaffDepartment::active()->get();
        
        // Get potential supervisors (managers and supervisors)
        $supervisors = StaffMember::whereIn('property_id', $properties->pluck('id'))
            ->whereIn('staff_role', ['manager', 'supervisor'])
            ->where('status', 'active')
            ->with('user', 'property')
            ->get();
        
        return view('staff.owner.create', compact('properties', 'departments', 'supervisors'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'department_id' => 'nullable|exists:staff_departments,id',
            'staff_role' => 'required|in:manager,supervisor,staff',
            'job_title' => 'nullable|string|max:255',
            'reports_to' => 'nullable|exists:staff_members,id',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:15',
            'emergency_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Verify property ownership
        $property = auth()->user()->properties()->findOrFail($validated['property_id']);

        // Create user account
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'staff',
            'is_staff' => true,
            'is_active' => true,
        ]);

        // Create staff member
        $staffMember = StaffMember::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'property_id' => $validated['property_id'],
            'department_id' => $validated['department_id'],
            'staff_role' => $validated['staff_role'],
            'job_title' => $validated['job_title'],
            'reports_to' => $validated['reports_to'],
            'employment_type' => $validated['employment_type'],
            'join_date' => $validated['join_date'],
            'phone' => $validated['phone'],
            'emergency_contact' => $validated['emergency_contact'],
            'notes' => $validated['notes'],
            'status' => 'active',
        ]);

        return redirect()->route('owner.staff.show', $staffMember)
            ->with('success', 'Staff member added successfully!');
    }

    /**
     * Display the specified staff member.
     */
    public function show(StaffMember $staffMember)
    {
        $this->authorize('view', $staffMember);
        
        $staffMember->load([
            'user',
            'property',
            'department',
            'supervisor.user',
            'subordinates.user',
            'assignedTasks' => fn($q) => $q->latest()->limit(10),
            'attendance' => fn($q) => $q->latest()->limit(10),
            'leaveRequests' => fn($q) => $q->latest()->limit(5),
        ]);

        // Calculate stats
        $stats = [
            'total_tasks' => $staffMember->assignedTasks()->count(),
            'completed_tasks' => $staffMember->assignedTasks()->whereIn('status', ['completed', 'verified'])->count(),
            'pending_tasks' => $staffMember->assignedTasks()->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completion_rate' => $staffMember->getTaskCompletionRate(30),
            'subordinates_count' => $staffMember->subordinates()->count(),
        ];

        return view('staff.owner.show', compact('staffMember', 'stats'));
    }

    /**
     * Show the form for editing the staff member.
     */
    public function edit(StaffMember $staffMember)
    {
        $this->authorize('update', $staffMember);
        
        $properties = auth()->user()->properties()->get();
        $departments = StaffDepartment::active()->get();
        
        $supervisors = StaffMember::where('property_id', $staffMember->property_id)
            ->whereIn('staff_role', ['manager', 'supervisor'])
            ->where('id', '!=', $staffMember->id)
            ->where('status', 'active')
            ->with('user')
            ->get();
        
        $staffMember->load('user');
        
        return view('staff.owner.edit', compact('staffMember', 'properties', 'departments', 'supervisors'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, StaffMember $staffMember)
    {
        $this->authorize('update', $staffMember);
        
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'department_id' => 'nullable|exists:staff_departments,id',
            'staff_role' => 'required|in:manager,supervisor,staff',
            'job_title' => 'nullable|string|max:255',
            'reports_to' => 'nullable|exists:staff_members,id',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'status' => 'required|in:active,inactive,on_leave,suspended',
            'join_date' => 'required|date',
            'end_date' => 'nullable|date|after:join_date',
            'phone' => 'nullable|string|max:15',
            'emergency_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $staffMember->update($validated);

        return redirect()->route('owner.staff.show', $staffMember)
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(StaffMember $staffMember)
    {
        $this->authorize('delete', $staffMember);
        
        $staffMember->delete();

        return redirect()->route('owner.staff.index')
            ->with('success', 'Staff member removed successfully!');
    }

    /**
     * Get staff hierarchy for a property.
     */
    public function hierarchy(Property $property)
    {
        $this->authorize('view', $property);
        
        $hierarchy = [
            'managers' => StaffMember::where('property_id', $property->id)
                ->managers()
                ->with(['user', 'department', 'subordinates.user', 'subordinates.department'])
                ->get(),
        ];
        
        return response()->json($hierarchy);
    }
}
