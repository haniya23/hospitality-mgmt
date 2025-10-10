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
                            <p class="text-xs text-gray-500 mt-2">Access controls will appear below once role is selected</p>
                        </div>
                    </div>

                </div>

                <!-- Simple Access Control -->
                <div class="border-t border-gray-200/50 pt-6" x-show="form.role_id">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Access Control</h4>
                    <p class="text-sm text-gray-600 mb-6">Simple toggles for staff access. All staff can view upcoming bookings and guest services, but only those with access can edit them. Managers automatically get full access.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Booking Access -->
                        <div class="modern-card rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-semibold text-gray-900">Booking Access</h5>
                                        <p class="text-sm text-gray-600">Can edit booking details and reservations</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.booking_access" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Guest Service Access -->
                        <div class="modern-card rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-concierge-bell text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-semibold text-gray-900">Guest Service Access</h5>
                                        <p class="text-sm text-gray-600">Can update guest services and handle requests</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.guest_service_access" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-blue-800">How Access Works</h4>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li><strong>All staff</strong> can view upcoming bookings and guest services</li>
                                        <li><strong>Booking Access:</strong> Allows editing booking details, check-in/check-out times, and guest information</li>
                                        <li><strong>Guest Service Access:</strong> Allows updating guest service requests, room service, and guest communications</li>
                                        <li>Perfect for cleaners (no access) vs front desk staff (both access)</li>
                                        <li><strong>Managers:</strong> Automatically get both access levels</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment Dates -->
                <div class="border-t border-gray-200/50 pt-6" x-show="form.role_id">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Assignment Dates</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            end_date: '',
            booking_access: false,
            guest_service_access: false
        },
        properties: @json($properties),
        roles: [],
        isSubmitting: false,
        
        init() {
            this.$nextTick(() => {
                this.initializeSelect2();
                this.loadAllRoles();
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
            });
            
            $('#role_select').on('change', (e) => {
                this.form.role_id = e.target.value;
                this.updateAccessBasedOnRole();
            });
        },
        
        async loadAllRoles() {
            try {
                const response = await fetch('/api/roles');
                const data = await response.json();
                this.roles = data.roles || [];
                
                // Update Select2 with all roles
                this.$nextTick(() => {
                    $('#role_select').trigger('change');
                });
            } catch (error) {
                console.error('Error loading roles:', error);
                this.roles = [];
            }
        },
        
        updateAccessBasedOnRole() {
            if (!this.form.role_id || !this.roles.length) {
                return;
            }
            
            // Find the selected role
            const selectedRole = this.roles.find(role => role.id == this.form.role_id);
            
            if (selectedRole) {
                // Auto-set access based on role
                if (selectedRole.name.toLowerCase() === 'manager') {
                    this.form.booking_access = true;
                    this.form.guest_service_access = true;
                } else {
                    // For non-managers, default to no access
                    this.form.booking_access = false;
                    this.form.guest_service_access = false;
                }
            }
        },
        
        async submitForm() {
            this.isSubmitting = true;
            
            try {
                const formData = {
                    ...this.form
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
