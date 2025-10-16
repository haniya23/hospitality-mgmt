# Staff Access Control System Guide

## Overview
This system implements a simple, hierarchy-based access control for staff members. It tracks who creates and updates data, and controls what each staff member can see and do based on their role and individual permissions.

## Hierarchy

### 1. **Manager** (Highest Level)
- **Full Access**: Managers have all permissions by default
- **Can Manage**: All staff in their property
- **Can See**: All data in their property
- **Special Powers**:
  - Assign/revoke permissions for any staff member
  - View financial reports
  - Edit property settings
  - Delete records
  - Manage all staff members

### 2. **Supervisor** (Middle Level)
- **Default Access**: 
  - Create and edit reservations
  - Manage guests
  - Create and assign tasks
  - Verify completed tasks
  - View reports (non-financial)
- **Can Manage**: Their direct reports only
- **Can See**: Their own data and their team's data
- **Cannot Do**:
  - Delete critical records
  - Edit properties
  - View financial reports
  - Manage permissions

### 3. **Staff** (Basic Level)
- **Default Access**:
  - View reservations and guests
  - Create new bookings
  - Complete assigned tasks
  - Submit their own attendance
- **Can See**: Only their own data
- **Cannot Do**:
  - Edit or delete anything
  - Assign tasks
  - View other staff members
  - Access financial information

## Key Features

### 1. Automatic Permission Assignment
When a new staff member is created, they automatically get default permissions based on their role:
- Manager → Full access
- Supervisor → Team management access
- Staff → Basic read/create access

### 2. Custom Permission Management
Managers can customize permissions for individual staff members:
- Go to Manager Dashboard → "Access Management"
- Click "Edit Access" next to any staff member
- Check/uncheck permissions as needed
- Click "Save Permissions"

### 3. Permission Categories

#### Reservations
- View, Create, Edit, Delete reservations

#### Guests
- View, Create, Edit, Delete guest records

#### Properties & Accommodations
- View, Edit property and accommodation details

#### Payments & Invoices
- View, Create, Edit payments
- View, Create invoices

#### Tasks
- View, Create, Edit, Delete tasks
- Assign tasks to others
- Verify completed tasks

#### Staff Management
- View, Create, Edit, Delete staff members

#### Reports
- View general reports
- View financial reports (managers only)

#### System
- Manage permissions (managers only)

### 4. Data Tracking
All important tables now track:
- **created_by**: Who created the record
- **updated_by**: Who last updated the record
- **created_at**: When it was created
- **updated_at**: When it was last updated

This provides full audit trails for accountability.

## How to Use

### For Property Owners
1. Create staff members with appropriate roles
2. Let managers handle permission assignments
3. Review audit logs to see who made changes

### For Managers
1. Access "Access Management" from your dashboard
2. View all staff members in your property
3. Edit individual permissions as needed
4. Use quick actions:
   - **Edit Access**: Customize specific permissions
   - **Reset**: Restore default permissions for their role
   - **Revoke All**: Emergency - remove all access

### For Supervisors
1. You can only see and manage your direct reports
2. Assign tasks to your team members
3. Verify completed tasks
4. You cannot change permissions

### For Staff
1. You only see your own tasks and data
2. Complete assigned tasks
3. Submit attendance and leave requests
4. Create new bookings if permitted

## Permission Checking

### In Views (Blade Templates)
```php
@if(auth()->user()->staffMember->hasPermission('can_view_payments'))
    <!-- Show payment information -->
@endif
```

### In Controllers
```php
if (!$staff->hasPermission('can_edit_reservations')) {
    abort(403, 'You do not have permission to edit reservations.');
}
```

### Using Middleware
```php
Route::get('/payments', [PaymentController::class, 'index'])
    ->middleware(['auth', 'staff.permission:can_view_payments']);
```

## Database Tables

### staff_permissions
Stores individual permissions for each staff member.

### All Main Tables
Now include:
- `created_by` (user_id)
- `updated_by` (user_id)

Tables updated:
- reservations
- guests
- properties
- property_accommodations
- payments
- invoices
- tasks
- task_logs
- check_ins
- check_outs
- staff_members
- staff_attendance
- staff_leave_requests
- staff_performance_reviews
- maintenance_tickets
- property_photos
- pricing_rules

## Best Practices

1. **Start with Default Permissions**
   - Let the system assign defaults based on role
   - Only customize when necessary

2. **Regular Reviews**
   - Managers should review staff permissions quarterly
   - Remove access for inactive staff immediately

3. **Clear Hierarchy**
   - Keep reporting structure clear
   - Supervisors should have 3-7 direct reports max

4. **Document Changes**
   - Note why custom permissions were assigned
   - Review changes in performance evaluations

5. **Security**
   - Never share login credentials
   - Report suspicious activity immediately
   - Managers: Use "Revoke All" for emergency suspension

## Troubleshooting

### Staff Can't Access Something
1. Check their role (Manager/Supervisor/Staff)
2. Check their individual permissions (Access Management)
3. Verify their status is "Active"
4. Check if they report to the right person

### Can't Change Permissions
- Only managers can manage permissions
- You must be in the same property
- Staff member must be your subordinate or below

### Missing Data
- Staff only see data they have permission for
- Check hierarchy - staff see only their data
- Supervisors see their team's data
- Managers see everything in their property

## Routes

- Access Management: `/staff/permissions`
- Edit Staff Access: `/staff/permissions/{staff}/edit`
- Manager Dashboard: `/manager/dashboard`
- Supervisor Dashboard: `/supervisor/dashboard`
- Staff Dashboard: `/staff/dashboard`

## Support

For issues or questions:
1. Contact your property manager
2. Managers contact property owner
3. Technical issues: Check system logs

---

**System implemented on:** October 12, 2025
**Version:** 1.0



