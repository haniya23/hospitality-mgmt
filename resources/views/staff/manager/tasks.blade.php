@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">All Tasks</h1>
            <p class="mt-2 text-gray-600">Monitor all tasks across your property</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Create Task
        </a>
    </div>

    {{-- Tasks Grid --}}
    <div class="grid grid-cols-1 gap-6">
        @forelse($tasks as $task)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
                                @if($task->description)
                                    <p class="text-gray-600 text-sm mt-1">{{ $task->description }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getStatusBadgeColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            @if($task->department)
                                <span class="inline-flex items-center text-gray-600">
                                    <i class="{{ $task->department->icon }} mr-1"></i> {{ $task->department->name }}
                                </span>
                            @endif

                            @if($task->assignedStaff)
                                <span class="text-gray-600">
                                    <i class="fas fa-user mr-1"></i> {{ $task->assignedStaff->user->name }}
                                </span>
                            @endif

                            @if($task->assignedBy)
                                <span class="text-gray-500">
                                    <i class="fas fa-user-tie mr-1"></i> Assigned by {{ $task->assignedBy->user->name }}
                                </span>
                            @endif

                            @if($task->location)
                                <span class="text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                </span>
                            @endif

                            <span class="text-gray-500">
                                <i class="fas fa-calendar mr-1"></i> {{ $task->scheduled_at->format('M d, h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-tasks text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No tasks found</p>
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

