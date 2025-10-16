@extends('layouts.app')

@section('title', 'Task Management - Stay loops')
@section('page-title', 'Task Management')

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">
                <i class="fas fa-home"></i>
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-700 font-medium">Tasks</span>
        </nav>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border border-white/20 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-tasks text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Task Management</h2>
                    <p class="text-sm text-purple-600 font-medium mt-1">{{ $tasks->total() }} total tasks</p>
                </div>
            </div>
            <a href="{{ route('owner.tasks.create') }}" 
                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>Assign Task
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Property</label>
                <select name="property_id" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:ring-2 focus:ring-purple-500">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Priority</label>
                <select name="priority" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl shadow-sm py-2.5 px-3 text-sm text-gray-900 focus:ring-2 focus:ring-purple-500">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>🟢 Low</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
        @forelse($tasks as $task)
            <div class="p-6 border-b border-gray-200 hover:bg-purple-50/50 transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <a href="{{ route('owner.tasks.show', $task) }}" class="text-xl font-bold text-gray-900 hover:text-purple-600 transition-colors">
                                {{ $task->title }}
                            </a>
                            
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

                            <span class="px-3 py-1 {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }} rounded-full text-xs font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            
                            <span class="px-3 py-1 {{ $priorityColors[$task->priority] ?? 'bg-gray-500 text-white' }} rounded-full text-xs font-bold">
                                {{ strtoupper($task->priority) }}
                            </span>
                        </div>

                        <p class="text-gray-600 mb-3">{{ Str::limit($task->description, 150) }}</p>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-building text-purple-600 mr-2"></i>
                                <span class="font-medium">{{ $task->property->name }}</span>
                            </div>
                            
                            <div class="flex items-center">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                <span class="font-medium">{{ $task->assignedStaff->user->name }}</span>
                            </div>

                            @if($task->department)
                            <div class="flex items-center">
                                <i class="fas fa-layer-group text-green-600 mr-2"></i>
                                <span>{{ $task->department->name }}</span>
                            </div>
                            @endif

                            <div class="flex items-center">
                                <i class="fas fa-calendar text-orange-600 mr-2"></i>
                                <span>Due: {{ $task->due_at->format('M d, Y H:i') }}</span>
                            </div>

                            @if($task->requires_photo_proof)
                            <div class="flex items-center">
                                <i class="fas fa-camera text-indigo-600 mr-2"></i>
                                <span>Photo required</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('owner.tasks.show', $task) }}" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-semibold">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('owner.tasks.edit', $task) }}" 
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all text-sm font-semibold">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <i class="fas fa-tasks text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No tasks yet</h3>
                <p class="text-gray-600 mb-6">Start by assigning tasks to your team members</p>
                <a href="{{ route('owner.tasks.create') }}" 
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Assign First Task
                </a>
            </div>
        @endforelse

        @if($tasks->hasPages())
        <div class="p-6 bg-gray-50">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>
@endsection

