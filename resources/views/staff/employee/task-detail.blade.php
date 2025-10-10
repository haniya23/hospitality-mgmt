@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                <p class="mt-2 text-gray-600">Task Details</p>
            </div>
            <a href="{{ route('staff.my-tasks') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Information --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Task Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($task->description)
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Description</p>
                            <p class="text-gray-900">{{ $task->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Task Type</p>
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</p>
                        </div>
                        @if($task->location)
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Location</p>
                                <p class="font-medium">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ $task->location }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($task->checklist_items)
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Checklist</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($task->checklist_items as $item)
                                    <li class="text-gray-700">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($task->rejection_reason)
                        <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded">
                            <p class="text-sm font-semibold text-red-800 mb-1">Rejected - Rework Required</p>
                            <p class="text-sm text-red-700">{{ $task->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Complete Task Form --}}
            @if($task->status === 'in_progress' || $task->status === 'rejected')
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                        <h2 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ $task->status === 'rejected' ? 'Rework and' : '' }} Complete Task
                        </h2>
                    </div>
                    <div class="p-6">
                        {{-- Upload Photos --}}
                        <form action="{{ route('staff.tasks.upload-proof', $task) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Proof Photos {{ $task->requires_photo_proof ? '*' : '(Optional)' }}
                            </label>
                            <input type="file" name="photos[]" accept="image/*" multiple 
                                {{ $task->requires_photo_proof ? 'required' : '' }}
                                class="w-full border-gray-300 rounded-lg">
                            <input type="hidden" name="media_type" value="proof">
                            <p class="text-xs text-gray-500 mt-1">You can upload up to 5 photos (max 5MB each)</p>
                            <button type="submit" class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-upload mr-2"></i> Upload Photos
                            </button>
                        </form>

                        {{-- Completion Form --}}
                        <form action="{{ route('staff.tasks.complete', $task) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes</label>
                                <textarea name="completion_notes" rows="4" 
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                    placeholder="Describe what you did, any issues encountered, etc..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                                <i class="fas fa-check-circle mr-2"></i> Mark as Completed
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Uploaded Photos --}}
            @if($task->media->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold">Uploaded Photos ({{ $task->media->count() }})</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($task->media as $media)
                                <div class="relative group">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $media->caption }}" 
                                        class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition rounded-lg flex items-center justify-center">
                                        <a href="{{ $media->getUrl() }}" target="_blank" 
                                            class="text-white opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-search-plus text-2xl"></i>
                                        </a>
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 rounded">{{ ucfirst($media->media_type) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Activity Log --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Activity Log</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($task->logs as $log)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-history text-gray-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm">
                                        <span class="font-semibold">{{ $log->getPerformerName() }}</span>
                                        <span class="{{ $log->getActionColor() }}">{{ $log->getActionLabel() }}</span>
                                        @if($log->from_status && $log->to_status)
                                            <span class="text-gray-500">
                                                ({{ $log->from_status }} → {{ $log->to_status }})
                                            </span>
                                        @endif
                                    </p>
                                    @if($log->notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $log->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $log->performed_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Quick Info --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Details</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getPriorityBadgeColor() }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $task->getStatusBadgeColor() }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Created By</p>
                        <p class="font-medium">{{ $task->creator->name }}</p>
                    </div>

                    @if($task->assignedBy)
                        <div>
                            <p class="text-sm text-gray-500">Assigned By</p>
                            <p class="font-medium">{{ $task->assignedBy->user->name }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500">Scheduled</p>
                        <p class="font-medium">{{ $task->scheduled_at->format('M d, Y h:i A') }}</p>
                    </div>

                    @if($task->due_at)
                        <div>
                            <p class="text-sm text-gray-500">Due By</p>
                            <p class="font-medium {{ $task->isOverdue() ? 'text-red-600' : '' }}">
                                {{ $task->due_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if($task->started_at)
                        <div>
                            <p class="text-sm text-gray-500">Started</p>
                            <p class="font-medium">{{ $task->started_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif

                    @if($task->completed_at)
                        <div>
                            <p class="text-sm text-gray-500">Completed</p>
                            <p class="font-medium">{{ $task->completed_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif

                    @if($task->verified_at)
                        <div>
                            <p class="text-sm text-gray-500">Verified</p>
                            <p class="font-medium text-green-600">{{ $task->verified_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($task->status === 'assigned')
                        <form action="{{ route('staff.tasks.start', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-play mr-2"></i> Start Task
                            </button>
                        </form>
                    @elseif($task->status === 'completed')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                            <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-sm font-semibold text-yellow-800">Awaiting Verification</p>
                            <p class="text-xs text-yellow-700 mt-1">Your supervisor will review this task soon</p>
                        </div>
                    @elseif($task->status === 'verified')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm font-semibold text-green-800">Task Verified</p>
                            <p class="text-xs text-green-700 mt-1">Great job! Task completed successfully</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="lg:col-span-1">
            @if($task->verification_notes)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm font-semibold text-green-800 mb-2">
                        <i class="fas fa-comment-check mr-2"></i> Verification Notes
                    </p>
                    <p class="text-sm text-green-700">{{ $task->verification_notes }}</p>
                </div>
            @endif

            @if($task->completion_notes)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <p class="text-sm font-semibold mb-2">Your Completion Notes</p>
                    <p class="text-sm text-gray-700">{{ $task->completion_notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

