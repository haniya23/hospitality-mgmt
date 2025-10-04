@extends('layouts.app')

@section('title', 'Staff Management')

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
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50" x-data="staffManagement()">
    <!-- Header -->
    <header class="soft-header-gradient text-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                        <i class="fas fa-users text-teal-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Staff Management</h1>
                        <p class="text-sm text-slate-700">Manage your staff members and monitor their progress</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('owner.staff.create') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-user-plus text-pink-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Add Staff</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-6 mb-8">
            <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-sm sm:text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Staff</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.total_staff"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-sm sm:text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Active Staff</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.active_staff"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-tasks text-white text-sm sm:text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.total_tasks"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-emerald-400 to-teal-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-sm sm:text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Completed Tasks</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.completed_tasks"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-red-400 to-pink-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-triangle text-white text-sm sm:text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Overdue Tasks</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.overdue_tasks"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="modern-card rounded-xl sm:rounded-2xl overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200/50">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-bold text-gray-900">Staff Members</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <!-- Filter by Property -->
                        <div class="flex-1 sm:min-w-0 sm:flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1 sm:hidden">Property</label>
                            <select id="property-filter" class="w-full">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filter by Status -->
                        <div class="flex-1 sm:min-w-0 sm:flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1 sm:hidden">Status</label>
                            <select id="status-filter" class="w-full">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 p-4 sm:p-6">
                <template x-for="staff in staffMembers" :key="staff.id">
                    <div class="modern-card rounded-xl sm:rounded-2xl p-4 sm:p-6 hover:scale-105 transition-transform duration-300">
                        <!-- Staff Header -->
                        <div class="flex items-center space-x-3 sm:space-x-4 mb-4">
                            <!-- Staff Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-lg sm:text-xl" x-text="staff.user.name.charAt(0).toUpperCase()"></span>
                                </div>
                            </div>
                            
                            <!-- Staff Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-1">
                                    <h4 class="text-base sm:text-lg font-medium text-gray-900 truncate" x-text="staff.user.name"></h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                                          :class="getStatusClass(staff.status)" x-text="staff.status"></span>
                                </div>
                                <p class="text-sm text-gray-500" x-text="staff.user.mobile_number"></p>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 text-xs sm:text-sm text-gray-600 mt-1">
                                    <span class="flex items-center">
                                        <i class="fas fa-building mr-1"></i>
                                        <span x-text="staff.property.name" class="truncate"></span>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-user-tag mr-1"></i>
                                        <span x-text="staff.role.name"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Staff Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-sm sm:text-base font-medium text-gray-900" x-text="staff.task_stats.total"></p>
                                <p class="text-xs text-gray-500">Tasks</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm sm:text-base font-medium text-green-600" x-text="staff.task_stats.completed"></p>
                                <p class="text-xs text-gray-500">Done</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm sm:text-base font-medium" :class="staff.task_stats.overdue > 0 ? 'text-red-600' : 'text-gray-900'" x-text="staff.task_stats.overdue"></p>
                                <p class="text-xs text-gray-500">Overdue</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm sm:text-base font-medium text-blue-600" x-text="staff.completion_rate + '%'"></p>
                                <p class="text-xs text-gray-500">Rate</p>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <a :href="`/owner/attendance/staff/${staff.user.uuid}?uuid=${staff.user.uuid}`" 
                               class="flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <span class="hidden sm:inline">Attendance</span>
                                <span class="sm:hidden">Att.</span>
                            </a>
                            <a :href="`/owner/leave-requests?staff_id=${staff.user.id}&uuid=${staff.user.uuid}`" 
                               class="flex items-center justify-center px-3 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium">
                                <i class="fas fa-calendar-times mr-2"></i>
                                <span class="hidden sm:inline">Leave</span>
                                <span class="sm:hidden">Leave</span>
                            </a>
                        </div>
                        
                        <!-- Main Actions -->
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <a :href="`/owner/staff/${staff.uuid}`" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-2"></i>
                                View Profile
                            </a>
                            <a :href="`/owner/staff/${staff.uuid}/edit`" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </a>
                        </div>
                        
                    </div>
                </template>
                
                <div x-show="staffMembers.length === 0" class="col-span-full p-6 text-center text-gray-500">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No staff members found</h3>
                    <p class="text-sm text-gray-500 mb-4">Get started by adding your first staff member.</p>
                    <a href="{{ route('owner.staff.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add Staff Member
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function staffManagement() {
    return {
        staffMembers: @json($staffAssignments),
        properties: @json($properties),
        stats: @json($stats),
        filters: {
            property_id: '',
            status: ''
        },

        init() {
            this.initializeSelect2();
        },

        initializeSelect2() {
            $('#property-filter').select2({
                placeholder: 'Filter by Property',
                allowClear: true,
                width: '100%',
                dropdownParent: $('.modern-card')
            }).on('change', (e) => {
                this.filters.property_id = e.target.value;
                this.filterStaff();
            });

            $('#status-filter').select2({
                placeholder: 'Filter by Status',
                allowClear: true,
                width: '100%',
                dropdownParent: $('.modern-card')
            }).on('change', (e) => {
                this.filters.status = e.target.value;
                this.filterStaff();
            });
        },

        filterStaff() {
            // This would typically make an AJAX call to filter staff
            // For now, we'll just show all staff
            console.log('Filtering staff:', this.filters);
        },

        getStatusClass(status) {
            const statusClasses = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800',
                'suspended': 'bg-red-100 text-red-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },

        editStaff(staffId) {
            window.location.href = `/owner/staff/${staffId}/edit`;
        }
    }
}
</script>
@endpush
@endsection