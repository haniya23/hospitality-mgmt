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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="modern-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Staff</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.total_staff"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Staff</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.active_staff"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-tasks text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.total_tasks"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed Tasks</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.completed_tasks"></p>
                    </div>
                </div>
            </div>

            <div class="modern-card rounded-2xl p-6 hover:scale-105 transition-transform duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Overdue Tasks</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.overdue_tasks"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="modern-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Staff Members</h3>
                    <div class="flex items-center space-x-4">
                        <!-- Filter by Property -->
                        <div class="min-w-0 flex-1">
                            <select id="property_filter" x-model="filters.property_id" @change="loadStaff()" 
                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 select2-dropdown">
                                <option value="">All Properties</option>
                                <template x-for="property in properties" :key="property.id">
                                    <option :value="property.id" x-text="property.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Filter by Status -->
                        <div class="min-w-0 flex-1">
                            <select id="status_filter" x-model="filters.status" @change="loadStaff()" 
                                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800 select2-dropdown">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="divide-y divide-gray-200/30">
                <template x-for="staff in staffMembers" :key="staff.id">
                    <div class="p-6 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Staff Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <span class="text-white font-bold text-xl" x-text="staff.user.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
                                
                                <!-- Staff Info -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-1">
                                        <h4 class="text-lg font-medium text-gray-900" x-text="staff.user.name"></h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              :class="getStatusClass(staff.status)" x-text="staff.status"></span>
                                        <span class="text-sm text-gray-500" x-text="staff.user.mobile_number"></span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-building mr-1"></i>
                                            <span x-text="staff.property.name"></span>
                                        </span>
                                        <span>
                                            <i class="fas fa-user-tag mr-1"></i>
                                            <span x-text="staff.role.name"></span>
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            Started: <span x-text="formatDate(staff.start_date)"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Staff Stats -->
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900" x-text="staff.task_stats.total"></p>
                                    <p class="text-xs text-gray-500">Total Tasks</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-green-600" x-text="staff.task_stats.completed"></p>
                                    <p class="text-xs text-gray-500">Completed</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-medium" :class="staff.task_stats.overdue > 0 ? 'text-red-600' : 'text-gray-900'" x-text="staff.task_stats.overdue"></p>
                                    <p class="text-xs text-gray-500">Overdue</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-blue-600" x-text="staff.completion_rate + '%'"></p>
                                    <p class="text-xs text-gray-500">Completion Rate</p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button @click="viewStaff(staff.id)" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </button>
                                <button @click="editStaff(staff.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </button>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                        <div class="py-1">
                                            <button @click="assignTask(staff.id)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-tasks mr-2"></i>Assign Task
                                            </button>
                                            <button @click="sendNotification(staff.id)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-bell mr-2"></i>Send Notification
                                            </button>
                                            <button @click="viewPermissions(staff.id)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-key mr-2"></i>Manage Permissions
                                            </button>
                                            <div class="border-t border-gray-100"></div>
                                            <button @click="deactivateStaff(staff.id)" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <i class="fas fa-user-times mr-2"></i>Deactivate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-medium text-gray-900">Recent Activity</h5>
                                <span class="text-xs text-gray-500">Last 7 days</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <template x-for="activity in staff.recent_activities" :key="activity.id">
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="fas fa-circle w-2 h-2 mr-2 text-blue-500"></i>
                                        <span x-text="activity.description"></span>
                                        <span class="ml-auto" x-text="activity.time_ago"></span>
                                    </div>
                                </template>
                                <div x-show="staff.recent_activities.length === 0" class="text-xs text-gray-500 italic">
                                    No recent activity
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="staffMembers.length === 0" class="p-6 text-center text-gray-500">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No staff members found</h3>
                    <p class="text-sm text-gray-500 mb-4">Get started by adding your first staff member.</p>
                    <a href="{{ route('owner.staff.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add Staff Member
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Assignment Modal -->
    <div x-show="showTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-transition>
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Task</h3>
                <form @submit.prevent="submitTaskAssignment()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Name</label>
                        <input x-model="taskForm.task_name" type="text" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter task name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea x-model="taskForm.description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter task description"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                            <select x-model="taskForm.task_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="cleaning">Cleaning</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="guest_service">Guest Service</option>
                                <option value="check_in">Check-in</option>
                                <option value="check_out">Check-out</option>
                                <option value="inspection">Inspection</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select x-model="taskForm.priority" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date & Time</label>
                        <input x-model="taskForm.scheduled_at" type="datetime-local" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showTaskModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Assign Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div x-show="showNotificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-transition>
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Send Notification</h3>
                <form @submit.prevent="submitNotification()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input x-model="notificationForm.title" type="text" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter notification title">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea x-model="notificationForm.message" rows="3" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter notification message"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select x-model="notificationForm.type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="general">General</option>
                                <option value="task_assignment">Task Assignment</option>
                                <option value="urgent_update">Urgent Update</option>
                                <option value="reminder">Reminder</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select x-model="notificationForm.priority" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showNotificationModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function staffManagement() {
    return {
        staffMembers: @json($staffAssignments),
        properties: @json($properties),
        stats: {
            total_staff: 0,
            active_staff: 0,
            total_tasks: 0,
            completed_tasks: 0,
            overdue_tasks: 0
        },
        filters: {
            property_id: '',
            status: ''
        },
        showTaskModal: false,
        showNotificationModal: false,
        currentStaffId: null,
        taskForm: {
            task_name: '',
            description: '',
            task_type: 'cleaning',
            priority: 'medium',
            scheduled_at: ''
        },
        notificationForm: {
            title: '',
            message: '',
            type: 'general',
            priority: 'medium'
        },
        
        init() {
            this.loadStats();
            this.loadStaff();
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
        
        initializeSelect2() {
            // Initialize Select2 for property filter
            $('#property_filter').select2({
                placeholder: 'All Properties',
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'select2-blue-theme',
                containerCssClass: 'select2-blue-container'
            });
            
            // Initialize Select2 for status filter
            $('#status_filter').select2({
                placeholder: 'All Status',
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'select2-blue-theme',
                containerCssClass: 'select2-blue-container'
            });
            
            // Handle Select2 change events
            $('#property_filter').on('change', (e) => {
                this.filters.property_id = e.target.value;
                this.loadStaff();
            });
            
            $('#status_filter').on('change', (e) => {
                this.filters.status = e.target.value;
                this.loadStaff();
            });
        },
        
        async loadStats() {
            try {
                const response = await fetch('/owner/staff/stats', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
                // Fallback to server-side data
                this.stats = @json($stats ?? []);
            }
        },
        
        async loadStaff() {
            try {
                const params = new URLSearchParams(this.filters);
                const response = await fetch(`/owner/staff?${params}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                this.staffMembers = data.staffAssignments || [];
            } catch (error) {
                console.error('Error loading staff:', error);
                // Fallback to server-side data
                this.staffMembers = @json($staffAssignments ?? []);
            }
        },
        
        viewStaff(staffId) {
            window.location.href = `/owner/staff/${staffId}`;
        },
        
        editStaff(staffId) {
            window.location.href = `/owner/staff/${staffId}/edit`;
        },
        
        assignTask(staffId) {
            this.currentStaffId = staffId;
            this.showTaskModal = true;
        },
        
        async submitTaskAssignment() {
            try {
                const response = await fetch(`/owner/staff/${this.currentStaffId}/assign-task`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.taskForm)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showTaskModal = false;
                    this.resetTaskForm();
                    this.loadStats();
                    this.showNotification('Task assigned successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to assign task', 'error');
                }
            } catch (error) {
                console.error('Error assigning task:', error);
                this.showNotification('Failed to assign task', 'error');
            }
        },
        
        sendNotification(staffId) {
            this.currentStaffId = staffId;
            this.showNotificationModal = true;
        },
        
        async submitNotification() {
            try {
                const response = await fetch(`/owner/staff/${this.currentStaffId}/send-notification`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.notificationForm)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showNotificationModal = false;
                    this.resetNotificationForm();
                    this.showNotification('Notification sent successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to send notification', 'error');
                }
            } catch (error) {
                console.error('Error sending notification:', error);
                this.showNotification('Failed to send notification', 'error');
            }
        },
        
        viewPermissions(staffId) {
            window.location.href = `/owner/staff/${staffId}#permissions`;
        },
        
        async deactivateStaff(staffId) {
            if (confirm('Are you sure you want to deactivate this staff member?')) {
                try {
                    const response = await fetch(`/owner/staff/${staffId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        this.loadStats();
                        this.loadStaff();
                        this.showNotification('Staff member deactivated successfully', 'success');
                    } else {
                        this.showNotification(result.error || 'Failed to deactivate staff member', 'error');
                    }
                } catch (error) {
                    console.error('Error deactivating staff:', error);
                    this.showNotification('Failed to deactivate staff member', 'error');
                }
            }
        },
        
        resetTaskForm() {
            this.taskForm = {
                task_name: '',
                description: '',
                task_type: 'cleaning',
                priority: 'medium',
                scheduled_at: ''
            };
        },
        
        resetNotificationForm() {
            this.notificationForm = {
                title: '',
                message: '',
                type: 'general',
                priority: 'medium'
            };
        },
        
        getStatusClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800',
                'suspended': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },
        
        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this with a proper notification system
            alert(message);
        }
    }
}
</script>
@endsection
