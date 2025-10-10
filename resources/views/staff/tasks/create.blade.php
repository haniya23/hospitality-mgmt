@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Task</h1>
                <p class="mt-2 text-gray-600">Assign a new task to your team</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
            </a>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow-lg">
        <form action="{{ route('tasks.store') }}" method="POST" class="p-8">
            @csrf

            {{-- Task Information --}}
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                    Task Information
                </h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            placeholder="e.g., Clean Room 101, Fix AC in Lobby"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Detailed description of what needs to be done...">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Task Type *</label>
                            <select name="task_type" required
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="guest_service">Guest Service</option>
                                <option value="inspection">Inspection</option>
                                <option value="delivery">Delivery</option>
                                <option value="setup">Setup</option>
                                <option value="inventory">Inventory</option>
                                <option value="administrative">Administrative</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                            <select name="priority" required
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department_id"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                placeholder="e.g., Room 101, Main Lobby, Kitchen"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date/Time *</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', now()->format('Y-m-d\TH:i')) }}" required
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date/Time</label>
                            <input type="datetime-local" name="due_at" value="{{ old('due_at') }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign To (Optional)</label>
                        <select name="assigned_to"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Unassigned (will assign later)</option>
                            @foreach($availableStaff as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->user->name }} - {{ $staff->department?->name ?? 'No Dept' }} ({{ $staff->job_title ?? ucfirst($staff->staff_role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="requires_photo_proof" value="1" {{ old('requires_photo_proof') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" id="requires_photo">
                        <label for="requires_photo" class="ml-2 text-sm text-gray-700">
                            Require photo proof upon completion
                        </label>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-4 border-t pt-6">
                <a href="{{ route('tasks.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus-circle mr-2"></i> Create Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

