@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manager Dashboard</h1>
        <p class="mt-2 text-gray-600">{{ $manager->property->name }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Staff</p>
                    <p class="text-2xl font-bold">{{ $stats['total_staff'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Completed Tasks</p>
                    <p class="text-2xl font-bold">{{ $stats['completed_tasks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Tasks</p>
                    <p class="text-2xl font-bold">{{ $stats['pending_tasks'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Today's Tasks</p>
                    <p class="text-2xl font-bold">{{ $stats['tasks_today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Supervisors --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Supervisors ({{ $supervisors->count() }})</h2>
                <a href="{{ route('manager.supervisors') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6">
                @forelse($supervisors->take(5) as $supervisor)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-semibold">
                                    {{ substr($supervisor->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">{{ $supervisor->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $supervisor->department?->name ?? 'No Department' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $supervisor->getStatusBadgeColor() }}">
                            {{ ucfirst($supervisor->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No supervisors yet</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Tasks --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Recent Tasks</h2>
                <a href="{{ route('manager.tasks') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6">
                @forelse($tasks->take(5) as $task)
                    <div class="py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="font-medium">{{ $task->title }}</h3>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                @if($task->assignedStaff)
                                    {{ $task->assignedStaff->user->name }}
                                @else
                                    <span class="text-gray-400">Unassigned</span>
                                @endif
                            </span>
                            <span class="px-2 py-1 rounded text-xs {{ $task->getStatusBadgeColor() }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent tasks</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-plus-circle text-3xl mb-2"></i>
            <p class="font-semibold">Create Task</p>
        </a>
        <a href="{{ route('manager.analytics') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-chart-bar text-3xl mb-2"></i>
            <p class="font-semibold">View Analytics</p>
        </a>
        <a href="{{ route('owner.staff.index') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-user-plus text-3xl mb-2"></i>
            <p class="font-semibold">Manage Staff</p>
        </a>
        <a href="{{ route('staff.permissions.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-shield-alt text-3xl mb-2"></i>
            <p class="font-semibold">Access Management</p>
        </a>
    </div>
</div>
@endsection

