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
        
        // Get all potential supervisors grouped by property
        // Managers can supervise supervisors and staff
        // Supervisors can supervise staff only
        $allStaff = StaffMember::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 'active')
            ->with('user', 'property', 'department')
            ->orderBy('property_id')
            ->orderByRaw("FIELD(staff_role, 'manager', 'supervisor', 'staff')")
            ->get();
        
        // Prepare staff data for JavaScript
        $staffData = $allStaff->map(function($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user->name,
                'role' => $staff->staff_role,
                'property_id' => $staff->property_id,
                'department' => $staff->department ? $staff->department->name : 'No Dept',
                'job_title' => $staff->job_title ?? ucfirst($staff->staff_role),
            ];
        });
        
        return view('staff.owner.create', compact('properties', 'departments', 'staffData'));
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
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                'unique:users,mobile_number'
            ],
            'password' => 'required|string|min:6',
            'department_id' => 'nullable|exists:staff_departments,id',
            'staff_role' => 'required|in:manager,supervisor,staff',
            'job_title' => 'nullable|string|max:255',
            'reports_to' => [
                'nullable',
                'exists:staff_members,id',
                'required_if:staff_role,supervisor'
            ],
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'join_date' => 'required|date',
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'emergency_contact' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'notes' => 'nullable|string',
        ], [
            'mobile_number.regex' => 'Mobile number must be exactly 10 digits.',
            'mobile_number.unique' => 'This mobile number is already registered.',
            'phone.regex' => 'Phone number must be 10-15 digits.',
            'emergency_contact.regex' => 'Emergency contact must be 10-15 digits.',
            'reports_to.required_if' => 'Supervisors must report to a Manager.',
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
    public function show(StaffMember $staff)
    {
        // Check if staff member exists
        if (!$staff->exists) {
            abort(404, 'Staff member not found');
        }
        
        // Eager load relationships for authorization check
        $staff->loadMissing(['property', 'user']);
        
        // Owner authorization: check if staff belongs to owner's properties
        if (auth()->user()->isOwner()) {
            $ownerPropertyIds = auth()->user()->properties()->pluck('id')->toArray();
            
            if (!in_array($staff->property_id, $ownerPropertyIds)) {
                abort(403, 'This staff member does not belong to any of your properties.');
            }
        } else {
            // For non-owners (managers, supervisors), use the policy
            $this->authorize('view', $staff);
        }
        
        $staff->load([
            'user',
            'department',
            'supervisor.user',
            'subordinates.user',
            'assignedTasks' => fn($q) => $q->latest()->limit(10),
            'attendance' => fn($q) => $q->latest()->limit(10),
            'leaveRequests' => fn($q) => $q->latest()->limit(5),
        ]);

        // Calculate stats
        $stats = [
            'total_tasks' => $staff->assignedTasks()->count(),
            'completed_tasks' => $staff->assignedTasks()->whereIn('status', ['completed', 'verified'])->count(),
            'pending_tasks' => $staff->assignedTasks()->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completion_rate' => $staff->getTaskCompletionRate(30),
            'subordinates_count' => $staff->subordinates()->count(),
        ];

        return view('staff.owner.show', compact('staff', 'stats'));
    }

    /**
     * Show the form for editing the staff member.
     */
    public function edit(StaffMember $staff)
    {
        // Check if staff member exists
        if (!$staff->exists) {
            abort(404, 'Staff member not found');
        }
        
        // Eager load property for authorization
        $staff->loadMissing(['property', 'user']);
        
        // Owner authorization: check if staff belongs to owner's properties
        if (auth()->user()->isOwner()) {
            $ownerPropertyIds = auth()->user()->properties()->pluck('id')->toArray();
            
            if (!in_array($staff->property_id, $ownerPropertyIds)) {
                abort(403, 'This staff member does not belong to any of your properties.');
            }
        } else {
            // For non-owners, use the policy
            $this->authorize('update', $staff);
        }
        
        $properties = auth()->user()->properties()->get();
        $departments = StaffDepartment::active()->get();
        
        // Get all staff members who can be supervisors (based on hierarchy rules)
        $allStaff = StaffMember::whereIn('property_id', $properties->pluck('id'))
            ->where('id', '!=', $staff->id)
            ->where('status', 'active')
            ->with('user', 'property', 'department')
            ->orderBy('property_id')
            ->orderByRaw("FIELD(staff_role, 'manager', 'supervisor', 'staff')")
            ->get();
        
        // Prepare staff data for JavaScript
        $staffData = $allStaff->map(function($staffMember) {
            return [
                'id' => $staffMember->id,
                'name' => $staffMember->user->name,
                'role' => $staffMember->staff_role,
                'property_id' => $staffMember->property_id,
                'department' => $staffMember->department ? $staffMember->department->name : 'No Dept',
                'job_title' => $staffMember->job_title ?? ucfirst($staffMember->staff_role),
            ];
        });
        
        $staff->load('user', 'department', 'supervisor');
        
        return view('staff.owner.edit', compact('staff', 'properties', 'departments', 'staffData'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, StaffMember $staff)
    {
        // Eager load property for authorization
        $staff->loadMissing('property');
        
        // Owner authorization
        if (auth()->user()->isOwner()) {
            $ownerPropertyIds = auth()->user()->properties()->pluck('id')->toArray();
            
            if (!in_array($staff->property_id, $ownerPropertyIds)) {
                abort(403, 'This staff member does not belong to any of your properties.');
            }
        } else {
            $this->authorize('update', $staff);
        }
        
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'department_id' => 'nullable|exists:staff_departments,id',
            'staff_role' => 'required|in:manager,supervisor,staff',
            'job_title' => 'nullable|string|max:255',
            'reports_to' => [
                'nullable',
                'exists:staff_members,id',
                'required_if:staff_role,supervisor'
            ],
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'status' => 'required|in:active,inactive,on_leave,suspended',
            'join_date' => 'required|date',
            'end_date' => 'nullable|date|after:join_date',
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'emergency_contact' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'notes' => 'nullable|string',
        ], [
            'phone.regex' => 'Phone number must be 10-15 digits.',
            'emergency_contact.regex' => 'Emergency contact must be 10-15 digits.',
            'reports_to.required_if' => 'Supervisors must report to a Manager.',
        ]);

        $staff->update($validated);

        return redirect()->route('owner.staff.show', $staff)
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(StaffMember $staff)
    {
        $this->authorize('delete', $staff);
        
        $staff->delete();

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
