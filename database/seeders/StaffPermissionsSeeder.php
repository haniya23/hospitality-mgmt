<?php

namespace Database\Seeders;

use App\Models\StaffMember;
use App\Models\StaffPermission;
use Illuminate\Database\Seeder;

class StaffPermissionsSeeder extends Seeder
{
    /**
     * Seed permissions for existing staff members who don't have any
     */
    public function run(): void
    {
        $staffMembers = StaffMember::doesntHave('permissions')->get();

        foreach ($staffMembers as $staff) {
            $defaultPermissions = StaffPermission::getDefaultPermissions($staff->staff_role);
            
            StaffPermission::create(array_merge(
                ['staff_member_id' => $staff->id],
                $defaultPermissions
            ));
        }

        $this->command->info('Seeded permissions for ' . $staffMembers->count() . ' staff members.');
    }
}



