@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Supervisor Dashboard</h1>
        <p class="mt-2 text-gray-600">{{ $supervisor->department?->name ?? 'Department' }} - {{ $supervisor->property->name }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">My Team</p>
                    <p class="text-2xl font-bold">{{ $stats['team_size'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Verification</p>
                    <p class="text-2xl font-bold">{{ $stats['pending_verification'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Today's Tasks</p>
                    <p class="text-2xl font-bold">{{ $stats['tasks_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Overdue</p>
                    <p class="text-2xl font-bold">{{ $stats['overdue_tasks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- My Team --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold">My Team ({{ $myTeam->count() }})</h2>
                <a href="{{ route('supervisor.my-team') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6">
                @forelse($myTeam as $member)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-semibold">
                                    {{ substr($member->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">{{ $member->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $member->job_title ?? 'Staff Member' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $member->getStatusBadgeColor() }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No team members yet</p>
                @endforelse
            </div>
        </div>

        {{-- Tasks Requiring Verification --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Pending Verification ({{ $tasksToVerify->count() }})</h2>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($tasksToVerify as $task)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-medium">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-500">By: {{ $task->assignedStaff->user->name }}</p>
                                <p class="text-xs text-gray-400">Completed: {{ $task->completed_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <form action="{{ route('supervisor.tasks.verify', $task) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    <i class="fas fa-check mr-1"></i> Verify
                                </button>
                            </form>
                            <button onclick="rejectTask({{ $task->id }})" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                <i class="fas fa-times mr-1"></i> Reject
                            </button>
                            @if($task->media->count() > 0)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded">
                                    <i class="fas fa-camera mr-1"></i> {{ $task->media->count() }} photos
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No tasks pending verification</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-plus-circle text-3xl mb-2"></i>
            <p class="font-semibold">Create Task</p>
        </a>
        <a href="{{ route('supervisor.tasks') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-tasks text-3xl mb-2"></i>
            <p class="font-semibold">All Tasks</p>
        </a>
        <a href="{{ route('supervisor.my-team') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-users text-3xl mb-2"></i>
            <p class="font-semibold">My Team</p>
        </a>
    </div>
</div>
@endsection

