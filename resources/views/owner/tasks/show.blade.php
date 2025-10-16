@extends('layouts.app')

@section('title', $task->title . ' - Task Details')

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600"><i class="fas fa-home"></i></a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('owner.tasks.index') }}" class="hover:text-blue-600">Tasks</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-700 font-medium">{{ $task->title }}</span>
        </nav>
    </div>

    <div class="bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border border-white/20">
        <!-- Header -->
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $task->title }}</h1>
                <div class="flex items-center gap-3">
                    @php
                        $statusColors = [
                            'assigned' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'verified' => 'bg-emerald-100 text-emerald-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $priorityColors = [
                            'urgent' => 'bg-red-500 text-white',
                            'high' => 'bg-orange-500 text-white',
                            'medium' => 'bg-yellow-500 text-white',
                            'low' => 'bg-green-500 text-white',
                        ];
                    @endphp
                    <span class="px-4 py-2 {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }} rounded-full text-sm font-bold">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                    <span class="px-4 py-2 {{ $priorityColors[$task->priority] ?? 'bg-gray-500 text-white' }} rounded-full text-sm font-bold">
                        {{ strtoupper($task->priority) }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('owner.tasks.edit', $task) }}" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all font-semibold">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>

        <!-- Task Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white/50 rounded-xl p-4 border border-white/30">
                <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-info-circle text-blue-600 mr-2"></i>Task Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Property:</span>
                        <span class="font-semibold">{{ $task->property->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Department:</span>
                        <span class="font-semibold">{{ $task->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</span>
                    </div>
                    @if($task->location)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Location:</span>
                        <span class="font-semibold">{{ $task->location }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white/50 rounded-xl p-4 border border-white/30">
                <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-user text-green-600 mr-2"></i>Assignment</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Assigned To:</span>
                        <a href="{{ route('owner.staff.show', $task->assignedStaff) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                            {{ $task->assignedStaff->user->name }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Role:</span>
                        <span class="font-semibold">{{ ucfirst($task->assignedStaff->staff_role) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Scheduled:</span>
                        <span class="font-semibold">{{ $task->scheduled_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Due:</span>
                        <span class="font-semibold {{ $task->due_at->isPast() && !in_array($task->status, ['completed', 'verified']) ? 'text-red-600' : '' }}">
                            {{ $task->due_at->format('M d, Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white/50 rounded-xl p-6 border border-white/30 mb-6">
            <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-align-left text-purple-600 mr-2"></i>Description</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description }}</p>
        </div>

        <!-- Timestamps -->
        @if($task->started_at || $task->completed_at || $task->verified_at)
        <div class="bg-white/50 rounded-xl p-6 border border-white/30">
            <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-clock text-orange-600 mr-2"></i>Timeline</h3>
            <div class="space-y-2 text-sm">
                @if($task->started_at)
                <div class="flex items-center gap-2">
                    <i class="fas fa-play text-yellow-600"></i>
                    <span>Started: {{ $task->started_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
                @if($task->completed_at)
                <div class="flex items-center gap-2">
                    <i class="fas fa-check text-green-600"></i>
                    <span>Completed: {{ $task->completed_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
                @if($task->verified_at)
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-double text-emerald-600"></i>
                    <span>Verified: {{ $task->verified_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Media -->
        @if($task->media->isNotEmpty())
        <div class="bg-white/50 rounded-xl p-6 border border-white/30 mt-6">
            <h3 class="font-bold text-gray-900 mb-3"><i class="fas fa-camera text-indigo-600 mr-2"></i>Proof Photos</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($task->media as $media)
                    <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all">
                        <img src="{{ Storage::url($media->file_path) }}" alt="Task proof" class="w-full h-32 object-cover">
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection

