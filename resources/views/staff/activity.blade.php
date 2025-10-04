@extends('layouts.staff')

@section('title', 'Staff Activity Log')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="staffActivity()">
    <!-- Header -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Activity Log</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Track your daily activities and task history</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-calendar mr-1"></i>{{ now()->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-blue-100">
                    <i class="fas fa-tasks text-blue-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Tasks Completed</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ Auth::user()->staffTasks()->where('staff_tasks.status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-green-100">
                    <i class="fas fa-clipboard-check text-green-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Checklists Done</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ Auth::user()->checklistExecutions()->where('checklist_executions.status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-purple-100">
                    <i class="fas fa-clock text-purple-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Hours Worked</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ Auth::user()->getTodaysActivity()->count() * 0.5 }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-orange-100">
                    <i class="fas fa-star text-orange-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ Auth::user()->getTaskCompletionRate(7) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Recent Activity</h3>
        
        @if($activities->count() > 0)
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex items-start p-3 sm:p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                 style="background-color: {{ 
                                     $activity->action === 'task_completed' ? '#10b981' : 
                                     ($activity->action === 'checklist_completed' ? '#3b82f6' : 
                                     ($activity->action === 'task_started' ? '#f59e0b' : '#6b7280')) 
                                 }}20">
                                <i class="fas {{ 
                                    $activity->action === 'task_completed' ? 'fa-check' : 
                                    ($activity->action === 'checklist_completed' ? 'fa-clipboard-check' : 
                                    ($activity->action === 'task_started' ? 'fa-play' : 'fa-info')) 
                                }} text-sm" style="color: {{ 
                                    $activity->action === 'task_completed' ? '#10b981' : 
                                    ($activity->action === 'checklist_completed' ? '#3b82f6' : 
                                    ($activity->action === 'task_started' ? '#f59e0b' : '#6b7280')) 
                                }}"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm sm:text-base font-medium text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                    </h4>
                                    @if($activity->description)
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $activity->description }}</p>
                                    @endif
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>{{ $activity->created_at->diffForHumans() }}
                                        </span>
                                        @if($activity->property)
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-building mr-1"></i>{{ $activity->property->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $activity->created_at->format('H:i') }}</div>
                                    <div class="text-xs text-gray-400">{{ $activity->created_at->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No activity yet</h3>
                <p class="text-gray-500">Your activity log will appear here as you complete tasks and checklists.</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('staff.tasks') }}" class="flex flex-col items-center p-3 sm:p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <i class="fas fa-tasks text-blue-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-blue-700">View Tasks</span>
            </a>
            <a href="{{ route('staff.checklists') }}" class="flex flex-col items-center p-3 sm:p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <i class="fas fa-clipboard-check text-green-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-green-700">Checklists</span>
            </a>
            <a href="{{ route('staff.notifications') }}" class="flex flex-col items-center p-3 sm:p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors">
                <i class="fas fa-bell text-orange-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-orange-700">Notifications</span>
            </a>
            <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center p-3 sm:p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <i class="fas fa-tachometer-alt text-purple-600 text-lg sm:text-xl mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-purple-700">Dashboard</span>
            </a>
        </div>
    </div>
</div>

<script>
function staffActivity() {
    return {
        // Activity-specific functionality can be added here
    }
}
</script>
@endsection
