@extends('layouts.staff')

@section('title', 'Staff Tasks')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="staffTasks()">
    <!-- Back Button -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('staff.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    
    <!-- Header -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">My Tasks</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your assigned tasks</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Property Selector -->
                    <select x-model="filters.property_id" @change="loadTasks()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Properties</option>
                        <template x-for="property in assignedProperties" :key="property.id">
                            <option :value="property.id" x-text="property.name"></option>
                        </template>
                    </select>
                    
                    <!-- Show Completed Toggle -->
                    <label class="flex items-center">
                        <input type="checkbox" x-model="filters.show_completed" @change="loadTasks()" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Show Completed</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Task Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Task Type</label>
                    <select x-model="filters.task_type" @change="loadTasks()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="cleaning">Cleaning</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="guest_service">Guest Service</option>
                        <option value="check_in">Check-in</option>
                        <option value="check_out">Check-out</option>
                        <option value="inspection">Inspection</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select x-model="filters.priority" @change="loadTasks()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Priorities</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="filters.status" @change="loadTasks()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    <button @click="clearFilters()" class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.total"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.pending"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.in_progress"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.completed"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.overdue"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Tasks</h3>
            </div>
            
            <div class="divide-y divide-gray-200">
                <template x-for="task in tasks" :key="task.id">
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900" x-text="task.task_name"></h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="getPriorityClass(task.priority)" x-text="task.priority"></span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="getStatusClass(task.status)" x-text="task.status"></span>
                                    <span x-show="isOverdue(task)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2" x-text="task.description"></p>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span x-text="task.property.name"></span>
                                    </span>
                                    <span x-show="task.scheduled_at">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span x-text="formatDateTime(task.scheduled_at)"></span>
                                    </span>
                                    <span x-show="task.task_type">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span x-text="task.task_type"></span>
                                    </span>
                                </div>
                                
                                <!-- Completion Notes (if completed) -->
                                <div x-show="task.status === 'completed' && task.completion_notes" class="mt-3 p-3 bg-green-50 rounded-md">
                                    <p class="text-sm text-green-800">
                                        <strong>Completion Notes:</strong> <span x-text="task.completion_notes"></span>
                                    </p>
                                </div>
                                
                                <!-- Completion Photos (if completed) -->
                                <div x-show="task.status === 'completed' && task.completion_photos && task.completion_photos.length > 0" class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Completion Photos:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <template x-for="photo in task.completion_photos" :key="photo">
                                            <img :src="photo" :alt="task.task_name + ' completion photo'" class="w-full h-20 object-cover rounded-md">
                                        </template>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <template x-if="task.status === 'pending'">
                                    <button @click="startTask(task.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Start
                                    </button>
                                </template>
                                
                                <template x-if="task.status === 'in_progress'">
                                    <button @click="completeTask(task.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Complete
                                    </button>
                                </template>
                                
                                <template x-if="task.status === 'completed'">
                                    <div class="flex items-center text-green-600">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Completed</span>
                                    </div>
                                </template>
                                
                                <template x-if="task.status === 'cancelled'">
                                    <div class="flex items-center text-red-600">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Cancelled</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="tasks.length === 0" class="p-6 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later for new assignments.</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div x-show="tasks.length > 0" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> results
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="loadTasks(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <span class="text-sm text-gray-700">
                            Page <span x-text="pagination.current_page"></span> of <span x-text="pagination.last_page"></span>
                        </span>
                        <button @click="loadTasks(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Completion Modal -->
    <div x-show="showCompletionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-transition>
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Task</h3>
                <form @submit.prevent="submitTaskCompletion()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes</label>
                        <textarea x-model="completionNotes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add any notes about the task completion..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Photos (Optional)</label>
                        <input type="file" multiple accept="image/*" @change="handlePhotoUpload($event)" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showCompletionModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Complete Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function staffTasks() {
    return {
        filters: {
            property_id: '',
            task_type: '',
            priority: '',
            status: '',
            show_completed: false
        },
        tasks: [],
        taskStats: {
            total: 0,
            pending: 0,
            in_progress: 0,
            completed: 0,
            overdue: 0
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0
        },
        showCompletionModal: false,
        currentTaskId: null,
        completionNotes: '',
        completionPhotos: [],
        
        assignedProperties: @json($assignedProperties),
        
        init() {
            this.loadTasks();
        },
        
        async loadTasks(page = 1) {
            try {
                const params = new URLSearchParams({
                    page: page,
                    ...this.filters
                });
                
                const response = await fetch(`/staff/tasks?${params}`);
                const data = await response.json();
                
                this.tasks = data.tasks.data || [];
                this.taskStats = data.taskStats || {};
                this.pagination = {
                    current_page: data.tasks.current_page || 1,
                    last_page: data.tasks.last_page || 1,
                    from: data.tasks.from || 0,
                    to: data.tasks.to || 0,
                    total: data.tasks.total || 0
                };
            } catch (error) {
                console.error('Error loading tasks:', error);
            }
        },
        
        clearFilters() {
            this.filters = {
                property_id: '',
                task_type: '',
                priority: '',
                status: '',
                show_completed: false
            };
            this.loadTasks();
        },
        
        async startTask(taskId) {
            try {
                const response = await fetch(`/staff/tasks/${taskId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                const result = await response.json();
                if (result.success) {
                    this.loadTasks();
                    this.showNotification('Task started successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to start task', 'error');
                }
            } catch (error) {
                console.error('Error starting task:', error);
                this.showNotification('Failed to start task', 'error');
            }
        },
        
        completeTask(taskId) {
            this.currentTaskId = taskId;
            this.showCompletionModal = true;
        },
        
        async submitTaskCompletion() {
            try {
                const formData = new FormData();
                formData.append('completion_notes', this.completionNotes);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Add photos
                this.completionPhotos.forEach((photo, index) => {
                    formData.append(`completion_photos[${index}]`, photo);
                });
                
                const response = await fetch(`/staff/tasks/${this.currentTaskId}/complete`, {
                    method: 'POST',
                    body: formData,
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showCompletionModal = false;
                    this.resetCompletionForm();
                    this.loadTasks();
                    this.showNotification('Task completed successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to complete task', 'error');
                }
            } catch (error) {
                console.error('Error completing task:', error);
                this.showNotification('Failed to complete task', 'error');
            }
        },
        
        handlePhotoUpload(event) {
            this.completionPhotos = Array.from(event.target.files);
        },
        
        resetCompletionForm() {
            this.currentTaskId = null;
            this.completionNotes = '';
            this.completionPhotos = [];
        },
        
        getPriorityClass(priority) {
            const classes = {
                'urgent': 'bg-red-100 text-red-800',
                'high': 'bg-orange-100 text-orange-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'low': 'bg-green-100 text-green-800',
            };
            return classes[priority] || 'bg-gray-100 text-gray-800';
        },
        
        getStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800',
                'in_progress': 'bg-blue-100 text-blue-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        isOverdue(task) {
            if (!task.scheduled_at) return false;
            const scheduledDate = new Date(task.scheduled_at);
            const now = new Date();
            return scheduledDate < now && ['pending', 'in_progress'].includes(task.status);
        },
        
        formatDateTime(dateString) {
            if (!dateString) return 'Not scheduled';
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },
        
        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this with a proper notification system
            alert(message);
        }
    }
}
</script>
@endsection
