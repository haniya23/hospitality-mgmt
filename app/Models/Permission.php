<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'module',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Permission constants organized by module
    const PERMISSIONS = [
        'booking_management' => [
            'view_bookings' => 'View upcoming bookings calendar',
            'create_bookings' => 'Create new bookings',
            'edit_bookings' => 'Edit existing bookings',
            'cancel_bookings' => 'Cancel bookings',
            'view_booking_reports' => 'View booking reports and analytics',
        ],
        'guest_services' => [
            'view_guest_details' => 'View guest profiles and information',
            'update_guest_services' => 'Mark guest service tasks as completed',
            'manage_checkin_checkout' => 'Handle guest check-in and check-out',
            'manage_guest_requests' => 'Handle guest requests and complaints',
        ],
        'task_management' => [
            'view_assigned_tasks' => 'View assigned daily tasks',
            'update_task_status' => 'Update task progress and status',
            'upload_task_photos' => 'Upload proof of completion photos',
            'create_tasks' => 'Create new tasks',
            'assign_tasks' => 'Assign tasks to other staff',
        ],
        'cleaning_maintenance' => [
            'access_cleaning_checklists' => 'Access cleaning checklists',
            'execute_checklists' => 'Execute cleaning checklists',
            'update_checklist_progress' => 'Update checklist item completion',
            'manage_room_preparation' => 'Manage room preparation tasks',
            'manage_maintenance' => 'Manage maintenance tasks',
            'manage_repairs' => 'Manage repair tasks',
        ],
        'staff_management' => [
            'manage_staff' => 'Manage staff assignments and roles',
            'view_staff_reports' => 'View staff performance reports',
            'manage_staff_schedules' => 'Manage staff schedules',
            'approve_leave_requests' => 'Approve staff leave requests',
        ],
        'property_management' => [
            'manage_property' => 'Manage property settings and details',
            'manage_accommodations' => 'Manage property accommodations',
            'manage_pricing' => 'Manage pricing and rates',
            'manage_amenities' => 'Manage property amenities',
        ],
        'financial_management' => [
            'manage_finances' => 'Manage financial operations',
            'view_financial_reports' => 'View financial reports',
            'manage_payments' => 'Manage payments and invoices',
            'manage_commissions' => 'Manage commissions and partnerships',
        ],
        'communication' => [
            'receive_notifications' => 'Receive notifications from owner',
            'add_task_notes' => 'Add notes and remarks to tasks',
            'report_issues' => 'Report damages or issues',
            'send_notifications' => 'Send notifications to staff',
        ],
        'reporting' => [
            'view_activity_logs' => 'View activity logs',
            'generate_completion_reports' => 'Generate task completion reports',
            'view_reports' => 'View various system reports',
            'export_data' => 'Export data and reports',
        ],
        'security' => [
            'manage_security' => 'Manage security operations',
            'monitor_property' => 'Monitor property security',
            'access_cameras' => 'Access security cameras',
        ],
        'administration' => [
            'basic_administration' => 'Basic administrative tasks',
            'manage_settings' => 'Manage system settings',
            'backup_data' => 'Backup and restore data',
        ],
    ];

    public static function createDefaultPermissions()
    {
        foreach (self::PERMISSIONS as $module => $permissions) {
            foreach ($permissions as $name => $description) {
                self::firstOrCreate(
                    ['name' => $name],
                    [
                        'description' => $description,
                        'module' => $module,
                    ]
                );
            }
        }
    }

    public static function getPermissionsByModule($module = null)
    {
        if ($module) {
            return self::where('module', $module)->get();
        }
        
        return self::all()->groupBy('module');
    }
}
