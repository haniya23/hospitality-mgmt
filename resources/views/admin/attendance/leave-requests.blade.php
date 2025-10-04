@extends('layouts.app')

@section('title', 'Leave Requests Management')

@section('header')
<x-page-header 
    title="Leave Requests Management" 
    subtitle="Manage staff leave requests and approvals" 
    icon="calendar-times">
    
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
            'value' => $stats['pending_requests'], 
            'label' => 'Pending Requests',
            'icon' => 'fas fa-clock',
            'bgGradient' => 'from-yellow-50 to-amber-50',
            'accentColor' => 'bg-yellow-500'
        ],
        [
            'value' => $stats['approved_requests'], 
            'label' => 'Approved Requests',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500'
        ],
        [
            'value' => $stats['rejected_requests'], 
            'label' => 'Rejected Requests',
            'icon' => 'fas fa-times-circle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500'
        ],
        [
            'value' => $stats['total_days_approved'], 
            'label' => 'Days Approved',
            'icon' => 'fas fa-calendar-check',
            'bgGradient' => 'from-blue-50 to-indigo-50',
            'accentColor' => 'bg-blue-500',
            'suffix' => ' days'
        ]
    ]" />
</x-page-header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                <select name="property_id" id="property-filter" class="w-full">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                <select name="staff_id" id="staff-filter" class="w-full">
                    <option value="">All Staff</option>
                    @foreach($staff as $staffMember)
                        <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                            {{ $staffMember->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status-filter" class="w-full">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select name="leave_type" id="leave-type-filter" class="w-full">
                    <option value="">All Types</option>
                    <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                    <option value="personal" {{ request('leave_type') == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                    <option value="vacation" {{ request('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                    <option value="emergency" {{ request('leave_type') == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                    <option value="other" {{ request('leave_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Leave Requests -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Leave Requests</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
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
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($request->staffAssignment->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->staffAssignment->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->staffAssignment->user->mobile_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->staffAssignment->property->name }}
                            </td>
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
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No leave requests found</h3>
                                    <p class="text-sm text-gray-500">Try adjusting your filters or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $leaveRequests->links() }}
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

<!-- View Leave Details Modal -->
<div id="viewLeaveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Leave Request Details</h3>
            </div>
            <div id="leaveDetailsContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Select2 for filters
$(document).ready(function() {
    $('#property-filter').select2({
        placeholder: 'Select Property',
        allowClear: true,
        width: '100%'
    });
    
    $('#staff-filter').select2({
        placeholder: 'Select Staff Member',
        allowClear: true,
        width: '100%'
    });
    
    $('#status-filter').select2({
        placeholder: 'Select Status',
        allowClear: true,
        width: '100%'
    });
    
    $('#leave-type-filter').select2({
        placeholder: 'Select Leave Type',
        allowClear: true,
        width: '100%'
    });
});

let currentLeaveId = null;

function approveLeave(leaveId) {
    currentLeaveId = leaveId;
    document.getElementById('approveLeaveModal').classList.remove('hidden');
}

function rejectLeave(leaveId) {
    currentLeaveId = leaveId;
    document.getElementById('rejectLeaveModal').classList.remove('hidden');
}

function viewLeaveDetails(leaveId) {
    currentLeaveId = leaveId;
    // Load leave details via AJAX
    loadLeaveDetails(leaveId);
    document.getElementById('viewLeaveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveLeaveModal').classList.add('hidden');
    currentLeaveId = null;
}

function closeRejectModal() {
    document.getElementById('rejectLeaveModal').classList.add('hidden');
    currentLeaveId = null;
}

function closeViewModal() {
    document.getElementById('viewLeaveModal').classList.add('hidden');
    currentLeaveId = null;
}

async function loadLeaveDetails(leaveId) {
    try {
        const response = await fetch(`/owner/leave-requests/${leaveId}/details`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('leaveDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Staff Member</label>
                            <p class="text-sm text-gray-900">${data.leave.staff_name}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Property</label>
                            <p class="text-sm text-gray-900">${data.leave.property_name}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Leave Type</label>
                            <p class="text-sm text-gray-900">${data.leave.leave_type}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Days</label>
                            <p class="text-sm text-gray-900">${data.leave.total_days}</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Duration</label>
                        <p class="text-sm text-gray-900">${data.leave.start_date} - ${data.leave.end_date}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Reason</label>
                        <p class="text-sm text-gray-900">${data.leave.reason || 'No reason provided'}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${data.leave.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : (data.leave.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">${data.leave.status}</span>
                    </div>
                    ${data.leave.admin_notes ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Admin Notes</label>
                        <p class="text-sm text-gray-900">${data.leave.admin_notes}</p>
                    </div>
                    ` : ''}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading leave details:', error);
    }
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