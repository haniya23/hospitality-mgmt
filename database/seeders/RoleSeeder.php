<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Property;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create all default permissions
        Permission::createDefaultPermissions();

        // Get all properties to create roles for each
        $properties = Property::all();

        foreach ($properties as $property) {
            $this->createDefaultRoles($property);
        }
    }

    /**
     * Create default roles for a property
     */
    private function createDefaultRoles(Property $property): void
    {
        $defaultRoles = [
            [
                'name' => 'Manager',
                'description' => 'Property manager with full access to all operations and staff management',
                'permissions' => [
                    // Booking & Guest Management
                    'view_bookings', 'create_bookings', 'edit_bookings', 'cancel_bookings', 'view_booking_reports',
                    // Guest Services
                    'view_guest_details', 'update_guest_services', 'manage_checkin_checkout', 'manage_guest_requests',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos', 'create_tasks', 'assign_tasks',
                    // Cleaning & Maintenance
                    'access_cleaning_checklists', 'execute_checklists', 'update_checklist_progress', 'manage_room_preparation', 'manage_maintenance', 'manage_repairs',
                    // Staff Management
                    'manage_staff', 'view_staff_reports', 'manage_staff_schedules', 'approve_leave_requests',
                    // Property Management
                    'manage_property', 'manage_accommodations', 'manage_pricing', 'manage_amenities',
                    // Financial Management
                    'manage_finances', 'view_financial_reports', 'manage_payments', 'manage_commissions',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues', 'send_notifications',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports', 'view_reports', 'export_data',
                    // Security
                    'manage_security', 'monitor_property', 'access_cameras',
                    // Administration
                    'basic_administration', 'manage_settings', 'backup_data'
                ]
            ],
            [
                'name' => 'Supervisor',
                'description' => 'Supervisor with oversight responsibilities for daily operations',
                'permissions' => [
                    // Booking & Guest Management
                    'view_bookings', 'view_booking_reports',
                    // Guest Services
                    'view_guest_details', 'update_guest_services', 'manage_checkin_checkout', 'manage_guest_requests',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos', 'create_tasks', 'assign_tasks',
                    // Cleaning & Maintenance
                    'access_cleaning_checklists', 'execute_checklists', 'update_checklist_progress', 'manage_room_preparation', 'manage_maintenance', 'manage_repairs',
                    // Staff Management
                    'manage_staff', 'view_staff_reports', 'manage_staff_schedules',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues', 'send_notifications',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports', 'view_reports',
                    // Security
                    'manage_security', 'monitor_property',
                    // Administration
                    'basic_administration'
                ]
            ],
            [
                'name' => 'Housekeeping',
                'description' => 'Housekeeping staff responsible for cleaning and room preparation',
                'permissions' => [
                    // Guest Services
                    'view_guest_details', 'update_guest_services',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos',
                    // Cleaning & Maintenance
                    'access_cleaning_checklists', 'execute_checklists', 'update_checklist_progress', 'manage_room_preparation',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports'
                ]
            ],
            [
                'name' => 'Front Desk',
                'description' => 'Front desk staff handling guest check-in/out and guest services',
                'permissions' => [
                    // Booking & Guest Management
                    'view_bookings',
                    // Guest Services
                    'view_guest_details', 'update_guest_services', 'manage_checkin_checkout', 'manage_guest_requests',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports',
                    // Administration
                    'basic_administration'
                ]
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Maintenance staff responsible for property upkeep and repairs',
                'permissions' => [
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos',
                    // Cleaning & Maintenance
                    'manage_maintenance', 'manage_repairs',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports'
                ]
            ],
            [
                'name' => 'Security',
                'description' => 'Security staff responsible for property safety and guest security',
                'permissions' => [
                    // Guest Services
                    'view_guest_details', 'manage_guest_requests',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos',
                    // Security
                    'manage_security', 'monitor_property', 'access_cameras',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs', 'view_reports'
                ]
            ],
            [
                'name' => 'Receptionist',
                'description' => 'Receptionist handling guest inquiries and basic administrative tasks',
                'permissions' => [
                    // Booking & Guest Management
                    'view_bookings',
                    // Guest Services
                    'view_guest_details', 'manage_guest_requests',
                    // Task Management
                    'view_assigned_tasks', 'update_task_status',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs',
                    // Administration
                    'basic_administration'
                ]
            ],
            [
                'name' => 'Cleaner',
                'description' => 'Cleaning staff focused on room and common area maintenance',
                'permissions' => [
                    // Task Management
                    'view_assigned_tasks', 'update_task_status', 'upload_task_photos',
                    // Cleaning & Maintenance
                    'access_cleaning_checklists', 'execute_checklists', 'update_checklist_progress',
                    // Communication
                    'receive_notifications', 'add_task_notes', 'report_issues',
                    // Reporting
                    'view_activity_logs', 'generate_completion_reports'
                ]
            ]
        ];

        foreach ($defaultRoles as $roleData) {
            // Check if role already exists for this property
            $existingRole = Role::where('property_id', $property->id)
                ->where('name', $roleData['name'])
                ->first();

            if (!$existingRole) {
                $role = Role::create([
                    'property_id' => $property->id,
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_active' => true,
                ]);
            } else {
                $role = $existingRole;
                // Update description if it's different
                if ($role->description !== $roleData['description']) {
                    $role->update(['description' => $roleData['description']]);
                }
            }

            // Attach permissions to the role (this will handle both new and existing roles)
            $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
            $role->permissions()->sync($permissions->pluck('id'));
        }
    }

    /**
     * Create roles for a specific property (called when owner creates staff)
     */
    public static function createRolesForProperty(Property $property): void
    {
        $seeder = new self();
        $seeder->createDefaultRoles($property);
    }

    /**
     * Create a custom role for a property
     */
    public static function createCustomRole(Property $property, string $name, string $description, array $permissions = []): Role
    {
        $role = Role::create([
            'property_id' => $property->id,
            'name' => $name,
            'description' => $description,
            'is_active' => true,
        ]);

        // Attach permissions to the role
        if (!empty($permissions)) {
            $permissionModels = Permission::whereIn('name', $permissions)->get();
            $role->permissions()->attach($permissionModels);
        }

        return $role;
    }
}
