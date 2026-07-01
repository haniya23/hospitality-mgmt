@extends('layouts.app')

@section('title', 'Edit Task - Stay loops')
@section('page-title', 'Edit Task')

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">
                <i class="fas fa-home"></i>
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('owner.tasks.index') }}" class="hover:text-blue-600">Tasks</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('owner.tasks.show', $task) }}" class="hover:text-blue-600">{{ $task->title }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-700 font-medium">Edit Task</span>
        </nav>
    </div>

    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border border-white/20">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Edit Task</h2>
                <p class="text-sm text-blue-600 font-medium mt-1">Update details or status of this task</p>
            </div>
        </div>

        <form action="{{ route('owner.tasks.update', $task) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Task Status & Property -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Status & Property
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Property
                        </label>
                        <select name="property_id" disabled
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-500 bg-gray-50 cursor-not-allowed">
                            <option value="{{ $task->property_id }}">{{ $task->property->name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $task->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Task Details -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-align-left text-blue-600 mr-2"></i>Task Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Task Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required
                            value="{{ old('title', $task->title) }}"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., Clean Room 101">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="4" required
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="Provide detailed instructions...">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Task Type <span class="text-red-500">*</span>
                            </label>
                            <select name="task_type" required
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                                <option value="cleaning" {{ old('task_type', $task->task_type) === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                <option value="maintenance" {{ old('task_type', $task->task_type) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inspection" {{ old('task_type', $task->task_type) === 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="delivery" {{ old('task_type', $task->task_type) === 'delivery' ? 'selected' : '' }}>Delivery</option>
                                <option value="customer_service" {{ old('task_type', $task->task_type) === 'customer_service' ? 'selected' : '' }}>Customer Service</option>
                                <option value="administrative" {{ old('task_type', $task->task_type) === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                <option value="other" {{ old('task_type', $task->task_type) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" required
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>🟢 Low</option>
                                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>🟠 High</option>
                                <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                            <input type="text" name="location"
                                value="{{ old('location', $task->location) }}"
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., Room 101, Lobby">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>Schedule
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Scheduled Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="scheduled_at" required
                            value="{{ old('scheduled_at', $task->scheduled_at ? $task->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Due Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="due_at" required
                            value="{{ old('due_at', $task->due_at ? $task->due_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('owner.tasks.show', $task) }}" 
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                    <i class="fas fa-check mr-2"></i>Update Task
                </button>
            </div>
        </form>
    </div>
@endsection
