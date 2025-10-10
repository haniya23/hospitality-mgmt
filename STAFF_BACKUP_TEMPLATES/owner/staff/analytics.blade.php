@extends('layouts.app')

@section('title', 'Staff Analytics')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="staffAnalytics()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Staff Analytics</h1>
                    <p class="text-sm text-gray-600">Monitor staff performance and productivity metrics</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('owner.staff.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Staff
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Date Range:</label>
                <select x-model="dateRange" @change="loadAnalytics()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
                
                <label class="text-sm font-medium text-gray-700">Property:</label>
                <select x-model="selectedProperty" @change="loadAnalytics()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Properties</option>
                    <template x-for="property in properties" :key="property.id">
                        <option :value="property.id" x-text="property.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Analytics Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Staff</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="analytics.active_staff"></p>
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
                        <p class="text-sm font-medium text-gray-600">Tasks Completed</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="analytics.completed_tasks"></p>
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
                        <p class="text-sm font-medium text-gray-600">Avg. Task Time</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="analytics.avg_task_time"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-percentage text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                        <p class="text-2xl font-semibold text-gray-900" x-text="analytics.completion_rate + '%'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Performance Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Staff Performance Overview</h3>
            <div class="space-y-4">
                <template x-for="staff in staffPerformance" :key="staff.id">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold" x-text="staff.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900" x-text="staff.name"></h4>
                                    <p class="text-sm text-gray-600" x-text="staff.property_name"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900" x-text="staff.completion_rate + '%'"></p>
                                <p class="text-sm text-gray-600">Completion Rate</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-lg font-semibold text-gray-900" x-text="staff.total_tasks"></p>
                                <p class="text-xs text-gray-600">Total Tasks</p>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-green-600" x-text="staff.completed_tasks"></p>
                                <p class="text-xs text-gray-600">Completed</p>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-yellow-600" x-text="staff.in_progress_tasks"></p>
                                <p class="text-xs text-gray-600">In Progress</p>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-red-600" x-text="staff.overdue_tasks"></p>
                                <p class="text-xs text-gray-600">Overdue</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-500" :style="`width: ${staff.completion_rate}%`"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Task Type Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Task Type Breakdown</h3>
                <div class="space-y-3">
                    <template x-for="taskType in taskTypeBreakdown" :key="taskType.type">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full" :class="getTaskTypeColor(taskType.type)"></div>
                                <span class="text-sm font-medium text-gray-900" x-text="taskType.name"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600" x-text="taskType.count + ' tasks'"></span>
                                <span class="text-sm font-medium text-gray-900" x-text="taskType.percentage + '%'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Priority Distribution</h3>
                <div class="space-y-3">
                    <template x-for="priority in priorityDistribution" :key="priority.priority">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full" :class="getPriorityColor(priority.priority)"></div>
                                <span class="text-sm font-medium text-gray-900" x-text="priority.name"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600" x-text="priority.count + ' tasks'"></span>
                                <span class="text-sm font-medium text-gray-900" x-text="priority.percentage + '%'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                <template x-for="activity in recentActivity" :key="activity.id">
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-circle text-blue-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                            <p class="text-xs text-gray-500" x-text="activity.staff_name + ' • ' + activity.time_ago"></p>
                        </div>
                    </div>
                </template>
                
                <div x-show="recentActivity.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No recent activity</h3>
                    <p class="text-sm text-gray-500">Activity will appear here as staff members work on tasks.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function staffAnalytics() {
    return {
        dateRange: '30',
        selectedProperty: '',
        analytics: {
            active_staff: 0,
            completed_tasks: 0,
            avg_task_time: '0h 0m',
            completion_rate: 0
        },
        staffPerformance: [],
        taskTypeBreakdown: [],
        priorityDistribution: [],
        recentActivity: [],
        properties: @json(\App\Models\Property::where('owner_id', auth()->id())->where('status', 'active')->get()),
        
        init() {
            this.loadAnalytics();
        },
        
        async loadAnalytics() {
            try {
                const params = new URLSearchParams({
                    days: this.dateRange,
                    property_id: this.selectedProperty
                });
                
                const response = await fetch(`/owner/staff/analytics?${params}`);
                const data = await response.json();
                
                this.analytics = data.analytics || {};
                this.staffPerformance = data.staffPerformance || [];
                this.taskTypeBreakdown = data.taskTypeBreakdown || [];
                this.priorityDistribution = data.priorityDistribution || [];
                this.recentActivity = data.recentActivity || [];
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        },
        
        getTaskTypeColor(type) {
            const colors = {
                'cleaning': 'bg-blue-500',
                'maintenance': 'bg-green-500',
                'guest_service': 'bg-purple-500',
                'check_in': 'bg-yellow-500',
                'check_out': 'bg-orange-500',
                'inspection': 'bg-red-500',
                'other': 'bg-gray-500',
            };
            return colors[type] || 'bg-gray-500';
        },
        
        getPriorityColor(priority) {
            const colors = {
                'urgent': 'bg-red-500',
                'high': 'bg-orange-500',
                'medium': 'bg-yellow-500',
                'low': 'bg-green-500',
            };
            return colors[priority] || 'bg-gray-500';
        }
    }
}
</script>
@endsection
