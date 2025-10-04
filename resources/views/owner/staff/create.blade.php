@extends('layouts.app')

@section('title', 'Add Staff Member')

@push('styles')
<style>
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .modern-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    /* Select2 Blue Theme */
    .select2-blue-theme .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    .select2-blue-container .select2-selection {
        border-radius: 0.75rem !important;
        border-width: 2px !important;
        border-color: #e5e7eb !important;
        height: 42px !important;
    }
    .select2-blue-container .select2-selection--focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    }
    .select2-container--open .select2-dropdown {
        border: 2px solid #3b82f6 !important;
        border-radius: 0.75rem !important;
        margin-top: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    .select2-results__option {
        padding: 0.75rem 1rem !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50" x-data="addStaff()">
    <!-- Header -->
    <header class="soft-header-gradient text-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                        <i class="fas fa-user-plus text-teal-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Add Staff Member</h1>
                        <p class="text-sm text-slate-700">Create a new staff account and assign permissions</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('owner.staff.index') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-arrow-left text-pink-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Back to Staff</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="modern-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-bold text-gray-900">Staff Information</h3>
                <p class="text-sm text-gray-600">Enter the staff member's basic information and assignment details.</p>
            </div>
            
            <form @submit.prevent="submitForm()" class="p-6 space-y-6">
                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                        <input x-model="form.name" type="text" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" placeholder="Enter full name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mobile Number *</label>
                        <input x-model="form.mobile_number" type="tel" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" placeholder="Enter mobile number">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PIN (4 digits) *</label>
                        <input x-model="form.pin" type="password" required maxlength="4" pattern="[0-9]{4}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" placeholder="Enter 4-digit PIN">
                        <p class="text-xs text-gray-500 mt-2">Staff will use this PIN to log in</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email (Optional)</label>
                        <input x-model="form.email" type="email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" placeholder="Enter email address">
                    </div>
                </div>

                <!-- Assignment Information -->
                <div class="border-t border-gray-200/50 pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Assignment Details</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Property *</label>
                            <select id="property_select" x-model="form.property_id" required @change="loadRoles()" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 select2-dropdown">
                                <option value="">Select Property</option>
                                <template x-for="property in properties" :key="property.id">
                                    <option :value="property.id" x-text="property.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
                            <select id="role_select" x-model="form.role_id" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 select2-dropdown">
                                <option value="">Select Role</option>
                                <template x-for="role in roles" :key="role.id">
                                    <option :value="role.id" x-text="role.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date *</label>
                            <input x-model="form.start_date" type="date" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">End Date (Optional)</label>
                            <input x-model="form.end_date" type="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800">
                            <p class="text-xs text-gray-500 mt-2">Leave empty for permanent assignment</p>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="border-t border-gray-200/50 pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h4>
                    <p class="text-sm text-gray-600 mb-6">Select what this staff member can access and do. You can modify these later.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Booking & Guest Management -->
                        <div class="modern-card rounded-xl p-6">
                            <h5 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                                Booking & Guest Management
                            </h5>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_bookings" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">View Bookings Calendar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_guest_details" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">View Guest Details</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_guest_services" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Update Guest Services</span>
                                </label>
                            </div>
                        </div>

                        <!-- Task Management -->
                        <div class="modern-card rounded-xl p-6">
                            <h5 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-tasks text-green-500 mr-2"></i>
                                Task Management
                            </h5>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_assigned_tasks" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">View Assigned Tasks</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_task_status" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Update Task Status</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.upload_task_photos" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Upload Task Photos</span>
                                </label>
                            </div>
                        </div>

                        <!-- Cleaning & Maintenance -->
                        <div class="modern-card rounded-xl p-6">
                            <h5 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-broom text-purple-500 mr-2"></i>
                                Cleaning & Maintenance
                            </h5>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.access_cleaning_checklists" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Access Cleaning Checklists</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.execute_checklists" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Execute Checklists</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_checklist_progress" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Update Checklist Progress</span>
                                </label>
                            </div>
                        </div>

                        <!-- Communication & Reporting -->
                        <div class="modern-card rounded-xl p-6">
                            <h5 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-comments text-orange-500 mr-2"></i>
                                Communication & Reporting
                            </h5>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.receive_notifications" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Receive Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.add_task_notes" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Add Task Notes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.report_issues" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">Report Issues</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_activity_logs" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm text-gray-700">View Activity Logs</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Permission Sets -->
                <div class="border-t border-gray-200/50 pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Permission Sets</h4>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" @click="setAllPermissions(true)" class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-check-double mr-2 text-green-500"></i>
                            Select All
                        </button>
                        <button type="button" @click="setAllPermissions(false)" class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-times mr-2 text-red-500"></i>
                            Clear All
                        </button>
                        <button type="button" @click="setBasicPermissions()" class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-user mr-2 text-blue-500"></i>
                            Basic Access
                        </button>
                        <button type="button" @click="setFullPermissions()" class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-user-shield mr-2 text-purple-500"></i>
                            Full Access
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="border-t border-gray-200/50 pt-6">
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('owner.staff.index') }}" class="w-full sm:w-auto px-6 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto px-6 py-3 border-2 border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-all duration-200">
                            <span x-show="!isSubmitting">Create Staff Member</span>
                            <span x-show="isSubmitting">Creating...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addStaff() {
    return {
        form: {
            name: '',
            mobile_number: '',
            pin: '',
            email: '',
            property_id: '',
            role_id: '',
            start_date: new Date().toISOString().split('T')[0],
            end_date: ''
        },
        properties: @json($properties),
        roles: [],
        permissions: {
            view_bookings: true,
            view_guest_details: true,
            update_guest_services: true,
            view_assigned_tasks: true,
            update_task_status: true,
            upload_task_photos: true,
            access_cleaning_checklists: true,
            execute_checklists: true,
            update_checklist_progress: true,
            receive_notifications: true,
            add_task_notes: true,
            report_issues: true,
            view_activity_logs: true
        },
        isSubmitting: false,
        
        init() {
            // Set default permissions to basic access
            this.setBasicPermissions();
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
        
        initializeSelect2() {
            // Initialize Select2 for property dropdown
            $('#property_select').select2({
                placeholder: 'Select Property',
                allowClear: false,
                width: '100%',
                dropdownCssClass: 'select2-blue-theme',
                containerCssClass: 'select2-blue-container'
            });
            
            // Initialize Select2 for role dropdown
            $('#role_select').select2({
                placeholder: 'Select Role',
                allowClear: false,
                width: '100%',
                dropdownCssClass: 'select2-blue-theme',
                containerCssClass: 'select2-blue-container'
            });
            
            // Handle Select2 change events
            $('#property_select').on('change', (e) => {
                this.form.property_id = e.target.value;
                this.loadRoles();
            });
            
            $('#role_select').on('change', (e) => {
                this.form.role_id = e.target.value;
            });
        },
        
        async loadRoles() {
            if (!this.form.property_id) {
                this.roles = [];
                this.form.role_id = '';
                $('#role_select').val('').trigger('change');
                return;
            }
            
            try {
                const response = await fetch(`/api/properties/${this.form.property_id}/roles`);
                const data = await response.json();
                this.roles = data.roles || [];
                
                // Clear role selection and update Select2
                this.form.role_id = '';
                this.$nextTick(() => {
                    $('#role_select').val('').trigger('change');
                });
            } catch (error) {
                console.error('Error loading roles:', error);
                this.roles = [];
                this.form.role_id = '';
                $('#role_select').val('').trigger('change');
            }
        },
        
        setAllPermissions(value) {
            Object.keys(this.permissions).forEach(key => {
                this.permissions[key] = value;
            });
        },
        
        setBasicPermissions() {
            this.permissions = {
                view_bookings: true,
                view_guest_details: false,
                update_guest_services: false,
                view_assigned_tasks: true,
                update_task_status: true,
                upload_task_photos: true,
                access_cleaning_checklists: true,
                execute_checklists: true,
                update_checklist_progress: true,
                receive_notifications: true,
                add_task_notes: true,
                report_issues: true,
                view_activity_logs: true
            };
        },
        
        setFullPermissions() {
            this.setAllPermissions(true);
        },
        
        async submitForm() {
            this.isSubmitting = true;
            
            try {
                const formData = {
                    ...this.form,
                    permissions: this.permissions
                };
                
                console.log('Submitting form data:', formData);
                
                const response = await fetch('/owner/staff', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(formData)
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Response result:', result);
                
                if (result.success) {
                    this.showNotification('Staff member created successfully!', 'success');
                    window.location.href = '/owner/staff';
                } else {
                    this.showNotification(result.message || result.error || 'Failed to create staff member', 'error');
                }
            } catch (error) {
                console.error('Error creating staff:', error);
                console.error('Error details:', error.message);
                this.showNotification('Failed to create staff member: ' + error.message, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        
        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this with a proper notification system
            alert(message);
        }
    }
}
</script>
@endsection
