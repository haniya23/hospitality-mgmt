@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Team's Tasks</h1>
            <p class="mt-2 text-gray-600">Manage and verify tasks for your team</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Create Task
        </a>
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
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                <select name="assigned_to" class="w-full border-gray-300 rounded-lg">
                    <option value="">All Team Members</option>
                    @foreach($myTeam as $member)
                        <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>
                            {{ $member->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
                <a href="{{ route('supervisor.tasks') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Tasks List --}}
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
                            @if($task->description)
                                <p class="text-gray-600 mt-1">{{ $task->description }}</p>
                            @endif
                            
                            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                                @if($task->assignedStaff)
                                    <span class="text-gray-600">
                                        <i class="fas fa-user mr-1"></i> {{ $task->assignedStaff->user->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">
                                        <i class="fas fa-user-slash mr-1"></i> Unassigned
                                    </span>
                                @endif

                                @if($task->location)
                                    <span class="text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                    </span>
                                @endif

                                <span class="text-gray-500">
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

                    {{-- Actions --}}
                    @if($task->status === 'completed')
                        <div class="flex gap-3 border-t pt-4">
                            <form action="{{ route('supervisor.tasks.verify', $task) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                                    <i class="fas fa-check-circle mr-2"></i> Verify Task
                                </button>
                            </form>
                            <button onclick="openRejectModal({{ $task->id }})" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                                <i class="fas fa-times-circle mr-2"></i> Reject
                            </button>
                        </div>
                    @elseif($task->status === 'pending' && !$task->assigned_to)
                        <div class="border-t pt-4">
                            <button onclick="openAssignModal({{ $task->id }})" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-user-plus mr-2"></i> Assign to Team Member
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
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

{{-- Assign Modal (Add this at the bottom) --}}
<script>
function openAssignModal(taskId) {
    // Simple prompt for now - can be enhanced with a proper modal
    const staffName = prompt('Enter staff member name or ID to assign:');
    if (staffName) {
        // Submit form programmatically
        alert('Task assignment feature - implement with proper modal');
    }
}

function openRejectModal(taskId) {
    const reason = prompt('Enter rejection reason:');
    if (reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/supervisor/tasks/${taskId}/reject`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'rejection_reason';
        reasonInput.value = reason;
        
        form.appendChild(csrf);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

