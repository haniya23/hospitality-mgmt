@extends('layouts.app')

@section('title', 'Staff Attendance')

@push('styles')
<style>
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .modern-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Header -->
    <header class="soft-header-gradient text-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-white bg-opacity-10"></div>
        <div class="relative px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                        <i class="fas fa-calendar-check text-teal-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Staff Attendance</h1>
                        <p class="text-sm text-slate-700">Monitor your staff attendance and working hours</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                        <i class="fas fa-arrow-left text-pink-500 mr-2"></i>
                        <span class="font-medium text-slate-800">Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="modern-card rounded-2xl p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('owner.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Property</label>
                    <select name="property_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Staff Member</label>
                    <select name="staff_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                                {{ $staffMember->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('owner.attendance.index') }}" class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors text-center">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Attendance Table -->
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr($record->staffAssignment->user->name, 0, 2) }}
                                        </span>
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
                                <button onclick="editAttendance({{ $record->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('owner.attendance.staff', $record->staffAssignment->uuid) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </a>
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
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">
                                        {{ substr($record->staffAssignment->user->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $record->staffAssignment->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->staffAssignment->user->mobile_number }}</div>
                                </div>
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
                                <span class="text-gray-500">Property:</span>
                                <div class="font-medium text-gray-900">{{ $record->staffAssignment->property->name }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <div class="font-medium text-gray-900">{{ $record->date->format('M d, Y') }}</div>
                            </div>
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
                            <div>
                                <span class="text-gray-500">Hours:</span>
                                <div class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($record->check_in_time)->diffInHours(\Carbon\Carbon::parse($record->check_out_time)) }}h
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                            <button onclick="editAttendance({{ $record->id }})" class="px-3 py-1 text-blue-600 hover:text-blue-900 text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <a href="{{ route('owner.attendance.staff', $record->staffAssignment->uuid) }}" class="px-3 py-1 text-green-600 hover:text-green-900 text-sm">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
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
                <p class="text-gray-500">No attendance records found for the selected filters.</p>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Check In</label>
                            <input type="time" name="check_in_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Check Out</label>
                            <input type="time" name="check_out_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editAttendance(attendanceId) {
    // Load attendance data and show modal
    fetch(`/owner/attendance/${attendanceId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('#editAttendanceForm [name="status"]').value = data.status;
            document.querySelector('#editAttendanceForm [name="check_in_time"]').value = data.check_in_time;
            document.querySelector('#editAttendanceForm [name="check_out_time"]').value = data.check_out_time;
            document.querySelector('#editAttendanceForm [name="notes"]').value = data.notes || '';
            
            document.getElementById('editAttendanceForm').action = `/owner/attendance/${attendanceId}`;
            document.getElementById('editAttendanceModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading attendance data');
        });
}

function closeEditModal() {
    document.getElementById('editAttendanceModal').classList.add('hidden');
}

document.getElementById('editAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const attendanceId = this.action.split('/').pop();
    
    fetch(`/owner/attendance/${attendanceId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
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
@endpush
@endsection
