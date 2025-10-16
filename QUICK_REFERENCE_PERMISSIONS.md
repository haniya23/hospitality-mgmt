# Staff Permissions - Quick Reference Card

## 🎯 Hierarchy at a Glance

| Role | Access Level | Can Manage | Special Powers |
|------|-------------|------------|----------------|
| 👔 **Manager** | Everything in property | All staff | Manage permissions, Financial reports, Delete records |
| 👷 **Supervisor** | Team data | Direct reports | Assign tasks, Verify tasks, Team reports |
| 👤 **Staff** | Own data only | Nobody | Complete tasks, Basic entry |

---

## 🔐 Default Permissions Matrix

| Feature | Manager | Supervisor | Staff |
|---------|---------|------------|-------|
| View Reservations | ✅ | ✅ | ✅ |
| Create Reservations | ✅ | ✅ | ✅ |
| Edit Reservations | ✅ | ✅ | ❌ |
| Delete Reservations | ✅ | ❌ | ❌ |
| View Guests | ✅ | ✅ | ✅ |
| Create Guests | ✅ | ✅ | ✅ |
| Edit Guests | ✅ | ✅ | ❌ |
| Delete Guests | ✅ | ❌ | ❌ |
| View Properties | ✅ | ✅ | ✅ |
| Edit Properties | ✅ | ❌ | ❌ |
| View Payments | ✅ | ✅ | ❌ |
| Create Payments | ✅ | ✅ | ❌ |
| View Invoices | ✅ | ✅ | ❌ |
| View Tasks | ✅ | ✅ | ✅ |
| Create Tasks | ✅ | ✅ | ❌ |
| Assign Tasks | ✅ | ✅ | ❌ |
| Verify Tasks | ✅ | ✅ | ❌ |
| View Staff | ✅ | ✅ | ❌ |
| Manage Staff | ✅ | ❌ | ❌ |
| View Reports | ✅ | ✅ | ❌ |
| Financial Reports | ✅ | ❌ | ❌ |
| Manage Permissions | ✅ | ❌ | ❌ |

---

## 🚀 Quick Actions

### As Manager
```
1. Go to Manager Dashboard
2. Click "Access Management"
3. Choose staff member
4. Edit Access / Reset / Revoke
```

### As Supervisor
```
1. Go to Supervisor Dashboard
2. View "My Team"
3. Assign tasks to direct reports
4. Verify completed tasks
```

### As Staff
```
1. Go to Staff Dashboard
2. View "My Tasks"
3. Complete assigned tasks
4. Submit attendance
```

---

## 💻 Quick Code Examples

### Blade Template
```blade
@staffCan('can_view_payments')
    <a href="/payments">Payments</a>
@endstaffCan

@isManager
    <button>Manager Only</button>
@endisManager
```

### Controller
```php
if (!$staff->hasPermission('can_edit_reservations')) {
    abort(403, 'No permission');
}
```

### Route
```php
Route::get('/payments', [Controller::class, 'index'])
    ->middleware('staff.permission:can_view_payments');
```

---

## 📱 Access URLs

| Page | URL | Who Can Access |
|------|-----|----------------|
| Access Management | `/staff/permissions` | Managers only |
| Manager Dashboard | `/manager/dashboard` | Managers only |
| Supervisor Dashboard | `/supervisor/dashboard` | Supervisors & Managers |
| Staff Dashboard | `/staff/dashboard` | All staff |

---

## 🛠️ Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't access feature | Check permissions in Access Management |
| Can't see staff | Check hierarchy - you can only see subordinates |
| Permission denied | Contact your manager |
| Can't edit permissions | Only managers can manage permissions |

---

## 📋 All Available Permissions

```
✓ can_view_reservations
✓ can_create_reservations
✓ can_edit_reservations
✓ can_delete_reservations
✓ can_view_guests
✓ can_create_guests
✓ can_edit_guests
✓ can_delete_guests
✓ can_view_properties
✓ can_edit_properties
✓ can_view_accommodations
✓ can_edit_accommodations
✓ can_view_payments
✓ can_create_payments
✓ can_edit_payments
✓ can_view_invoices
✓ can_create_invoices
✓ can_view_tasks
✓ can_create_tasks
✓ can_edit_tasks
✓ can_delete_tasks
✓ can_assign_tasks
✓ can_verify_tasks
✓ can_view_staff
✓ can_create_staff
✓ can_edit_staff
✓ can_delete_staff
✓ can_view_reports
✓ can_view_financial_reports
✓ can_manage_permissions
```

---

## 🔍 Audit Tracking

All these tables now track `created_by` and `updated_by`:
- Reservations
- Guests
- Properties
- Accommodations
- Payments
- Invoices
- Tasks
- Check-ins/Check-outs
- Staff records
- And more...

---

## 📚 Documentation Files

1. **STAFF_ACCESS_CONTROL_GUIDE.md** - Complete user guide
2. **PERMISSIONS_USAGE_EXAMPLES.md** - Code examples
3. **STAFF_ACCESS_IMPLEMENTATION_SUMMARY.md** - Implementation overview
4. **QUICK_REFERENCE_PERMISSIONS.md** - This file

---

**Print this card for easy reference!**  
**Last Updated:** October 12, 2025



