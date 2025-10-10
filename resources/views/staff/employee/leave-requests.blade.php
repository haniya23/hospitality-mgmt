@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Leave Requests</h1>
            <p class="mt-2 text-gray-600">Manage your leave applications</p>
        </div>
        <button onclick="toggleLeaveForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Request Leave
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Approved</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Rejected</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $stats['rejected'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Days Approved</div>
            <div class="text-2xl font-bold text-purple-600 mt-2">{{ $stats['total_days_approved'] }}</div>
        </div>
    </div>

    {{-- Leave Request Form --}}
    <div id="leaveForm" class="hidden mb-8">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-blue-50 border-b">
                <h2 class="text-lg font-semibold text-blue-900">Submit Leave Request</h2>
            </div>
            <form action="{{ route('staff.leave-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type *</label>
                        <select name="leave_type" required class="w-full border-gray-300 rounded-lg">
                            <option value="">Select Type</option>
                            <option value="sick">Sick Leave</option>
                            <option value="vacation">Vacation</option>
                            <option value="personal">Personal</option>
                            <option value="emergency">Emergency</option>
                            <option value="maternity">Maternity</option>
                            <option value="paternity">Paternity</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" name="start_date" required min="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" name="end_date" required min="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments (Optional)</label>
                        <input type="file" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                            class="w-full border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Medical certificate, documents, etc.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                        <textarea name="reason" rows="3" required
                            placeholder="Please provide reason for leave..."
                            class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t pt-6">
                    <button type="button" onclick="toggleLeaveForm()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Leave Requests List --}}
    <div class="space-y-4">
        @forelse($leaveRequests as $leave)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="{{ $leave->getLeaveTypeIcon() }} text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">{{ ucfirst($leave->leave_type) }} Leave</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                    <span class="text-gray-500">({{ $leave->total_days }} working days)</span>
                                </p>
                                <p class="text-sm text-gray-700 mt-2">{{ $leave->reason }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $leave->getStatusBadgeColor() }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>

                    @if($leave->review_notes)
                        <div class="mt-4 p-3 {{ $leave->status === 'approved' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-lg">
                            <p class="text-sm font-semibold {{ $leave->status === 'approved' ? 'text-green-800' : 'text-red-800' }}">
                                Review Notes:
                            </p>
                            <p class="text-sm {{ $leave->status === 'approved' ? 'text-green-700' : 'text-red-700' }} mt-1">
                                {{ $leave->review_notes }}
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-4 pt-4 border-t text-sm text-gray-500">
                        <span>Submitted {{ $leave->created_at->diffForHumans() }}</span>
                        @if($leave->reviewed_at)
                            <span>Reviewed {{ $leave->reviewed_at->diffForHumans() }} by {{ $leave->reviewer?->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-calendar-alt text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No leave requests yet</p>
                <button onclick="toggleLeaveForm()" class="text-blue-600 hover:text-blue-800 mt-2">
                    Submit your first leave request →
                </button>
            </div>
        @endforelse
    </div>
</div>

<script>
function toggleLeaveForm() {
    const form = document.getElementById('leaveForm');
    form.classList.toggle('hidden');
}
</script>
@endsection

