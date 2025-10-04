@extends('layouts.app')

@section('title', 'Edit Staff - ' . $staffAssignment->user->name)

@section('header')
<x-page-header 
    title="Edit Staff Member" 
    subtitle="Update staff information and assignments for {{ $staffAssignment->user->name }}" 
    icon="user-edit">
    
    <!-- Back Button -->
    <div class="flex items-center space-x-3 mb-4">
        <a href="{{ route('owner.staff.show', $staffAssignment->uuid) }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Profile
        </a>
    </div>
</x-page-header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Staff Information Form -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center text-white font-bold text-xl">
                {{ substr($staffAssignment->user->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">{{ $staffAssignment->user->name }}</h3>
                <p class="text-sm text-gray-600">Staff Member ID: {{ $staffAssignment->id }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('owner.staff.update', $staffAssignment->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $staffAssignment->user->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Mobile Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="mobile_number" 
                           name="mobile_number" 
                           value="{{ old('mobile_number', $staffAssignment->user->mobile_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('mobile_number') border-red-500 @enderror"
                           required>
                    @error('mobile_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $staffAssignment->user->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">
                        PIN Code (4 digits)
                    </label>
                    <input type="text" 
                           id="pin" 
                           name="pin" 
                           value="{{ old('pin', $staffAssignment->user->pin) }}"
                           maxlength="4"
                           pattern="[0-9]{4}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('pin') border-red-500 @enderror"
                           placeholder="1234">
                    @error('pin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Assignment Information -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Assignment Details</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Property <span class="text-red-500">*</span>
                        </label>
                        <select id="property_id" 
                                name="property_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('property_id') border-red-500 @enderror"
                                required>
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" 
                                        {{ old('property_id', $staffAssignment->property_id) == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="role_id" 
                                name="role_id" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('role_id') border-red-500 @enderror"
                                required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        {{ old('role_id', $staffAssignment->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status') border-red-500 @enderror"
                                required>
                            <option value="active" {{ old('status', $staffAssignment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $staffAssignment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $staffAssignment->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Employment Dates -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Employment Period</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $staffAssignment->start_date ? $staffAssignment->start_date->format('Y-m-d') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date (Optional)
                        </label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date', $staffAssignment->end_date ? $staffAssignment->end_date->format('Y-m-d') : '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Salary Information -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Salary Details</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Salary
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₹</span>
                            </div>
                            <input type="number" 
                                   id="salary" 
                                   name="salary" 
                                   value="{{ old('salary', $staffAssignment->salary) }}"
                                   min="0"
                                   step="100"
                                   class="w-full pl-8 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('salary') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Commission Rate (%)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="commission_rate" 
                                   name="commission_rate" 
                                   value="{{ old('commission_rate', $staffAssignment->commission_rate) }}"
                                   min="0"
                                   max="100"
                                   step="0.1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('commission_rate') border-red-500 @enderror"
                                   placeholder="0.0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        @error('commission_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Access Control -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Access Control & Permissions</h4>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-blue-600"></i>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">Role-Based Permissions</h5>
                            <p class="text-sm text-gray-600">This staff member has permissions based on their assigned role: <strong>{{ $staffAssignment->role->name ?? 'No Role Assigned' }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Role Permissions -->
                    <div>
                        <h5 class="font-medium text-gray-900 mb-3">Role Permissions</h5>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @if($staffAssignment->role && $staffAssignment->role->permissions->count() > 0)
                                @foreach($staffAssignment->role->permissions->groupBy('module') as $module => $permissions)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <h6 class="font-medium text-gray-800 text-sm mb-2">{{ ucwords(str_replace('_', ' ', $module)) }}</h6>
                                        <div class="space-y-1">
                                            @foreach($permissions as $permission)
                                                <div class="flex items-center space-x-2 text-sm">
                                                    <i class="fas fa-check text-green-500"></i>
                                                    <span class="text-gray-700">{{ $permission->description }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-gray-500">
                                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                    <p>No permissions assigned to this role</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Custom Permissions -->
                    <div>
                        <h5 class="font-medium text-gray-900 mb-3">Custom Permissions</h5>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @php
                                $staffPermissions = \App\Models\StaffPermission::where('staff_assignment_id', $staffAssignment->id)->get()->keyBy('permission_key');
                            @endphp
                            
                            @foreach(\App\Models\StaffPermission::PERMISSIONS as $key => $description)
                                @php
                                    $permission = $staffPermissions->get($key);
                                    $isGranted = $permission ? $permission->is_granted : false;
                                @endphp
                                <div class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" 
                                               id="permission_{{ $key }}" 
                                               name="permissions[{{ $key }}][granted]"
                                               {{ $isGranted ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label for="permission_{{ $key }}" class="text-sm text-gray-700 cursor-pointer">
                                            {{ $description }}
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-xs text-gray-500">{{ $key }}</span>
                                        @if($isGranted)
                                            <i class="fas fa-check text-green-500 text-xs"></i>
                                        @else
                                            <i class="fas fa-times text-red-500 text-xs"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium">Permission Override</p>
                                    <p>Custom permissions override role-based permissions. Uncheck to deny access even if the role allows it.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('owner.staff.show', $staffAssignment->uuid) }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center font-medium">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 font-medium">
                    <i class="fas fa-save mr-2"></i>Update Staff Member
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-red-200">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                <p class="text-sm text-red-600">These actions are irreversible. Please proceed with caution.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button onclick="resetPassword()" 
                    class="flex items-center justify-center px-4 py-3 border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors">
                <i class="fas fa-key mr-2"></i>
                Reset Password
            </button>
            
            <button onclick="deactivateStaff()" 
                    class="flex items-center justify-center px-4 py-3 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                <i class="fas fa-user-times mr-2"></i>
                Deactivate Staff
            </button>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Reset Password</h3>
            </div>
            <form id="resetPasswordForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="new_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="confirm_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deactivate Staff Modal -->
<div id="deactivateStaffModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Deactivate Staff</h3>
            </div>
            <form id="deactivateStaffForm" class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 p-4 bg-red-50 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        <p class="text-sm text-red-800">This action will deactivate the staff member and revoke their access to the system.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Deactivation</label>
                        <textarea id="deactivation_reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeDeactivateStaffModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200">
                        Deactivate Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetPassword() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function deactivateStaff() {
    document.getElementById('deactivateStaffModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

function closeDeactivateStaffModal() {
    document.getElementById('deactivateStaffModal').classList.add('hidden');
}

document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    const formData = {
        password: newPassword,
        password_confirmation: confirmPassword
    };
    
    try {
        const response = await fetch(`/owner/staff/{{ $staffAssignment->uuid }}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Password reset successfully!');
            closeResetPasswordModal();
        } else {
            alert('Failed to reset password: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while resetting password.');
    }
});

document.getElementById('deactivateStaffForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const reason = document.getElementById('deactivation_reason').value;
    
    if (!confirm('Are you sure you want to deactivate this staff member? This action cannot be undone.')) {
        return;
    }
    
    const formData = {
        reason: reason
    };
    
    try {
        const response = await fetch(`/owner/staff/{{ $staffAssignment->uuid }}/deactivate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Staff member deactivated successfully!');
            window.location.href = "{{ route('owner.staff.index') }}";
        } else {
            alert('Failed to deactivate staff member: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while deactivating staff member.');
    }
});

// Initialize Select2 for dropdowns
$(document).ready(function() {
    $('#property_id').select2({
        placeholder: 'Select Property',
        allowClear: true,
        width: '100%'
    });
    
    $('#role_id').select2({
        placeholder: 'Select Role',
        allowClear: true,
        width: '100%'
    });
    
    $('#status').select2({
        placeholder: 'Select Status',
        allowClear: true,
        width: '100%'
    });

    // Handle permission updates
    $('input[name^="permissions"]').on('change', function() {
        const permissionKey = $(this).attr('name').match(/permissions\[([^\]]+)\]/)[1];
        const isGranted = $(this).is(':checked');
        
        updatePermission(permissionKey, isGranted);
    });
});

// Function to update individual permission
async function updatePermission(permissionKey, isGranted) {
    try {
        const permissions = {};
        permissions[permissionKey] = {
            granted: isGranted,
            restrictions: {}
        };

        const response = await fetch(`/owner/staff/{{ $staffAssignment->uuid }}/update-permissions`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ permissions: permissions })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update the visual indicator
            const icon = $(`input[name="permissions[${permissionKey}][granted]"]`).closest('.flex').find('i');
            if (isGranted) {
                icon.removeClass('fa-times text-red-500').addClass('fa-check text-green-500');
            } else {
                icon.removeClass('fa-check text-green-500').addClass('fa-times text-red-500');
            }
            
            // Show success message
            showNotification('Permission updated successfully!', 'success');
        } else {
            showNotification('Failed to update permission: ' + result.message, 'error');
            // Revert the checkbox
            $(`input[name="permissions[${permissionKey}][granted]"]`).prop('checked', !isGranted);
        }
    } catch (error) {
        showNotification('An error occurred while updating permission.', 'error');
        // Revert the checkbox
        $(`input[name="permissions[${permissionKey}][granted]"]`).prop('checked', !isGranted);
    }
}

// Function to show notifications
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'}">
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check' : type === 'error' ? 'fa-times' : 'fa-info'}"></i>
                <span>${message}</span>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
@endsection
