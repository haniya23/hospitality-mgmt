<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StaffAssignment;
use App\Models\StaffPermission;
use App\Models\Role;

class GrantManagerBookingPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:grant-manager-booking-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant booking creation permissions to staff with Manager roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Granting booking creation permissions to Manager role staff...');

        // Get all active staff assignments with Manager role
        $managerAssignments = StaffAssignment::whereHas('role', function($query) {
            $query->where('name', 'Manager');
        })->where('status', 'active')->get();

        $grantedCount = 0;

        foreach ($managerAssignments as $assignment) {
            // Check if they already have the permission
            $hasPermission = StaffPermission::where('staff_assignment_id', $assignment->id)
                ->where('permission_key', 'create_bookings')
                ->where('is_granted', true)
                ->exists();

            if (!$hasPermission) {
                // Grant the permission
                StaffPermission::grantPermission($assignment->id, 'create_bookings');
                $grantedCount++;
                
                $this->line("✓ Granted create_bookings permission to {$assignment->user->name} ({$assignment->property->name})");
            } else {
                $this->line("- {$assignment->user->name} already has create_bookings permission");
            }
        }

        $this->info("✅ Successfully granted booking creation permissions to {$grantedCount} manager(s).");
        
        return Command::SUCCESS;
    }
}
