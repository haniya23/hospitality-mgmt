<?php

namespace App\Observers;

use App\Models\StaffMember;
use App\Models\StaffPermission;

class StaffMemberObserver
{
    /**
     * Handle the StaffMember "created" event.
     */
    public function created(StaffMember $staffMember): void
    {
        // Automatically create permissions based on role
        $defaultPermissions = StaffPermission::getDefaultPermissions($staffMember->staff_role);
        
        StaffPermission::create(array_merge(
            ['staff_member_id' => $staffMember->id],
            $defaultPermissions
        ));
    }

    /**
     * Handle the StaffMember "updated" event.
     */
    public function updated(StaffMember $staffMember): void
    {
        // If role changed, update permissions to default for new role
        if ($staffMember->isDirty('staff_role')) {
            $defaultPermissions = StaffPermission::getDefaultPermissions($staffMember->staff_role);
            
            if ($staffMember->permissions) {
                $staffMember->permissions->update($defaultPermissions);
            } else {
                StaffPermission::create(array_merge(
                    ['staff_member_id' => $staffMember->id],
                    $defaultPermissions
                ));
            }
        }
    }
}


