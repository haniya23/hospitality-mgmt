<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\StaffPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display list of staff members with their permissions
     */
    public function index()
    {
        $currentStaff = auth()->user()->staffMember;
        
        // Only managers can view permissions management
        if (!$currentStaff->isManager()) {
            abort(403, 'Only managers can manage permissions.');
        }

        // Get all accessible staff members based on hierarchy
        $staffMembers = $currentStaff->getAccessibleStaff()
            ->load(['permissions.lastUpdatedBy', 'user', 'department', 'supervisor']);

        return view('staff.permissions.index', compact('staffMembers', 'currentStaff'));
    }

    /**
     * Show permission edit form
     */
    public function edit(StaffMember $staffMember)
    {
        $currentStaff = auth()->user()->staffMember;
        
        // Check if current staff can manage this staff member
        if (!$currentStaff->canManage($staffMember) && !$currentStaff->isManager()) {
            abort(403, 'You do not have permission to manage this staff member.');
        }

        // Load or create permissions
        if (!$staffMember->permissions) {
            $defaultPermissions = StaffPermission::getDefaultPermissions($staffMember->staff_role);
            $staffMember->permissions = StaffPermission::create(array_merge(
                ['staff_member_id' => $staffMember->id],
                $defaultPermissions
            ));
        }

        // Load the last updated by relationship
        $staffMember->load('permissions.lastUpdatedBy');

        // Get all permissions grouped by category
        $permissionGroups = [
            'Reservations' => [
                'can_view_reservations' => 'View Reservations',
                'can_create_reservations' => 'Create Reservations',
                'can_edit_reservations' => 'Edit Reservations',
                'can_delete_reservations' => 'Delete Reservations',
            ],
            'Guests' => [
                'can_view_guests' => 'View Guests',
                'can_create_guests' => 'Create Guests',
                'can_edit_guests' => 'Edit Guests',
                'can_delete_guests' => 'Delete Guests',
            ],
            'Properties & Accommodations' => [
                'can_view_properties' => 'View Properties',
                'can_edit_properties' => 'Edit Properties',
                'can_view_accommodations' => 'View Accommodations',
                'can_edit_accommodations' => 'Edit Accommodations',
            ],
            'Payments & Invoices' => [
                'can_view_payments' => 'View Payments',
                'can_create_payments' => 'Create Payments',
                'can_edit_payments' => 'Edit Payments',
                'can_view_invoices' => 'View Invoices',
                'can_create_invoices' => 'Create Invoices',
            ],
            'Tasks' => [
                'can_view_tasks' => 'View Tasks',
                'can_create_tasks' => 'Create Tasks',
                'can_edit_tasks' => 'Edit Tasks',
                'can_delete_tasks' => 'Delete Tasks',
                'can_assign_tasks' => 'Assign Tasks',
                'can_verify_tasks' => 'Verify Tasks',
            ],
            'Staff Management' => [
                'can_view_staff' => 'View Staff',
                'can_create_staff' => 'Create Staff',
                'can_edit_staff' => 'Edit Staff',
                'can_delete_staff' => 'Delete Staff',
            ],
            'Reports & Analytics' => [
                'can_view_reports' => 'View Reports',
                'can_view_financial_reports' => 'View Financial Reports',
            ],
            'System' => [
                'can_manage_permissions' => 'Manage Permissions',
            ],
        ];

        return view('staff.permissions.edit', compact('staffMember', 'permissionGroups', 'currentStaff'));
    }

    /**
     * Update staff member permissions
     */
    public function update(Request $request, StaffMember $staffMember)
    {
        $currentStaff = auth()->user()->staffMember;
        
        // Check if current staff can manage this staff member
        if (!$currentStaff->canManage($staffMember) && !$currentStaff->isManager()) {
            abort(403, 'You do not have permission to manage this staff member.');
        }

        // Get all permission fields
        $permissionFields = [
            'can_view_reservations',
            'can_create_reservations',
            'can_edit_reservations',
            'can_delete_reservations',
            'can_view_guests',
            'can_create_guests',
            'can_edit_guests',
            'can_delete_guests',
            'can_view_properties',
            'can_edit_properties',
            'can_view_accommodations',
            'can_edit_accommodations',
            'can_view_payments',
            'can_create_payments',
            'can_edit_payments',
            'can_view_invoices',
            'can_create_invoices',
            'can_view_tasks',
            'can_create_tasks',
            'can_edit_tasks',
            'can_delete_tasks',
            'can_assign_tasks',
            'can_verify_tasks',
            'can_view_staff',
            'can_create_staff',
            'can_edit_staff',
            'can_delete_staff',
            'can_view_reports',
            'can_view_financial_reports',
            'can_manage_permissions',
        ];

        // Build update data (checkboxes not checked will be false)
        $updateData = [];
        foreach ($permissionFields as $field) {
            $updateData[$field] = $request->has($field);
        }

        // Update or create permissions
        if ($staffMember->permissions) {
            $staffMember->permissions->update(array_merge($updateData, [
                'last_updated_by' => auth()->id(),
                'last_updated_at' => now(),
            ]));
        } else {
            StaffPermission::create(array_merge(
                ['staff_member_id' => $staffMember->id],
                $updateData,
                [
                    'last_updated_by' => auth()->id(),
                    'last_updated_at' => now(),
                ]
            ));
        }

        return redirect()->route('staff.permissions.index')
            ->with('success', 'Permissions updated successfully for ' . $staffMember->user->name);
    }

    /**
     * Reset permissions to default based on role
     */
    public function resetToDefault(StaffMember $staffMember)
    {
        $currentStaff = auth()->user()->staffMember;
        
        // Check if current staff can manage this staff member
        if (!$currentStaff->canManage($staffMember) && !$currentStaff->isManager()) {
            abort(403, 'You do not have permission to manage this staff member.');
        }

        $defaultPermissions = StaffPermission::getDefaultPermissions($staffMember->staff_role);
        
        if ($staffMember->permissions) {
            $staffMember->permissions->update(array_merge($defaultPermissions, [
                'last_updated_by' => auth()->id(),
                'last_updated_at' => now(),
            ]));
        } else {
            StaffPermission::create(array_merge(
                ['staff_member_id' => $staffMember->id],
                $defaultPermissions,
                [
                    'last_updated_by' => auth()->id(),
                    'last_updated_at' => now(),
                ]
            ));
        }

        return redirect()->back()
            ->with('success', 'Permissions reset to default for ' . $staffMember->user->name);
    }

    /**
     * Revoke all permissions
     */
    public function revokeAll(StaffMember $staffMember)
    {
        $currentStaff = auth()->user()->staffMember;
        
        // Check if current staff can manage this staff member
        if (!$currentStaff->canManage($staffMember) && !$currentStaff->isManager()) {
            abort(403, 'You do not have permission to manage this staff member.');
        }

        if ($staffMember->permissions) {
            // Set all permissions to false
            $allFalse = array_fill_keys([
                'can_view_reservations', 'can_create_reservations', 'can_edit_reservations', 'can_delete_reservations',
                'can_view_guests', 'can_create_guests', 'can_edit_guests', 'can_delete_guests',
                'can_view_properties', 'can_edit_properties', 'can_view_accommodations', 'can_edit_accommodations',
                'can_view_payments', 'can_create_payments', 'can_edit_payments', 'can_view_invoices', 'can_create_invoices',
                'can_view_tasks', 'can_create_tasks', 'can_edit_tasks', 'can_delete_tasks', 'can_assign_tasks', 'can_verify_tasks',
                'can_view_staff', 'can_create_staff', 'can_edit_staff', 'can_delete_staff',
                'can_view_reports', 'can_view_financial_reports', 'can_manage_permissions',
            ], false);
            
            $staffMember->permissions->update(array_merge($allFalse, [
                'last_updated_by' => auth()->id(),
                'last_updated_at' => now(),
            ]));
        }

        return redirect()->back()
            ->with('success', 'All permissions revoked for ' . $staffMember->user->name);
    }
}

