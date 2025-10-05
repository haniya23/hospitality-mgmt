@extends('layouts.app')

@section('title', 'Staff Attendance Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-xl font-bold text-slate-900">Staff Attendance Details</h1>
                    <p class="text-sm text-slate-700">Detailed attendance records for {{ $staffAssignment->user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('owner.attendance.index') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-arrow-left text-pink-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Back to Attendance</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Staff Info Card -->
        <div class="modern-card rounded-2xl p-6 mb-6">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">
                        {{ substr($staffAssignment->user->name, 0, 2) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $staffAssignment->user->name }}</h2>
                    <p class="text-gray-600">{{ $staffAssignment->user->mobile_number }}</p>
                    <p class="text-sm text-gray-500">{{ $staffAssignment->property->name }} • {{ $staffAssignment->role->name }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="modern-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-bold text-gray-900">Attendance Records</h3>
                <p class="text-sm text-gray-600">Showing {{ $attendance->count() }} of {{ $attendance->total() }} records</p>
            </div>
            
            @if($attendance->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendance as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $record->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'partial' => 'bg-blue-100 text-blue-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($record->check_in_time && $record->check_out_time)
                                    {{ \Carbon\Carbon::parse($record->check_in_time)->diffInHours(\Carbon\Carbon::parse($record->check_out_time)) }}h
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editAttendance({{ $record->id }})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="lg:hidden">
                <div class="space-y-4 p-4">
                    @foreach($attendance as $record)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="text-lg font-medium text-gray-900">{{ $record->date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $record->date->format('l') }}</div>
                            </div>
                            @php
                                $statusColors = [
                                    'present' => 'bg-green-100 text-green-800',
                                    'absent' => 'bg-red-100 text-red-800',
                                    'late' => 'bg-yellow-100 text-yellow-800',
                                    'partial' => 'bg-blue-100 text-blue-800'
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Check In:</span>
                                <div class="font-medium text-gray-900">
                                    {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">Check Out:</span>
                                <div class="font-medium text-gray-900">
                                    {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}
                                </div>
                            </div>
                            @if($record->check_in_time && $record->check_out_time)
                            <div class="col-span-2">
                                <span class="text-gray-500">Hours Worked:</span>
                                <div class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($record->check_in_time)->diffInHours(\Carbon\Carbon::parse($record->check_out_time)) }} hours
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex justify-end mt-4 pt-3 border-t border-gray-100">
                            <button onclick="editAttendance({{ $record->id }})" class="px-3 py-1 text-blue-600 hover:text-blue-900 text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendance->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Attendance Records</h3>
                <p class="text-gray-500">No attendance records found for this staff member.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div id="editAttendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Attendance</h3>
            </div>
            <form id="editAttendanceForm" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check In Time</label>
                        <input type="time" id="check_in_time" name="check_in_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check Out Time</label>
                        <input type="time" id="check_out_time" name="check_out_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAttendance(attendanceId) {
    // Fetch attendance data and populate modal
    fetch(`/owner/attendance/${attendanceId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('status').value = data.status;
            document.getElementById('check_in_time').value = data.check_in_time;
            document.getElementById('check_out_time').value = data.check_out_time;
            document.getElementById('editAttendanceForm').action = `/owner/attendance/${attendanceId}`;
            document.getElementById('editAttendanceModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading attendance data');
        });
}

function closeModal() {
    document.getElementById('editAttendanceModal').classList.add('hidden');
}

document.getElementById('editAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating attendance');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating attendance');
    });
});
</script>
@endsection
