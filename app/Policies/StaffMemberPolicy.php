<?php

namespace App\Policies;

use App\Models\StaffMember;
use App\Models\User;

class StaffMemberPolicy
{
    /**
     * Determine whether the user can view any staff members.
     */
    public function viewAny(User $user): bool
    {
        // Owners can view their property's staff
        // Managers can view their property's staff
        return $user->isOwner() || $user->staffMember?->isManager();
    }

    /**
     * Determine whether the user can view the staff member.
     */
    public function view(User $user, StaffMember $staffMember): bool
    {
        // Owner can view all staff in their properties
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        // Manager can view staff in their property
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $staffMember->property_id;
        }

        // Supervisors can view their subordinates
        if ($user->staffMember?->isSupervisor()) {
            return $staffMember->reports_to === $user->staffMember->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create staff members.
     */
    public function create(User $user): bool
    {
        // Only owners and managers can create staff members
        return $user->isOwner() || $user->staffMember?->isManager();
    }

    /**
     * Determine whether the user can update the staff member.
     */
    public function update(User $user, StaffMember $staffMember): bool
    {
        // Owner can update all staff in their properties
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        // Manager can update staff in their property (but not themselves)
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $staffMember->property_id 
                && $user->staffMember->id !== $staffMember->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the staff member.
     */
    public function delete(User $user, StaffMember $staffMember): bool
    {
        // Only owners can delete staff members
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can assign tasks to this staff member.
     */
    public function assignTasks(User $user, StaffMember $staffMember): bool
    {
        // Owner can assign to anyone in their properties
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        // Manager can assign to supervisors and staff in their property
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $staffMember->property_id
                && !$staffMember->isManager();
        }

        // Supervisor can assign to their subordinates
        if ($user->staffMember?->isSupervisor()) {
            return $staffMember->reports_to === $user->staffMember->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage attendance for this staff member.
     */
    public function manageAttendance(User $user, StaffMember $staffMember): bool
    {
        // Owner can manage all staff attendance
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        // Manager can manage all staff attendance in their property
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $staffMember->property_id;
        }

        // Supervisors can view their subordinates' attendance
        if ($user->staffMember?->isSupervisor()) {
            return $staffMember->reports_to === $user->staffMember->id;
        }

        return false;
    }

    /**
     * Determine whether the user can review leave requests for this staff member.
     */
    public function reviewLeaveRequests(User $user, StaffMember $staffMember): bool
    {
        // Owner can review all leave requests
        if ($user->isOwner()) {
            return $staffMember->property->owner_id === $user->id;
        }

        // Manager can review all leave requests in their property
        if ($user->staffMember?->isManager()) {
            return $user->staffMember->property_id === $staffMember->property_id;
        }

        // Supervisors can review their subordinates' leave requests
        if ($user->staffMember?->isSupervisor()) {
            return $staffMember->reports_to === $user->staffMember->id;
        }

        return false;
    }
}
