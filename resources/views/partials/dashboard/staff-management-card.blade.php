@if(auth()->user()->isOwner())
@php
    $user = auth()->user();
    $propertyIds = $user->properties()->pluck('id');
    $totalStaff = \App\Models\StaffMember::whereIn('property_id', $propertyIds)->count();
    $activeStaff = \App\Models\StaffMember::whereIn('property_id', $propertyIds)->where('status', 'active')->count();
    $todaysTasks = \App\Models\Task::whereIn('property_id', $propertyIds)->whereDate('scheduled_at', today())->count();
@endphp

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Staff Management</h3>
            <a href="{{ route('owner.staff.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                View All →
            </a>
        </div>
    </div>
    
    <div class="p-4">
        <!-- Staff Stats Grid -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ $totalStaff }}</div>
                <div class="text-xs text-gray-600">Total Staff</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $activeStaff }}</div>
                <div class="text-xs text-gray-600">Active</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $todaysTasks }}</div>
                <div class="text-xs text-gray-600">Today's Tasks</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('owner.staff.create') }}" class="flex items-center justify-center p-3 rounded-xl bg-gradient-to-r from-indigo-50 to-blue-50 hover:from-indigo-100 hover:to-blue-100 transition-all duration-300 border border-indigo-200 group">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                        <i class="fas fa-user-plus text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Add Staff</span>
                </div>
            </a>
            
            <a href="{{ route('manager.analytics') }}" class="flex items-center justify-center p-3 rounded-xl bg-gradient-to-r from-purple-50 to-violet-50 hover:from-purple-100 hover:to-violet-100 transition-all duration-300 border border-purple-200 group">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Analytics</span>
                </div>
            </a>
        </div>
        
        @if($totalStaff > 0)
        <!-- Recent Staff Activity -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-semibold text-gray-700">Recent Activity</h4>
                <span class="text-xs text-gray-500">Last 7 days</span>
            </div>
            <div class="space-y-2">
                @php
                    $propertyIds = $user->properties()->pluck('id');
                    $recentActivity = \App\Models\Task::whereIn('property_id', $propertyIds)
                        ->with('assignedStaff.user')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                @endphp
                
                @forelse($recentActivity as $task)
                <div class="flex items-center space-x-3 p-2 rounded-lg bg-gray-50">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-tasks text-indigo-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 truncate">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if(in_array($task->status, ['completed', 'verified'])) bg-green-100 text-green-800
                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-users text-gray-400 text-lg"></i>
                    </div>
                    <p class="text-sm text-gray-500">No recent activity</p>
                    <p class="text-xs text-gray-400">Staff tasks will appear here</p>
                </div>
                @endforelse
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-indigo-600 text-2xl"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">No Staff Yet</h4>
            <p class="text-sm text-gray-600 mb-4">Start building your team by adding staff members to manage your properties.</p>
            <a href="{{ route('owner.staff.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-user-plus mr-2"></i>
                Add Your First Staff Member
            </a>
        </div>
        @endif
    </div>
</div>
@endif
