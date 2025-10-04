@extends('layouts.app')

@section('title', 'Staff Profile - ' . $staffAssignment->user->name)

@section('header')
<x-page-header 
    title="Staff Profile" 
    subtitle="Detailed view of {{ $staffAssignment->user->name }}'s performance and activities" 
    icon="user-tie">
    
    <!-- Back Button -->
    <div class="flex items-center space-x-3 mb-4">
        <a href="{{ route('owner.staff.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Staff
        </a>
    </div>
    
    <x-stat-cards :cards="[
        [
            'value' => $taskStats['total'], 
            'label' => 'Total Tasks',
            'icon' => 'fas fa-tasks',
            'bgGradient' => 'from-blue-50 to-indigo-50',
            'accentColor' => 'bg-blue-500'
        ],
        [
            'value' => $taskStats['completed'], 
            'label' => 'Completed Tasks',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500'
        ],
        [
            'value' => $taskStats['overdue'], 
            'label' => 'Overdue Tasks',
            'icon' => 'fas fa-exclamation-triangle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500'
        ],
        [
            'value' => $completionRate, 
            'label' => 'Completion Rate',
            'icon' => 'fas fa-percentage',
            'bgGradient' => 'from-purple-50 to-violet-50',
            'accentColor' => 'bg-purple-500',
            'suffix' => '%'
        ]
    ]" />
</x-page-header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Staff Information Card -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center text-white font-bold text-2xl">
                    {{ substr($staffAssignment->user->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-semibold text-gray-900">{{ $staffAssignment->user->name }}</h3>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 text-sm text-gray-600 mt-2">
                        <span><i class="fas fa-phone mr-2"></i>{{ $staffAssignment->user->mobile_number }}</span>
                        <span><i class="fas fa-envelope mr-2"></i>{{ $staffAssignment->user->email }}</span>
                        <span><i class="fas fa-building mr-2"></i>{{ $staffAssignment->property->name }}</span>
                        <span><i class="fas fa-user-tag mr-2"></i>{{ $staffAssignment->role->name }}</span>
                        <span><i class="fas fa-calendar mr-2"></i>Started: {{ $staffAssignment->start_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('owner.attendance.staff', $staffAssignment->user->uuid) }}" 
                   class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium flex items-center justify-center">
                    <i class="fas fa-calendar-check mr-2"></i>Attendance
                </a>
                <a href="{{ route('owner.leave-requests.index', ['staff_id' => $staffAssignment->user->uuid]) }}" 
                   class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium flex items-center justify-center">
                    <i class="fas fa-calendar-times mr-2"></i>Leave Requests
                </a>
                <button onclick="assignTask()" 
                        class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>Assign Task
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button onclick="sendNotification()" 
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-bell text-blue-600"></i>
            </div>
            <h4 class="font-medium text-gray-900">Send Notification</h4>
            <p class="text-sm text-gray-500">Send a message</p>
        </button>
        
        <button onclick="viewPermissions()" 
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-key text-purple-600"></i>
            </div>
            <h4 class="font-medium text-gray-900">Manage Permissions</h4>
            <p class="text-sm text-gray-500">Control access</p>
        </button>
        
        <button onclick="viewActivity()" 
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-history text-green-600"></i>
            </div>
            <h4 class="font-medium text-gray-900">Activity Log</h4>
            <p class="text-sm text-gray-500">View history</p>
        </button>
        
        <button onclick="editStaff()" 
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-edit text-orange-600"></i>
            </div>
            <h4 class="font-medium text-gray-900">Edit Profile</h4>
            <p class="text-sm text-gray-500">Update details</p>
        </button>
    </div>

    <!-- Recent Tasks -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Recent Tasks</h3>
            <button onclick="assignTask()" 
                    class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                <i class="fas fa-plus mr-1"></i>New Task
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentTasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $task->scheduled_at ? $task->scheduled_at->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewTask({{ $task->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                            title="View Task">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editTask({{ $task->id }})" 
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                            title="Edit Task">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks assigned</h3>
                                    <p class="text-sm text-gray-500">This staff member doesn't have any tasks yet.</p>
                                    <button onclick="assignTask()" 
                                            class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Assign First Task
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Recent Notifications</h3>
            <button onclick="sendNotification()" 
                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-plus mr-1"></i>Send Notification
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentNotifications as $notification)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $notification->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($notification->message, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $notification->type === 'urgent' ? 'bg-red-100 text-red-800' : 
                                       ($notification->type === 'info' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $notification->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $notification->is_read ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $notification->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="viewNotification({{ $notification->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                        title="View Notification">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-bell text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications sent</h3>
                                    <p class="text-sm text-gray-500">No notifications have been sent to this staff member yet.</p>
                                    <button onclick="sendNotification()" 
                                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Send First Notification
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Task Modal -->
<div id="assignTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Assign New Task</h3>
            </div>
            <form id="assignTaskForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Title</label>
                        <input type="text" id="task_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="task_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select id="task_priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" id="task_due_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAssignTaskModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
                        Assign Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div id="sendNotificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Send Notification</h3>
            </div>
            <form id="sendNotificationForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="notification_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="notification_message" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="notification_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="info">Information</option>
                            <option value="urgent">Urgent</option>
                            <option value="reminder">Reminder</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeSendNotificationModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                        Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function assignTask() {
    document.getElementById('assignTaskModal').classList.remove('hidden');
}

function sendNotification() {
    document.getElementById('sendNotificationModal').classList.remove('hidden');
}

function viewPermissions() {
    alert('Manage permissions functionality will be implemented here.');
}

function viewActivity() {
    alert('Activity log functionality will be implemented here.');
}

function editStaff() {
    window.location.href = "{{ route('owner.staff.edit', $staffAssignment->uuid) }}";
}

function viewTask(taskId) {
    alert('View task functionality will be implemented here. Task ID: ' + taskId);
}

function editTask(taskId) {
    alert('Edit task functionality will be implemented here. Task ID: ' + taskId);
}

function viewNotification(notificationId) {
    alert('View notification functionality will be implemented here. Notification ID: ' + notificationId);
}

function closeAssignTaskModal() {
    document.getElementById('assignTaskModal').classList.add('hidden');
}

function closeSendNotificationModal() {
    document.getElementById('sendNotificationModal').classList.add('hidden');
}

document.getElementById('assignTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('task_title').value,
        description: document.getElementById('task_description').value,
        priority: document.getElementById('task_priority').value,
        scheduled_at: document.getElementById('task_due_date').value,
        staff_assignment_id: {{ $staffAssignment->id }}
    };
    
    try {
        const response = await fetch('/owner/staff/{{ $staffAssignment->uuid }}/tasks', {
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
            alert('Task assigned successfully!');
            window.location.reload();
        } else {
            alert('Failed to assign task: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while assigning task.');
    }
});

document.getElementById('sendNotificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('notification_title').value,
        message: document.getElementById('notification_message').value,
        type: document.getElementById('notification_type').value,
        staff_assignment_id: {{ $staffAssignment->id }}
    };
    
    try {
        const response = await fetch('/owner/staff/{{ $staffAssignment->uuid }}/notifications', {
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
            alert('Notification sent successfully!');
            window.location.reload();
        } else {
            alert('Failed to send notification: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while sending notification.');
    }
});
</script>
@endsection