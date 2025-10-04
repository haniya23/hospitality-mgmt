@extends('layouts.app')

@section('title', 'Attendance Management')

@section('header')
<x-page-header 
    title="Attendance Management" 
    subtitle="Track staff attendance and working hours" 
    icon="calendar-check">
    
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
            'value' => $stats['present_days'], 
            'label' => 'Present Days',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500'
        ],
        [
            'value' => $stats['absent_days'], 
            'label' => 'Absent Days',
            'icon' => 'fas fa-times-circle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500'
        ],
        [
            'value' => $stats['late_days'], 
            'label' => 'Late Days',
            'icon' => 'fas fa-clock',
            'bgGradient' => 'from-yellow-50 to-amber-50',
            'accentColor' => 'bg-yellow-500'
        ],
        [
            'value' => $stats['total_hours'], 
            'label' => 'Total Hours',
            'icon' => 'fas fa-hourglass-half',
            'bgGradient' => 'from-blue-50 to-indigo-50',
            'accentColor' => 'bg-blue-500',
            'suffix' => ' hrs'
        ]
    ]" />
</x-page-header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Leave</option>
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

    <!-- Attendance Records -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendance as $record)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($record->staffAssignment->user->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->staffAssignment->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->staffAssignment->user->mobile_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->staffAssignment->property->name }}
                            </td>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editAttendance({{ $record->id }})" 
                                        class="text-green-600 hover:text-green-900 transition-colors duration-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No attendance records found</h3>
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
            {{ $attendance->links() }}
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div id="editAttendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Attendance</h3>
            </div>
            <form id="editAttendanceForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check In Time</label>
                        <input type="time" id="check_in_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check Out Time</label>
                        <input type="time" id="check_out_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
                        Update
                    </button>
                </div>
            </form>
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
});

let currentAttendanceId = null;

function editAttendance(attendanceId) {
    currentAttendanceId = attendanceId;
    document.getElementById('editAttendanceModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editAttendanceModal').classList.add('hidden');
    currentAttendanceId = null;
}

document.getElementById('editAttendanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        check_in_time: document.getElementById('check_in_time').value,
        check_out_time: document.getElementById('check_out_time').value,
        status: document.getElementById('status').value,
        notes: document.getElementById('notes').value,
    };
    
    try {
        const response = await fetch(`/owner/attendance/${currentAttendanceId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Attendance updated successfully!');
            window.location.reload();
        } else {
            alert('Failed to update attendance: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred while updating attendance.');
    }
});
</script>
@endsection
