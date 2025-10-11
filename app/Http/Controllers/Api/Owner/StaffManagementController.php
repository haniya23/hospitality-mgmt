<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\StaffDepartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffManagementController extends Controller
{
    /**
     * Get all staff members for owner's properties
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get owner's property IDs
        $propertyIds = $user->properties()->pluck('id');

        $query = StaffMember::whereIn('property_id', $propertyIds)
            ->with(['user', 'department', 'property', 'supervisor.user']);

        // Apply filters
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('staff_role')) {
            $query->where('staff_role', $request->staff_role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $staffMembers = $query->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'staff_members' => $staffMembers->map(function ($staff) {
                    return $this->formatStaff($staff);
                }),
                'pagination' => [
                    'current_page' => $staffMembers->currentPage(),
                    'last_page' => $staffMembers->lastPage(),
                    'per_page' => $staffMembers->perPage(),
                    'total' => $staffMembers->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Get specific staff member details
     */
    public function show(Request $request, $uuid)
    {
        $user = $request->user();
        $propertyIds = $user->properties()->pluck('id');

        $staffMember = StaffMember::where('uuid', $uuid)
            ->whereIn('property_id', $propertyIds)
            ->with([
                'user',
                'department',
                'property',
                'supervisor.user',
                'subordinates.user',
            ])
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found or does not belong to your properties',
            ], 404);
        }

        // Get stats
        $stats = [
            'total_tasks' => $staffMember->assignedTasks()->count(),
            'completed_tasks' => $staffMember->assignedTasks()
                ->whereIn('status', ['completed', 'verified'])
                ->count(),
            'pending_tasks' => $staffMember->assignedTasks()
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'completion_rate' => $staffMember->getTaskCompletionRate(30),
            'subordinates_count' => $staffMember->subordinates()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'staff' => $this->formatStaffDetailed($staffMember),
                'stats' => $stats,
            ],
        ], 200);
    }

    /**
     * Create new staff member
     */
    public function store(Request $request)
    {
        $request->validate([
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
            'reports_to' => 'nullable|exists:staff_members,id',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'emergency_contact' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'notes' => 'nullable|string',
        ]);

        // Verify property ownership
        $property = $request->user()->properties()->find($request->property_id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or does not belong to you',
            ], 403);
        }

        // Create user account
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Create staff member
        $staffMember = StaffMember::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'property_id' => $request->property_id,
            'department_id' => $request->department_id,
            'staff_role' => $request->staff_role,
            'job_title' => $request->job_title,
            'reports_to' => $request->reports_to,
            'employment_type' => $request->employment_type,
            'join_date' => $request->join_date,
            'phone' => $request->phone,
            'emergency_contact' => $request->emergency_contact,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        $staffMember->load(['user', 'department', 'property']);

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully',
            'data' => $this->formatStaff($staffMember),
        ], 201);
    }

    /**
     * Update staff member
     */
    public function update(Request $request, $uuid)
    {
        $user = $request->user();
        $propertyIds = $user->properties()->pluck('id');

        $staffMember = StaffMember::where('uuid', $uuid)
            ->whereIn('property_id', $propertyIds)
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        $request->validate([
            'property_id' => 'sometimes|exists:properties,id',
            'department_id' => 'nullable|exists:staff_departments,id',
            'staff_role' => 'sometimes|in:manager,supervisor,staff',
            'job_title' => 'nullable|string|max:255',
            'reports_to' => 'nullable|exists:staff_members,id',
            'employment_type' => 'sometimes|in:full_time,part_time,contract,temporary',
            'status' => 'sometimes|in:active,inactive,on_leave,suspended',
            'join_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:join_date',
            'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'emergency_contact' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'notes' => 'nullable|string',
        ]);

        $staffMember->update($request->only([
            'property_id',
            'department_id',
            'staff_role',
            'job_title',
            'reports_to',
            'employment_type',
            'status',
            'join_date',
            'end_date',
            'phone',
            'emergency_contact',
            'notes',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully',
            'data' => $this->formatStaff($staffMember->fresh(['user', 'department', 'property'])),
        ], 200);
    }

    /**
     * Delete staff member
     */
    public function destroy(Request $request, $uuid)
    {
        $user = $request->user();
        $propertyIds = $user->properties()->pluck('id');

        $staffMember = StaffMember::where('uuid', $uuid)
            ->whereIn('property_id', $propertyIds)
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        $staffMember->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully',
        ], 200);
    }

    /**
     * Get staff hierarchy for a property
     */
    public function hierarchy(Request $request, $propertyId)
    {
        $property = $request->user()->properties()->find($propertyId);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $managers = StaffMember::where('property_id', $property->id)
            ->where('staff_role', 'manager')
            ->with(['user', 'department', 'subordinates' => function ($q) {
                $q->with(['user', 'department', 'subordinates.user']);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'property' => [
                    'id' => $property->id,
                    'uuid' => $property->uuid,
                    'name' => $property->name,
                ],
                'hierarchy' => $managers->map(function ($manager) {
                    return [
                        'id' => $manager->id,
                        'uuid' => $manager->uuid,
                        'name' => $manager->user->name,
                        'job_title' => $manager->job_title,
                        'department' => $manager->department?->name,
                        'supervisors' => $manager->subordinates->where('staff_role', 'supervisor')->map(function ($supervisor) {
                            return [
                                'id' => $supervisor->id,
                                'uuid' => $supervisor->uuid,
                                'name' => $supervisor->user->name,
                                'job_title' => $supervisor->job_title,
                                'department' => $supervisor->department?->name,
                                'staff' => $supervisor->subordinates->map(function ($staff) {
                                    return [
                                        'id' => $staff->id,
                                        'uuid' => $staff->uuid,
                                        'name' => $staff->user->name,
                                        'job_title' => $staff->job_title,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Get available departments
     */
    public function departments(Request $request)
    {
        $departments = StaffDepartment::active()->get();

        return response()->json([
            'success' => true,
            'data' => $departments->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'uuid' => $dept->uuid,
                    'name' => $dept->name,
                    'description' => $dept->description,
                ];
            }),
        ], 200);
    }

    /**
     * Format staff for API response
     */
    private function formatStaff($staff)
    {
        return [
            'id' => $staff->id,
            'uuid' => $staff->uuid,
            'name' => $staff->user->name,
            'email' => $staff->user->email,
            'mobile_number' => $staff->user->mobile_number,
            'staff_role' => $staff->staff_role,
            'job_title' => $staff->job_title,
            'employment_type' => $staff->employment_type,
            'status' => $staff->status,
            'join_date' => $staff->join_date?->format('Y-m-d'),
            'phone' => $staff->phone,
            'department' => $staff->department ? [
                'id' => $staff->department->id,
                'name' => $staff->department->name,
            ] : null,
            'property' => [
                'id' => $staff->property->id,
                'uuid' => $staff->property->uuid,
                'name' => $staff->property->name,
            ],
            'supervisor' => $staff->supervisor ? [
                'id' => $staff->supervisor->id,
                'uuid' => $staff->supervisor->uuid,
                'name' => $staff->supervisor->user->name,
            ] : null,
        ];
    }

    /**
     * Format staff with detailed information
     */
    private function formatStaffDetailed($staff)
    {
        return [
            'id' => $staff->id,
            'uuid' => $staff->uuid,
            'name' => $staff->user->name,
            'email' => $staff->user->email,
            'mobile_number' => $staff->user->mobile_number,
            'staff_role' => $staff->staff_role,
            'job_title' => $staff->job_title,
            'employment_type' => $staff->employment_type,
            'status' => $staff->status,
            'join_date' => $staff->join_date?->format('Y-m-d'),
            'end_date' => $staff->end_date?->format('Y-m-d'),
            'phone' => $staff->phone,
            'emergency_contact' => $staff->emergency_contact,
            'notes' => $staff->notes,
            'department' => $staff->department ? [
                'id' => $staff->department->id,
                'uuid' => $staff->department->uuid,
                'name' => $staff->department->name,
                'description' => $staff->department->description,
            ] : null,
            'property' => [
                'id' => $staff->property->id,
                'uuid' => $staff->property->uuid,
                'name' => $staff->property->name,
            ],
            'supervisor' => $staff->supervisor ? [
                'id' => $staff->supervisor->id,
                'uuid' => $staff->supervisor->uuid,
                'name' => $staff->supervisor->user->name,
                'job_title' => $staff->supervisor->job_title,
            ] : null,
            'subordinates' => $staff->subordinates->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'uuid' => $sub->uuid,
                    'name' => $sub->user->name,
                    'staff_role' => $sub->staff_role,
                    'job_title' => $sub->job_title,
                ];
            }),
        ];
    }
}

