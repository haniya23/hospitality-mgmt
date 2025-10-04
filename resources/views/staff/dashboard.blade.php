@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="staffDashboard()">
    <!-- Welcome Section -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">
                    @if(Auth::user()->getActiveStaffAssignments()->count() > 0)
                        {{ Auth::user()->getActiveStaffAssignments()->first()->role->name }} at {{ Auth::user()->getActiveStaffAssignments()->first()->property->name }}
                    @else
                        Staff Member
                    @endif
                </p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-sm text-gray-500">Today</div>
                <div class="text-lg sm:text-xl font-semibold text-gray-900">{{ now()->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-6">
        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-blue-100">
                    <i class="fas fa-tasks text-blue-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Today's Tasks</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.todaysTasks"></p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Overdue</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.overdueTasks"></p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.completedToday"></p>
                </div>
            </div>
        </div>

        @if(Auth::user()->hasPermission('manage_guest_services') || Auth::user()->hasPermission('manage_checkin_checkout'))
        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-indigo-100">
                    <i class="fas fa-sign-in-alt text-indigo-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Check-ins</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.todaysCheckins"></p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-purple-100">
                    <i class="fas fa-sign-out-alt text-purple-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Check-outs</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.todaysCheckouts"></p>
                </div>
            </div>
        </div>
        @endif

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6 hover:scale-105 transition-transform duration-300">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-orange-100">
                    <i class="fas fa-bell text-orange-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Notifications</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900" x-text="stats.unreadNotifications"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Tasks -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Today's Tasks</h3>
            <a href="{{ route('staff.tasks') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
        </div>
        
        <div class="space-y-3" x-show="todaysTasks.length > 0">
            <template x-for="task in todaysTasks.slice(0, 5)" :key="task.id">
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-2 h-2 rounded-full" 
                                 :class="{
                                     'bg-red-500': task.priority === 'high',
                                     'bg-yellow-500': task.priority === 'medium',
                                     'bg-green-500': task.priority === 'low'
                                 }"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm sm:text-base font-medium text-gray-900 truncate" x-text="task.title"></p>
                            <p class="text-xs sm:text-sm text-gray-500 truncate" x-text="task.description"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                              :class="{
                                  'bg-yellow-100 text-yellow-800': task.status === 'pending',
                                  'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                  'bg-green-100 text-green-800': task.status === 'completed'
                              }"
                              x-text="task.status.replace('_', ' ').toUpperCase()"></span>
                        <button @click="startTask(task.id)" 
                                class="text-xs px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                x-show="task.status === 'pending'">
                            Start
                        </button>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="todaysTasks.length === 0" class="text-center py-8">
            <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
            <p class="text-gray-500">No tasks for today!</p>
        </div>
    </div>

    @if(Auth::user()->hasPermission('manage_guest_services') || Auth::user()->hasPermission('manage_checkin_checkout'))
    <!-- Today's Guest Service -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Today's Guest Service</h3>
            <a href="{{ route('staff.guest-service.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Today's Check-ins -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-indigo-900">Check-ins</h4>
                    <i class="fas fa-sign-in-alt text-indigo-600 text-xl"></i>
                </div>
                
                <div class="space-y-3" x-show="todaysCheckins.length > 0">
                    <template x-for="checkin in todaysCheckins.slice(0, 3)" :key="checkin.id">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="checkin.guest_name"></p>
                                <p class="text-xs text-gray-600" x-text="checkin.property_name"></p>
                                <p class="text-xs text-indigo-600 font-medium" x-text="checkin.check_in_time"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs px-2 py-1 rounded-full font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': checkin.status === 'checked_in',
                                          'bg-yellow-100 text-yellow-800': checkin.status === 'confirmed'
                                      }"
                                      x-text="checkin.status.replace('_', ' ').toUpperCase()"></span>
                                <button @click="processCheckin(checkin.id)" 
                                        class="text-xs px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                                        x-show="checkin.status === 'confirmed'">
                                    Check In
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="todaysCheckins.length === 0" class="text-center py-4">
                    <i class="fas fa-calendar-check text-2xl text-indigo-400 mb-2"></i>
                    <p class="text-sm text-indigo-600">No check-ins today</p>
                </div>
            </div>

            <!-- Today's Check-outs -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-purple-900">Check-outs</h4>
                    <i class="fas fa-sign-out-alt text-purple-600 text-xl"></i>
                </div>
                
                <div class="space-y-3" x-show="todaysCheckouts.length > 0">
                    <template x-for="checkout in todaysCheckouts.slice(0, 3)" :key="checkout.id">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="checkout.guest_name"></p>
                                <p class="text-xs text-gray-600" x-text="checkout.property_name"></p>
                                <p class="text-xs text-purple-600 font-medium" x-text="checkout.check_out_time"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs px-2 py-1 rounded-full font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': checkout.status === 'checked_out',
                                          'bg-yellow-100 text-yellow-800': checkout.status === 'checked_in'
                                      }"
                                      x-text="checkout.status.replace('_', ' ').toUpperCase()"></span>
                                <button @click="processCheckout(checkout.id)" 
                                        class="text-xs px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                                        x-show="checkout.status === 'checked_in'">
                                    Check Out
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="todaysCheckouts.length === 0" class="text-center py-4">
                    <i class="fas fa-calendar-times text-2xl text-purple-400 mb-2"></i>
                    <p class="text-sm text-purple-600">No check-outs today</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Notifications -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Recent Notifications</h3>
            <a href="{{ route('staff.notifications') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
        </div>
        
        <div class="space-y-3" x-show="recentNotifications.length > 0">
            <template x-for="notification in recentNotifications.slice(0, 3)" :key="notification.id">
                <div class="flex items-start p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-2 h-2 rounded-full mt-2" 
                             :class="{
                                 'bg-red-500': notification.priority === 'urgent',
                                 'bg-yellow-500': notification.priority === 'high',
                                 'bg-blue-500': notification.priority === 'medium',
                                 'bg-green-500': notification.priority === 'low'
                             }"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm sm:text-base font-medium text-gray-900" x-text="notification.title"></p>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                        <p class="text-xs text-gray-500 mt-2" x-text="new Date(notification.created_at).toLocaleString()"></p>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="recentNotifications.length === 0" class="text-center py-8">
            <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">No notifications</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
            <a href="{{ route('staff.tasks') }}" class="flex flex-col items-center p-3 sm:p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <i class="fas fa-tasks text-blue-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-blue-700">View Tasks</span>
            </a>
            <a href="{{ route('staff.checklists') }}" class="flex flex-col items-center p-3 sm:p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <i class="fas fa-clipboard-check text-green-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-green-700">Checklists</span>
            </a>
            @if(Auth::user()->hasPermission('manage_guest_services') || Auth::user()->hasPermission('manage_checkin_checkout'))
            <a href="{{ route('staff.guest-service.index') }}" class="flex flex-col items-center p-3 sm:p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                <i class="fas fa-concierge-bell text-indigo-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-indigo-700">Guest Service</span>
            </a>
            @endif
            <a href="{{ route('staff.notifications') }}" class="flex flex-col items-center p-3 sm:p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors">
                <i class="fas fa-bell text-orange-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-orange-700">Notifications</span>
            </a>
            <a href="{{ route('staff.activity') }}" class="flex flex-col items-center p-3 sm:p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <i class="fas fa-history text-purple-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-purple-700">Activity</span>
            </a>
        </div>
    </div>
</div>

<script>
function staffDashboard() {
    return {
        stats: {
            todaysTasks: {{ Auth::user()->getTodaysTasks()->count() }},
            overdueTasks: {{ Auth::user()->getOverdueTasks()->count() }},
            completedToday: {{ Auth::user()->staffTasks()->whereDate('completed_at', today())->count() }},
            unreadNotifications: {{ Auth::user()->getUnreadNotificationsCount() }},
            todaysCheckins: {{ count($todaysCheckins ?? []) }},
            todaysCheckouts: {{ count($todaysCheckouts ?? []) }}
        },
        todaysTasks: @json($todaysTasks),
        recentNotifications: @json($unreadNotifications->take(5)),
        todaysCheckins: @json($todaysCheckins ?? []),
        todaysCheckouts: @json($todaysCheckouts ?? []),

        async startTask(taskId) {
            try {
                const response = await fetch(`/staff/tasks/${taskId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showNotification('Task started successfully!', 'success');
                    
                    // Update the task status
                    const task = this.todaysTasks.find(t => t.id === taskId);
                    if (task) {
                        task.status = 'in_progress';
                    }
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to start task', 'error');
                }
            } catch (error) {
                console.error('Error starting task:', error);
                this.showNotification('An error occurred while starting the task', 'error');
            }
        },

        async processCheckin(reservationId) {
            try {
                const response = await fetch(`/staff/guest-service/check-in/${reservationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showNotification('Check-in processed successfully!', 'success');
                    
                    // Update the check-in status
                    const checkin = this.todaysCheckins.find(c => c.id === reservationId);
                    if (checkin) {
                        checkin.status = 'checked_in';
                    }
                    
                    // Refresh stats
                    this.stats.todaysCheckins--;
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to process check-in', 'error');
                }
            } catch (error) {
                console.error('Error processing check-in:', error);
                this.showNotification('An error occurred while processing check-in', 'error');
            }
        },

        async processCheckout(reservationId) {
            try {
                const response = await fetch(`/staff/guest-service/check-out/${reservationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showNotification('Check-out processed successfully!', 'success');
                    
                    // Update the check-out status
                    const checkout = this.todaysCheckouts.find(c => c.id === reservationId);
                    if (checkout) {
                        checkout.status = 'checked_out';
                    }
                    
                    // Refresh stats
                    this.stats.todaysCheckouts--;
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to process check-out', 'error');
                }
            } catch (error) {
                console.error('Error processing check-out:', error);
                this.showNotification('An error occurred while processing check-out', 'error');
            }
        },

        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endsection