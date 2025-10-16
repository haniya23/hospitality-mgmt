@extends('layouts.app')

@section('title', $staff->user->name . ' - Staff Details')
@section('page-title', 'Staff Details')

@push('scripts')
<script>
function taskModal() {
    return {
        showModal: false,
        scheduledDate: '',
        dueDate: '',
        
        init() {
            // Set default dates
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            this.scheduledDate = now.toISOString().slice(0, 16);
            
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            this.dueDate = tomorrow.toISOString().slice(0, 16);
        },
        
        openModal() {
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
        },
        
        closeModal() {
            this.showModal = false;
            document.body.classList.remove('overflow-hidden');
        }
    }
}
</script>
@endpush

@section('content')
    <div x-data="taskModal()">
        <!-- Breadcrumb Navigation -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('owner.staff.index') }}" class="hover:text-blue-600 transition-colors">Staff</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700 font-medium">{{ $staff->user->name }}</span>
            </nav>
        </div>

    <!-- Staff Header Card -->
    <div class="bg-gradient-to-br from-white/95 to-blue-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $staff->getRoleBadgeColor() }} flex items-center justify-center shadow-lg text-2xl sm:text-3xl font-bold">
                    {{ substr($staff->user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $staff->user->name }}</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $staff->job_title ?? ucfirst($staff->staff_role) }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $staff->getRoleBadgeColor() }}">
                            {{ ucfirst($staff->staff_role) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $staff->getStatusBadgeColor() }}">
                            {{ ucfirst(str_replace('_', ' ', $staff->status)) }}
                        </span>
                        @if($staff->department)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" 
                                style="background-color: {{ $staff->department->color }}20; color: {{ $staff->department->color }};">
                                <i class="{{ $staff->department->icon }} mr-1"></i> {{ $staff->department->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button @click="openModal()" 
                    class="inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 font-semibold transition-all shadow-lg text-center">
                    <i class="fas fa-tasks mr-2"></i> Assign Task
                </button>
                <a href="{{ route('owner.staff.edit', $staff) }}" 
                    class="inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 font-semibold transition-all shadow-lg text-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('owner.staff.index') }}" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-semibold transition-all shadow-sm text-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-6 mb-6">
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Total Tasks</div>
        </div>

        <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Completed</div>
        </div>

        <div class="bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Pending</div>
        </div>

        <div class="bg-gradient-to-br from-white to-purple-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-percentage text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-purple-600">{{ number_format($stats['completion_rate'], 0) }}%</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Completion</div>
        </div>

        <div class="bg-gradient-to-br from-white to-indigo-50 rounded-2xl shadow-lg p-4 sm:p-6 border border-white/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-indigo-600">{{ $stats['subordinates_count'] }}</div>
            <div class="text-xs sm:text-sm text-gray-600 mt-1">Team Members</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-white/95 to-gray-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-id-card text-blue-600 mr-2"></i>
                    Profile Information
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl {{ $staff->getRoleBadgeColor() }} flex items-center justify-center text-4xl sm:text-5xl font-bold shadow-lg">
                            {{ substr($staff->user->name, 0, 1) }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->user->email }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Mobile</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->user->mobile_number }}</p>
                        </div>

                        @if($staff->phone)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Phone</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->phone }}</p>
                            </div>
                        @endif

                        @if($staff->emergency_contact)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Emergency Contact</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->emergency_contact }}</p>
                            </div>
                        @endif

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Property</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->property->name }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Employment Type</p>
                            <p class="font-medium text-gray-900 text-sm">{{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}</p>
                        </div>

                        <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Join Date</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $staff->join_date->format('M d, Y') }}</p>
                        </div>

                        @if($staff->supervisor)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Reports To</p>
                                <p class="font-medium text-gray-900 text-sm">{{ $staff->supervisor->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($staff->supervisor->staff_role) }}</p>
                            </div>
                        @endif

                        @if($staff->notes)
                            <div class="bg-white/50 rounded-xl p-3 border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Notes</p>
                                <p class="text-sm text-gray-700">{{ $staff->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($staff->subordinates->count() > 0)
                <div class="bg-gradient-to-br from-white/95 to-indigo-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>
                        Team Members ({{ $staff->subordinates->count() }})
                    </h2>
                    <div class="space-y-3">
                        @foreach($staff->subordinates as $subordinate)
                            <div class="flex items-center bg-white/50 rounded-xl p-3 border border-white/30">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                    {{ substr($subordinate->user->name, 0, 1) }}
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $subordinate->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $subordinate->job_title ?? ucfirst($subordinate->staff_role) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Activity & Tasks -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Tasks -->
            <div class="bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                    Recent Tasks
                </h2>
                <div class="space-y-4">
                    @forelse($staff->assignedTasks as $task)
                        <div class="bg-white/70 rounded-xl p-4 border border-white/50 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $task->title }}</h3>
                                    @if($task->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                    @endif
                                    @if($task->location)
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $task->location }}
                                        </p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $task->getPriorityBadgeColor() }} flex-shrink-0">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $task->scheduled_at ? $task->scheduled_at->format('M d, h:i A') : 'Not scheduled' }}
                                </span>
                                <span class="px-3 py-1 rounded-full font-semibold {{ $task->getStatusBadgeColor() }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No tasks assigned yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Attendance History -->
            <div class="bg-gradient-to-br from-white/95 to-green-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                    Recent Attendance
                </h2>
                <div class="space-y-3">
                    @forelse($staff->attendance as $attendance)
                        <div class="flex items-center justify-between bg-white/70 rounded-xl p-4 border border-white/50">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $attendance->date->format('M d, Y (D)') }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : 'N/A' }}
                                    <i class="fas fa-sign-out-alt ml-2 mr-1"></i>
                                    {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $attendance->getStatusBadgeColor() }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                                @if($attendance->hours_worked)
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($attendance->hours_worked, 1) }}h</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No attendance records yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Leave Requests -->
            <div class="bg-gradient-to-br from-white/95 to-amber-50/90 backdrop-blur-xl rounded-2xl shadow-xl p-4 sm:p-6 border border-white/20">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-amber-600 mr-2"></i>
                    Recent Leave Requests
                </h2>
                <div class="space-y-3">
                    @forelse($staff->leaveRequests as $leave)
                        <div class="bg-white/70 rounded-xl p-4 border border-white/50">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-start gap-2">
                                    <i class="{{ $leave->getLeaveTypeIcon() }} text-amber-600 mt-1"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ ucfirst($leave->leave_type) }} Leave</p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                            <span class="text-gray-500">({{ $leave->total_days }} days)</span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($leave->reason, 80) }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $leave->getStatusBadgeColor() }} flex-shrink-0">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white/50 rounded-xl">
                            <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No leave requests yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
        <!-- Task Assignment Modal -->
        <div x-show="showModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4"
            style="display: none;">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
            
            <!-- Modal Container -->
            <div @click.stop 
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-2xl w-full max-h-[95vh] flex flex-col overflow-hidden"
                style="touch-action: pan-y;">
                    <!-- Header -->
                    <div class="flex-shrink-0 bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 sm:p-6 rounded-t-xl sm:rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 sm:space-x-3 flex-1 min-w-0">
                                <i class="fas fa-tasks text-xl sm:text-2xl flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <h3 class="text-lg sm:text-2xl font-bold truncate">Assign Task</h3>
                                    <p class="text-xs sm:text-sm text-purple-100 truncate">To: {{ $staff->user->name }}</p>
                                </div>
                            </div>
                            <button @click="closeModal()" class="text-white hover:text-gray-200 transition-colors ml-2 flex-shrink-0 w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-times text-xl sm:text-2xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('owner.tasks.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $staff->property_id }}">
                        <input type="hidden" name="assigned_to" value="{{ $staff->id }}">
                        <input type="hidden" name="department_id" value="{{ $staff->department_id }}">

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto overscroll-contain">
                        <div class="pt-8 px-4 sm:px-6 pb-32 space-y-4 sm:space-y-6">

                        <!-- Task Title -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" required
                                class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="e.g., Clean Room 101">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="3" required
                                class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Detailed instructions..."></textarea>
                        </div>

                        <!-- Task Type & Priority -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                    Task Type <span class="text-red-500">*</span>
                                </label>
                                <select name="task_type" required
                                    class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500">
                                    <option value="cleaning">🧹 Cleaning</option>
                                    <option value="maintenance">🔧 Maintenance</option>
                                    <option value="inspection">🔍 Inspection</option>
                                    <option value="delivery">📦 Delivery</option>
                                    <option value="customer_service">👥 Customer Service</option>
                                    <option value="administrative">📋 Administrative</option>
                                    <option value="other">➕ Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" required
                                    class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500">
                                    <option value="low">🟢 Low</option>
                                    <option value="medium" selected>🟡 Medium</option>
                                    <option value="high">🟠 High</option>
                                    <option value="urgent">🔴 Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">Location</label>
                            <input type="text" name="location"
                                class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500"
                                placeholder="e.g., Room 101, Lobby">
                        </div>

                        <!-- Schedule -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                    Start <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="scheduled_at" x-model="scheduledDate" required
                                    class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-1.5 sm:mb-2">
                                    Due <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="due_at" x-model="dueDate" required
                                    class="w-full border border-gray-300 rounded-lg sm:rounded-xl shadow-sm py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base text-gray-900 focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>

                        <!-- Photo Proof -->
                        <div class="bg-purple-50 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-purple-200">
                            <label class="flex items-start sm:items-center cursor-pointer">
                                <input type="checkbox" name="requires_photo_proof" value="1"
                                    class="w-5 h-5 mt-0.5 sm:mt-0 flex-shrink-0 text-purple-600 border-gray-300 rounded focus:ring-2 focus:ring-purple-500">
                                <span class="ml-3 text-xs sm:text-sm font-bold text-gray-700">
                                    <i class="fas fa-camera mr-1 sm:mr-2 text-purple-600"></i>
                                    Require photo proof
                                </span>
                            </label>
                            <p class="ml-8 mt-1 text-xs text-gray-600 hidden sm:block">Staff must upload photos when completing</p>
                        </div>
                        </div>
                        </div>

                        <!-- Actions - Sticky Footer -->
                        <div class="flex-shrink-0 flex items-center justify-end space-x-2 sm:space-x-3 px-4 sm:px-6 pt-4 sm:pt-6 pb-20 sm:pb-6 border-t border-gray-200 bg-white shadow-lg rounded-b-xl sm:rounded-b-2xl">
                            <button type="button" @click="closeModal()"
                                class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-200 text-gray-700 rounded-lg sm:rounded-xl hover:bg-gray-300 font-semibold transition-all text-sm sm:text-base">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg sm:rounded-xl hover:from-purple-700 hover:to-pink-700 font-semibold transition-all shadow-lg text-sm sm:text-base">
                                <i class="fas fa-check mr-1 sm:mr-2"></i>Assign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
