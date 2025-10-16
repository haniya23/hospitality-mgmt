<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
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
        'last_updated_by',
        'last_updated_at',
    ];

    protected $casts = [
        'can_view_reservations' => 'boolean',
        'can_create_reservations' => 'boolean',
        'can_edit_reservations' => 'boolean',
        'can_delete_reservations' => 'boolean',
        'can_view_guests' => 'boolean',
        'can_create_guests' => 'boolean',
        'can_edit_guests' => 'boolean',
        'can_delete_guests' => 'boolean',
        'can_view_properties' => 'boolean',
        'can_edit_properties' => 'boolean',
        'can_view_accommodations' => 'boolean',
        'can_edit_accommodations' => 'boolean',
        'can_view_payments' => 'boolean',
        'can_create_payments' => 'boolean',
        'can_edit_payments' => 'boolean',
        'can_view_invoices' => 'boolean',
        'can_create_invoices' => 'boolean',
        'can_view_tasks' => 'boolean',
        'can_create_tasks' => 'boolean',
        'can_edit_tasks' => 'boolean',
        'can_delete_tasks' => 'boolean',
        'can_assign_tasks' => 'boolean',
        'can_verify_tasks' => 'boolean',
        'can_view_staff' => 'boolean',
        'can_create_staff' => 'boolean',
        'can_edit_staff' => 'boolean',
        'can_delete_staff' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_view_financial_reports' => 'boolean',
        'can_manage_permissions' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Get default permissions based on role
     */
    public static function getDefaultPermissions($role)
    {
        return match($role) {
            'manager' => [
                'can_view_reservations' => true,
                'can_create_reservations' => true,
                'can_edit_reservations' => true,
                'can_delete_reservations' => true,
                'can_view_guests' => true,
                'can_create_guests' => true,
                'can_edit_guests' => true,
                'can_delete_guests' => true,
                'can_view_properties' => true,
                'can_edit_properties' => true,
                'can_view_accommodations' => true,
                'can_edit_accommodations' => true,
                'can_view_payments' => true,
                'can_create_payments' => true,
                'can_edit_payments' => true,
                'can_view_invoices' => true,
                'can_create_invoices' => true,
                'can_view_tasks' => true,
                'can_create_tasks' => true,
                'can_edit_tasks' => true,
                'can_delete_tasks' => true,
                'can_assign_tasks' => true,
                'can_verify_tasks' => true,
                'can_view_staff' => true,
                'can_create_staff' => true,
                'can_edit_staff' => true,
                'can_delete_staff' => true,
                'can_view_reports' => true,
                'can_view_financial_reports' => true,
                'can_manage_permissions' => true,
            ],
            'supervisor' => [
                'can_view_reservations' => true,
                'can_create_reservations' => true,
                'can_edit_reservations' => true,
                'can_delete_reservations' => false,
                'can_view_guests' => true,
                'can_create_guests' => true,
                'can_edit_guests' => true,
                'can_delete_guests' => false,
                'can_view_properties' => true,
                'can_edit_properties' => false,
                'can_view_accommodations' => true,
                'can_edit_accommodations' => false,
                'can_view_payments' => true,
                'can_create_payments' => true,
                'can_edit_payments' => false,
                'can_view_invoices' => true,
                'can_create_invoices' => true,
                'can_view_tasks' => true,
                'can_create_tasks' => true,
                'can_edit_tasks' => true,
                'can_delete_tasks' => false,
                'can_assign_tasks' => true,
                'can_verify_tasks' => true,
                'can_view_staff' => true,
                'can_create_staff' => false,
                'can_edit_staff' => false,
                'can_delete_staff' => false,
                'can_view_reports' => true,
                'can_view_financial_reports' => false,
                'can_manage_permissions' => false,
            ],
            'staff' => [
                'can_view_reservations' => true,
                'can_create_reservations' => true,
                'can_edit_reservations' => false,
                'can_delete_reservations' => false,
                'can_view_guests' => true,
                'can_create_guests' => true,
                'can_edit_guests' => false,
                'can_delete_guests' => false,
                'can_view_properties' => true,
                'can_edit_properties' => false,
                'can_view_accommodations' => true,
                'can_edit_accommodations' => false,
                'can_view_payments' => false,
                'can_create_payments' => false,
                'can_edit_payments' => false,
                'can_view_invoices' => false,
                'can_create_invoices' => false,
                'can_view_tasks' => true,
                'can_create_tasks' => false,
                'can_edit_tasks' => true,
                'can_delete_tasks' => false,
                'can_assign_tasks' => false,
                'can_verify_tasks' => false,
                'can_view_staff' => false,
                'can_create_staff' => false,
                'can_edit_staff' => false,
                'can_delete_staff' => false,
                'can_view_reports' => false,
                'can_view_financial_reports' => false,
                'can_manage_permissions' => false,
            ],
            default => []
        };
    }
}

