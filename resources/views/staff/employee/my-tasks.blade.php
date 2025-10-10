@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
        <p class="mt-2 text-gray-600">All your assigned tasks</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <select name="priority" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
                <a href="{{ route('staff.my-tasks') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Tasks List --}}
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                                    @if($task->description)
                                        <p class="text-gray-600 mt-1">{{ $task->description }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-4 mt-3">
                                @if($task->department)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                        style="background-color: {{ $task->department->color }}20; color: {{ $task->department->color }};">
                                        <i class="{{ $task->department->icon }} mr-1"></i> {{ $task->department->name }}
                                    </span>
                                @endif
                                
                                @if($task->location)
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                    </span>
                                @endif
                                
                                @if($task->due_at)
                                    <span class="text-sm {{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        <i class="fas fa-clock mr-1"></i> Due: {{ $task->due_at->format('M d, h:i A') }}
                                    </span>
                                @endif

                                @if($task->requires_photo_proof)
                                    <span class="text-sm text-purple-600">
                                        <i class="fas fa-camera mr-1"></i> Photo Required
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getStatusBadgeColor() }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between border-t pt-4">
                        <div class="text-sm text-gray-500">
                            @if($task->assignedBy)
                                Assigned by {{ $task->assignedBy->user->name }}
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if($task->status === 'assigned')
                                <form action="{{ route('staff.tasks.start', $task) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-play mr-2"></i> Start Task
                                    </button>
                                </form>
                            @elseif($task->status === 'in_progress')
                                <a href="{{ route('staff.tasks.show', $task) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i> Complete Task
                                </a>
                            @elseif($task->status === 'completed')
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg">
                                    <i class="fas fa-clock mr-2"></i> Awaiting Verification
                                </span>
                            @elseif($task->status === 'verified')
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                                    <i class="fas fa-check-circle mr-2"></i> Verified
                                </span>
                            @elseif($task->status === 'rejected')
                                <a href="{{ route('staff.tasks.show', $task) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    <i class="fas fa-redo mr-2"></i> Rework Required
                                </a>
                            @endif
                            <a href="{{ route('staff.tasks.show', $task) }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-eye mr-2"></i> Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-tasks text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No tasks found</p>
                <p class="text-gray-400 text-sm mt-2">Tasks assigned to you will appear here</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tasks->hasPages())
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
@endsection

