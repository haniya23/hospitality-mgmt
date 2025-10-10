@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">All Tasks</h1>
            <p class="mt-2 text-gray-600">Manage and track all tasks</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Create Task
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department_id" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                <select name="assigned_to" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Staff</option>
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>
                            {{ $member->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-times mr-2"></i> Clear
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
                            <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                            @if($task->description)
                                <p class="text-gray-600 mt-1">{{ Str::limit($task->description, 150) }}</p>
                            @endif
                            
                            <div class="flex flex-wrap items-center gap-3 mt-3">
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

                                @if($task->assignedStaff)
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-user mr-1"></i> {{ $task->assignedStaff->user->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400 italic">
                                        <i class="fas fa-user-slash mr-1"></i> Unassigned
                                    </span>
                                @endif

                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> {{ $task->scheduled_at->format('M d, h:i A') }}
                                </span>
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

                    <div class="flex items-center justify-between border-t pt-4">
                        <div class="text-sm text-gray-500">
                            Created by {{ $task->creator->name }} • {{ $task->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No tasks found</p>
                <a href="{{ route('tasks.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                    Create your first task →
                </a>
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

