<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_assignment_id',
        'permission_key',
        'is_granted',
        'restrictions',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
        'restrictions' => 'array',
    ];

    // Permission keys constants
    const PERMISSIONS = [
        // Booking & Guest Management
        'view_bookings' => 'View upcoming bookings calendar',
        'create_bookings' => 'Create new bookings and reservations',
        'edit_bookings' => 'Edit existing bookings and reservations',
        'cancel_bookings' => 'Cancel bookings and reservations',
        'view_guest_details' => 'View guest profiles and information',
        'update_guest_services' => 'Mark guest service tasks as completed',
        
        // Task Management
        'view_assigned_tasks' => 'View assigned daily tasks',
        'update_task_status' => 'Update task progress and status',
        'upload_task_photos' => 'Upload proof of completion photos',
        
        // Cleaning & Maintenance
        'access_cleaning_checklists' => 'Access cleaning checklists',
        'execute_checklists' => 'Execute cleaning checklists',
        'update_checklist_progress' => 'Update checklist item completion',
        
        // Communication
        'receive_notifications' => 'Receive notifications from owner',
        'add_task_notes' => 'Add notes and remarks to tasks',
        'report_issues' => 'Report damages or issues',
        
        // Reporting
        'view_activity_logs' => 'View own activity logs',
        'generate_completion_reports' => 'Generate task completion reports',
    ];

    public function staffAssignment()
    {
        return $this->belongsTo(StaffAssignment::class);
    }

    // Scopes
    public function scopeGranted($query)
    {
        return $query->where('is_granted', true);
    }

    public function scopeDenied($query)
    {
        return $query->where('is_granted', false);
    }

    public function scopeByKey($query, $permissionKey)
    {
        return $query->where('permission_key', $permissionKey);
    }

    // Helper methods
    public function hasRestriction($key)
    {
        return isset($this->restrictions[$key]);
    }

    public function getRestriction($key, $default = null)
    {
        return $this->restrictions[$key] ?? $default;
    }

    public function setRestriction($key, $value)
    {
        $restrictions = $this->restrictions ?? [];
        $restrictions[$key] = $value;
        $this->update(['restrictions' => $restrictions]);
    }

    // Static methods for permission management
    public static function grantPermission($staffAssignmentId, $permissionKey, $restrictions = [])
    {
        return self::updateOrCreate(
            [
                'staff_assignment_id' => $staffAssignmentId,
                'permission_key' => $permissionKey,
            ],
            [
                'is_granted' => true,
                'restrictions' => $restrictions,
            ]
        );
    }

    public static function denyPermission($staffAssignmentId, $permissionKey)
    {
        return self::updateOrCreate(
            [
                'staff_assignment_id' => $staffAssignmentId,
                'permission_key' => $permissionKey,
            ],
            [
                'is_granted' => false,
            ]
        );
    }

    public static function hasPermission($staffAssignmentId, $permissionKey)
    {
        $permission = self::where('staff_assignment_id', $staffAssignmentId)
                         ->where('permission_key', $permissionKey)
                         ->first();
        
        return $permission ? $permission->is_granted : false;
    }

    public static function getStaffPermissions($staffAssignmentId)
    {
        return self::where('staff_assignment_id', $staffAssignmentId)
                   ->where('is_granted', true)
                   ->pluck('permission_key')
                   ->toArray();
    }

    public static function createDefaultPermissions($staffAssignmentId)
    {
        $defaultPermissions = [
            'view_bookings',
            'view_guest_details',
            'update_guest_services',
            'view_assigned_tasks',
            'update_task_status',
            'upload_task_photos',
            'access_cleaning_checklists',
            'execute_checklists',
            'update_checklist_progress',
            'receive_notifications',
            'add_task_notes',
            'report_issues',
            'view_activity_logs',
        ];

        foreach ($defaultPermissions as $permission) {
            self::grantPermission($staffAssignmentId, $permission);
        }
    }

    public static function createCustomPermissions($staffAssignmentId, $permissions)
    {
        foreach ($permissions as $permissionKey => $isGranted) {
            if ($isGranted) {
                self::grantPermission($staffAssignmentId, $permissionKey);
            }
        }
    }
}
