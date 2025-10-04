@extends('layouts.app')

@section('title', 'Staff Attendance Details')

@section('header')
<x-page-header 
    title="Staff Attendance Details" 
    subtitle="View detailed attendance and leave information for {{ $staff->name }}" 
    icon="user-clock">
    
    <!-- Back Button -->
    <div class="flex items-center space-x-3 mb-4">
        <a href="{{ route('owner.staff.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Staff
        </a>
    </div>
    
    <x-stat-cards :cards="[
        [
            'value' => $attendanceStats['present_days'], 
            'label' => 'Present Days',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500'
        ],
        [
            'value' => $attendanceStats['absent_days'], 
            'label' => 'Absent Days',
            'icon' => 'fas fa-times-circle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500'
        ],
        [
            'value' => $attendanceStats['total_hours'], 
            'label' => 'Total Hours',
            'icon' => 'fas fa-hourglass-half',
            'bgGradient' => 'from-blue-50 to-indigo-50',
            'accentColor' => 'bg-blue-500',
            'suffix' => ' hrs'
        ],
        [
            'value' => $attendanceStats['attendance_percentage'], 
            'label' => 'Attendance %',
            'icon' => 'fas fa-percentage',
            'bgGradient' => 'from-purple-50 to-violet-50',
            'accentColor' => 'bg-purple-500',
            'suffix' => '%'
        ]
    ]" />
</x-page-header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Staff Information -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center text-white font-bold text-xl">
                    {{ substr($staff->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $staff->name }}</h3>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 text-sm text-gray-600 mt-1">
                        <span class="truncate"><i class="fas fa-phone mr-1"></i>{{ $staff->mobile_number }}</span>
                        <span class="truncate"><i class="fas fa-envelope mr-1"></i>{{ $staff->email }}</span>
                        <span class="truncate"><i class="fas fa-building mr-1"></i>{{ $staff->getActiveStaffAssignments()->first()->property->name ?? 'N/A' }}</span>
                        <span class="truncate"><i class="fas fa-user-tag mr-1"></i>{{ $staff->getActiveStaffAssignments()->first()->role->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                <a href="{{ route('owner.staff.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Staff
                </a>
                <a href="{{ route('owner.staff.show', $staff->staffAssignments()->first()->uuid) }}" 
                   class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 text-center font-medium">
                    <i class="fas fa-eye mr-2"></i>View Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Attendance</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAttendance as $record)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->hours_worked ? $record->hours_worked . 'h' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : 
                                       ($record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($record->status === 'absent' ? 'bg-red-100 text-red-800' : 
                                       ($record->status === 'half_day' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->notes ? Str::limit($record->notes, 30) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No attendance records found</h3>
                                    <p class="text-sm text-gray-500">This staff member hasn't recorded any attendance yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Leave Requests -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Leave Requests</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="{{ $request->getLeaveTypeIcon() }} text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($request->leave_type) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-medium">{{ $request->total_days }}</span> days
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($request->status === 'pending')
                                        <button onclick="approveLeave({{ $request->id }})" 
                                                class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rejectLeave({{ $request->id }})" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button onclick="viewLeaveDetails({{ $request->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No leave requests found</h3>
                                    <p class="text-sm text-gray-500">This staff member hasn't submitted any leave requests yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Approve Leave Modal -->
<div id="approveLeaveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Approve Leave Request</h3>
            </div>
            <form id="approveLeaveForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea id="approve_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Add any notes for the staff member..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
                        Approve Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Leave Modal -->
<div id="rejectLeaveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Reject Leave Request</h3>
            </div>
            <form id="rejectLeaveForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea id="reject_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200">
                        Reject Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentLeaveId = null;

function approveLeave(leaveId) {
    currentLeaveId = leaveId;
    document.getElementById('approveLeaveModal').classList.remove('hidden');
}

function rejectLeave(leaveId) {
    currentLeaveId = leaveId;
    document.getElementById('rejectLeaveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveLeaveModal').classList.add('hidden');
    currentLeaveId = null;
}

function closeRejectModal() {
    document.getElementById('rejectLeaveModal').classList.add('hidden');
    currentLeaveId = null;
}

document.getElementById('approveLeaveForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        admin_notes: document.getElementById('approve_notes').value,
    };
    
    try {
        const response = await fetch(`/owner/leave-requests/${currentLeaveId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Leave request approved successfully!');
            window.location.reload();
        } else {
            alert('Failed to approve leave request: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while approving leave request.');
    }
});

document.getElementById('rejectLeaveForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        admin_notes: document.getElementById('reject_notes').value,
    };
    
    try {
        const response = await fetch(`/owner/leave-requests/${currentLeaveId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Leave request rejected successfully!');
            window.location.reload();
        } else {
            alert('Failed to reject leave request: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while rejecting leave request.');
    }
});
</script>
@endsection
