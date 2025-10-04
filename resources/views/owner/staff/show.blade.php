@extends('layouts.app')

@section('title', 'Staff Details')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="staffDetails()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('owner.staff.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Staff
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900" x-text="staffAssignment.user.name"></h1>
                        <p class="text-sm text-gray-600">
                            <span x-text="staffAssignment.property.name"></span> • 
                            <span x-text="staffAssignment.role.name"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                          :class="getStatusClass(staffAssignment.status)" x-text="staffAssignment.status"></span>
                    <a :href="`/owner/staff/${staffAssignment.id}/edit`" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Staff
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-tasks text-blue-600"></i>
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
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
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
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
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
                        <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="taskStats.overdue"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Task Completion Rate</h3>
                <span class="text-2xl font-bold text-green-600" x-text="completionRate + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-green-600 h-4 rounded-full transition-all duration-500" :style="`width: ${completionRate}%`"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                <span x-text="taskStats.completed"></span> of <span x-text="taskStats.total"></span> tasks completed
            </p>
        </div>

        <!-- Tabs -->
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Recent Tasks
                    </button>
                    <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Activity Log
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Notifications
                    </button>
                    <button @click="activeTab = 'permissions'" :class="activeTab === 'permissions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Permissions
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tasks Tab -->
                <div x-show="activeTab === 'tasks'" class="space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
                        <button @click="assignTask()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-plus mr-2"></i>
                            Assign Task
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="task in recentTasks" :key="task.id">
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h4 class="text-lg font-medium text-gray-900" x-text="task.task_name"></h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                  :class="getPriorityClass(task.priority)" x-text="task.priority"></span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                  :class="getStatusClass(task.status)" x-text="task.status"></span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2" x-text="task.description"></p>
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                <span x-text="formatDateTime(task.scheduled_at)"></span>
                                            </span>
                                            <span x-show="task.started_at">
                                                <i class="fas fa-play mr-1"></i>
                                                Started: <span x-text="formatDateTime(task.started_at)"></span>
                                            </span>
                                            <span x-show="task.completed_at">
                                                <i class="fas fa-check mr-1"></i>
                                                Completed: <span x-text="formatDateTime(task.completed_at)"></span>
                                            </span>
                                        </div>
                                        
                                        <!-- Completion Notes -->
                                        <div x-show="task.status === 'completed' && task.completion_notes" class="mt-3 p-3 bg-green-50 rounded-md">
                                            <p class="text-sm text-green-800">
                                                <strong>Completion Notes:</strong> <span x-text="task.completion_notes"></span>
                                            </p>
                                        </div>
                                        
                                        <!-- Completion Photos -->
                                        <div x-show="task.status === 'completed' && task.completion_photos && task.completion_photos.length > 0" class="mt-3">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Completion Photos:</p>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                <template x-for="photo in task.completion_photos" :key="photo">
                                                    <img :src="photo" :alt="task.task_name + ' completion photo'" class="w-full h-20 object-cover rounded-md">
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="recentTasks.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks assigned yet</h3>
                            <p class="text-sm text-gray-500 mb-4">Assign tasks to this staff member to track their progress.</p>
                            <button @click="assignTask()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-plus mr-2"></i>
                                Assign First Task
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div x-show="activeTab === 'activity'" class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Log</h3>
                    <div class="space-y-3">
                        <template x-for="activity in recentNotifications" :key="activity.id">
                            <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-circle text-blue-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="activity.action_description"></p>
                                    <p class="text-xs text-gray-500" x-text="activity.time_ago"></p>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="recentNotifications.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No activity yet</h3>
                            <p class="text-sm text-gray-500">Activity will appear here once the staff member starts working.</p>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div x-show="activeTab === 'notifications'" class="space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                        <button @click="sendNotification()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-bell mr-2"></i>
                            Send Notification
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="notification in recentNotifications" :key="notification.id">
                            <div class="border border-gray-200 rounded-lg p-4" :class="{ 'bg-red-50 border-red-200': notification.priority === 'urgent' }">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="font-medium text-gray-900" x-text="notification.title"></h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                  :class="getPriorityClass(notification.priority)" x-text="notification.priority"></span>
                                            <span x-show="notification.is_read" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Read
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-500" x-text="notification.time_ago"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="recentNotifications.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-bell text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications sent</h3>
                            <p class="text-sm text-gray-500 mb-4">Send notifications to communicate with this staff member.</p>
                            <button @click="sendNotification()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-bell mr-2"></i>
                                Send First Notification
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Permissions Tab -->
                <div x-show="activeTab === 'permissions'" class="space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                        <button @click="savePermissions()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Booking & Guest Management -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Booking & Guest Management</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_bookings" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">View Bookings Calendar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_guest_details" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">View Guest Details</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_guest_services" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Update Guest Services</span>
                                </label>
                            </div>
                        </div>

                        <!-- Task Management -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Task Management</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_assigned_tasks" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">View Assigned Tasks</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_task_status" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Update Task Status</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.upload_task_photos" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Upload Task Photos</span>
                                </label>
                            </div>
                        </div>

                        <!-- Cleaning & Maintenance -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Cleaning & Maintenance</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.access_cleaning_checklists" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Access Cleaning Checklists</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.execute_checklists" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Execute Checklists</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.update_checklist_progress" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Update Checklist Progress</span>
                                </label>
                            </div>
                        </div>

                        <!-- Communication & Reporting -->
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-3">Communication & Reporting</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.receive_notifications" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Receive Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.add_task_notes" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Add Task Notes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.report_issues" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Report Issues</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="permissions.view_activity_logs" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">View Activity Logs</span>
                                </label>
                            </div>
                        </div>
                    </div>
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
function staffDetails() {
    return {
        activeTab: 'tasks',
        showTaskModal: false,
        showNotificationModal: false,
        staffAssignment: @json($staffAssignment),
        recentTasks: @json($recentTasks),
        recentNotifications: @json($recentNotifications),
        taskStats: @json($taskStats),
        completionRate: {{ $completionRate }},
        permissions: @json($staffAssignment->staffPermissions->pluck('is_granted', 'permission_key')->toArray()),
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
        
        assignTask() {
            this.showTaskModal = true;
        },
        
        async submitTaskAssignment() {
            try {
                const response = await fetch(`/owner/staff/${this.staffAssignment.id}/assign-task`, {
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
                    this.loadData();
                    this.showNotification('Task assigned successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to assign task', 'error');
                }
            } catch (error) {
                console.error('Error assigning task:', error);
                this.showNotification('Failed to assign task', 'error');
            }
        },
        
        sendNotification() {
            this.showNotificationModal = true;
        },
        
        async submitNotification() {
            try {
                const response = await fetch(`/owner/staff/${this.staffAssignment.id}/send-notification`, {
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
                    this.loadData();
                    this.showNotification('Notification sent successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to send notification', 'error');
                }
            } catch (error) {
                console.error('Error sending notification:', error);
                this.showNotification('Failed to send notification', 'error');
            }
        },
        
        async savePermissions() {
            try {
                const response = await fetch(`/owner/staff/${this.staffAssignment.id}/update-permissions`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ permissions: this.permissions })
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showNotification('Permissions updated successfully', 'success');
                } else {
                    this.showNotification(result.error || 'Failed to update permissions', 'error');
                }
            } catch (error) {
                console.error('Error updating permissions:', error);
                this.showNotification('Failed to update permissions', 'error');
            }
        },
        
        async loadData() {
            // Reload data after changes
            window.location.reload();
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
                'completed': 'bg-green-100 text-green-800',
                'in_progress': 'bg-blue-100 text-blue-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800',
                'suspended': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
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
