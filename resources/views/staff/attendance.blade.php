@extends('layouts.staff')

@section('title', 'Staff Attendance')

@push('styles')
<style>
    .notification-enter {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-out;
    }
    
    .notification-enter-active {
        transform: translateX(0);
        opacity: 1;
    }
    
    .notification-exit {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease-in;
    }
    
    .notification-exit-active {
        transform: translateX(100%);
        opacity: 0;
    }
    
    @media (max-width: 640px) {
        .notification-enter {
            transform: translateY(-100%);
        }
        
        .notification-exit-active {
            transform: translateY(-100%);
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-6" 
     x-data="{
         // Modal states
         showLeaveRequestModal: false,
         showCheckoutModal: false,
         
         // Loading states
         isSubmitting: false,
         isSubmittingCheckout: false,
         
         // Data
         todaysCheckInTime: @if($todaysAttendance && $todaysAttendance->check_in_time)'{{ \Carbon\Carbon::parse($todaysAttendance->check_in_time)->format('H:i') }}'@else''@endif,
         
         // Forms
         leaveForm: {
             leave_type: '',
             start_date: '',
             end_date: '',
             reason: '',
             attachments: []
         },
         checkoutForm: {
             notes: ''
         },
         
         // Methods
         async checkIn() {
             try {
                 let locationData = null;
                 if (navigator.geolocation) {
                     navigator.geolocation.getCurrentPosition((position) => {
                         locationData = {
                             latitude: position.coords.latitude,
                             longitude: position.coords.longitude,
                             accuracy: position.coords.accuracy
                         };
                         this.performCheckIn(locationData);
                     }, () => {
                         this.performCheckIn(null);
                     });
                 } else {
                     this.performCheckIn(null);
                 }
             } catch (error) {
                 this.showNotification('Failed to get location. Check-in without location.', 'warning');
                 this.performCheckIn(null);
             }
         },
         
         async performCheckIn(locationData) {
             try {
                 const response = await fetch('/staff/attendance/check-in', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                         'Accept': 'application/json',
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify(locationData)
                 });
                 
                 const result = await response.json();
                 
                 if (result.success) {
                     this.showNotification('Check-in successful!', 'success');
                     setTimeout(() => {
                         window.location.reload();
                     }, 1000);
                 } else {
                     this.showNotification(result.message, 'error');
                 }
             } catch (error) {
                 this.showNotification('Failed to check in. Please try again.', 'error');
             }
         },
         
         checkOut() {
             @if($todaysAttendance && $todaysAttendance->check_in_time)
                 this.todaysCheckInTime = '{{ \Carbon\Carbon::parse($todaysAttendance->check_in_time)->format('H:i') }}';
             @else
                 this.todaysCheckInTime = new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
             @endif
             this.showCheckoutModal = true;
         },
         
         async submitCheckout() {
             this.isSubmittingCheckout = true;
             
             try {
                 const response = await fetch('/staff/attendance/check-out', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                         'Accept': 'application/json',
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({ notes: this.checkoutForm.notes })
                 });
                 
                 const result = await response.json();
                 
                 if (result.success) {
                     this.showNotification('Check-out successful!', 'success');
                     this.showCheckoutModal = false;
                     this.checkoutForm.notes = '';
                     setTimeout(() => {
                         window.location.reload();
                     }, 1000);
                 } else {
                     this.showNotification(result.message, 'error');
                 }
             } catch (error) {
                 this.showNotification('Failed to check out. Please try again.', 'error');
             } finally {
                 this.isSubmittingCheckout = false;
             }
         },
         
         async submitLeaveRequest() {
             this.isSubmitting = true;
             
             try {
                 const formData = new FormData();
                 formData.append('leave_type', this.leaveForm.leave_type);
                 formData.append('start_date', this.leaveForm.start_date);
                 formData.append('end_date', this.leaveForm.end_date);
                 formData.append('reason', this.leaveForm.reason);
                 
                 // Add attachments
                 this.leaveForm.attachments.forEach((file, index) => {
                     formData.append(`attachments[${index}]`, file);
                 });
                 
                 const response = await fetch('/staff/leave-requests', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                         'Accept': 'application/json',
                     },
                     body: formData
                 });
                 
                 const result = await response.json();
                 
                 if (result.success) {
                     this.showNotification('Leave request submitted successfully!', 'success');
                     this.showLeaveRequestModal = false;
                     this.resetLeaveForm();
                     setTimeout(() => {
                         window.location.reload();
                     }, 1000);
                 } else {
                     this.showNotification(result.message, 'error');
                 }
             } catch (error) {
                 this.showNotification('Failed to submit leave request. Please try again.', 'error');
             } finally {
                 this.isSubmitting = false;
             }
         },
         
         async cancelLeaveRequest(leaveRequestId) {
             if (!confirm('Are you sure you want to cancel this leave request?')) {
                 return;
             }
             
             try {
                 const response = await fetch(`/staff/leave-requests/${leaveRequestId}/cancel`, {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                         'Accept': 'application/json',
                     },
                 });
                 
                 const result = await response.json();
                 
                 if (result.success) {
                     this.showNotification('Leave request cancelled successfully!', 'success');
                     setTimeout(() => {
                         window.location.reload();
                     }, 1000);
                 } else {
                     this.showNotification(result.message, 'error');
                 }
             } catch (error) {
                 this.showNotification('Failed to cancel leave request. Please try again.', 'error');
             }
         },
         
         handleFileUpload(event) {
             this.leaveForm.attachments = Array.from(event.target.files);
         },
         
         resetLeaveForm() {
             this.leaveForm = {
                 leave_type: '',
                 start_date: '',
                 end_date: '',
                 reason: '',
                 attachments: []
             };
         },
         
         loadAttendanceHistory() {
             window.location.href = '/staff/attendance/history';
         },
         
         showNotification(message, type = 'info') {
             const notification = document.createElement('div');
             notification.className = `fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-sm z-50 p-4 rounded-xl shadow-xl backdrop-blur-sm border notification-enter ${
                 type === 'success' ? 'bg-green-500/95 text-white border-green-400/50' :
                 type === 'error' ? 'bg-red-500/95 text-white border-red-400/50' :
                 type === 'warning' ? 'bg-yellow-500/95 text-white border-yellow-400/50' :
                 'bg-blue-500/95 text-white border-blue-400/50'
             }`;
             
             const icon = type === 'success' ? 'fa-check-circle' :
                         type === 'error' ? 'fa-exclamation-circle' :
                         type === 'warning' ? 'fa-exclamation-triangle' :
                         'fa-info-circle';
             
             notification.innerHTML = `
                 <div class=\"flex items-center space-x-3\">
                     <i class=\"fas ${icon} text-lg flex-shrink-0\"></i>
                     <span class=\"text-sm font-medium\">${message}</span>
                 </div>
             `;
             
             document.body.appendChild(notification);
             
             setTimeout(() => {
                 notification.classList.remove('notification-enter');
                 notification.classList.add('notification-enter-active');
             }, 10);
             
             setTimeout(() => {
                 notification.classList.remove('notification-enter-active');
                 notification.classList.add('notification-exit-active');
                 setTimeout(() => {
                     notification.remove();
                 }, 300);
             }, 3000);
         }
     }">
    
    <!-- Back Button -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('staff.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    
    <!-- Header -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Attendance & Leave</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Track your daily attendance and manage leave requests</p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-sm text-gray-500">Today</div>
                <div class="text-lg sm:text-xl font-semibold text-gray-900">{{ now()->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Status -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Today's Status</h3>
        
        @if($todaysAttendance)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4 {{ $todaysAttendance->status === 'present' ? 'bg-green-100' : ($todaysAttendance->status === 'late' ? 'bg-yellow-100' : 'bg-red-100') }}">
                        <i class="fas {{ $todaysAttendance->getStatusIcon() }} text-lg {{ $todaysAttendance->status === 'present' ? 'text-green-600' : ($todaysAttendance->status === 'late' ? 'text-yellow-600' : 'text-red-600') }}"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ ucfirst($todaysAttendance->status) }}</h4>
                        <p class="text-sm text-gray-600">
                            @if($todaysAttendance->check_in_time)
                                Check-in: {{ \Carbon\Carbon::parse($todaysAttendance->check_in_time)->format('H:i') }}
                            @endif
                            @if($todaysAttendance->check_out_time)
                                | Check-out: {{ \Carbon\Carbon::parse($todaysAttendance->check_out_time)->format('H:i') }}
                            @endif
                        </p>
                        @if($todaysAttendance->hours_worked > 0)
                            <p class="text-sm text-blue-600 font-medium">Hours worked: {{ $todaysAttendance->hours_worked }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    @if(!$todaysAttendance->check_in_time)
                        <button @click="checkIn()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>Check In
                        </button>
                    @elseif(!$todaysAttendance->check_out_time)
                        <button @click="checkOut()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                        </button>
                    @else
                        <span class="text-sm text-gray-500">Day Complete</span>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-gray-400 text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">No attendance recorded</h4>
                <p class="text-gray-600 mb-4">Check in to start tracking your attendance</p>
                <button @click="checkIn()" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Check In Now
                </button>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Present Days</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $attendanceStats['present_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-red-100">
                    <i class="fas fa-times-circle text-red-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Absent Days</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $attendanceStats['absent_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-blue-100">
                    <i class="fas fa-clock text-blue-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Hours</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $attendanceStats['total_hours'] }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card rounded-xl sm:rounded-2xl p-3 sm:p-6">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg bg-purple-100">
                    <i class="fas fa-percentage text-purple-600 text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Attendance %</p>
                    <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $attendanceStats['attendance_percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Request Button -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900">Leave Management</h3>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Request time off and track your leave balance</p>
            </div>
            <button @click="showLeaveRequestModal = true" 
                    class="mt-4 sm:mt-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Request Leave
            </button>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    @if($pendingLeaveRequests->count() > 0)
        <div class="modern-card rounded-2xl p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Pending Requests</h3>
            <div class="space-y-3">
                @foreach($pendingLeaveRequests as $request)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                <i class="fas {{ $request->getLeaveTypeIcon() }} text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">{{ ucfirst($request->leave_type) }} Leave</h4>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                    ({{ $request->total_days }} days)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full font-medium">Pending</span>
                            @if($request->canBeCancelled())
                                <button @click="cancelLeaveRequest({{ $request->id }})" 
                                        class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upcoming Approved Leave -->
    @if($upcomingLeave->count() > 0)
        <div class="modern-card rounded-2xl p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Upcoming Leave</h3>
            <div class="space-y-3">
                @foreach($upcomingLeave as $request)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-green-50 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas {{ $request->getLeaveTypeIcon() }} text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">{{ ucfirst($request->leave_type) }} Leave</h4>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                    ({{ $request->total_days }} days)
                                </p>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full font-medium">Approved</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Attendance -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Recent Attendance</h3>
            <button @click="loadAttendanceHistory()" 
                    class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</button>
        </div>
        
        @if($recentAttendance->count() > 0)
            <div class="space-y-3">
                @foreach($recentAttendance as $attendance)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $attendance->status === 'present' ? 'bg-green-100' : ($attendance->status === 'late' ? 'bg-yellow-100' : 'bg-red-100') }}">
                                <i class="fas {{ $attendance->getStatusIcon() }} text-sm {{ $attendance->status === 'present' ? 'text-green-600' : ($attendance->status === 'late' ? 'text-yellow-600' : 'text-red-600') }}"></i>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">{{ $attendance->date->format('M d, Y') }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    @if($attendance->check_in_time)
                                        {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                    @endif
                                    @if($attendance->check_out_time)
                                        - {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                    @endif
                                    @if($attendance->hours_worked > 0)
                                        ({{ $attendance->hours_worked }}h)
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                              style="background-color: {{ $attendance->getStatusColor() === 'green' ? '#dcfce7' : ($attendance->getStatusColor() === 'yellow' ? '#fef3c7' : '#fee2e2') }}; color: {{ $attendance->getStatusColor() === 'green' ? '#166534' : ($attendance->getStatusColor() === 'yellow' ? '#92400e' : '#991b1b') }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar-check text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No attendance records yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Leave Request Modal -->
<div x-show="showLeaveRequestModal" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     @click.self="showLeaveRequestModal = false">
    
    <div class="bg-white rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Request Leave</h3>
            <button @click="showLeaveRequestModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form @submit.prevent="submitLeaveRequest()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                    <select x-model="leaveForm.leave_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select leave type</option>
                        <option value="sick">Sick Leave</option>
                        <option value="personal">Personal Leave</option>
                        <option value="vacation">Vacation</option>
                        <option value="emergency">Emergency</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" x-model="leaveForm.start_date" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" x-model="leaveForm.end_date" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea x-model="leaveForm.reason" required rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Please provide a reason for your leave request..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attachments (Optional)</label>
                    <input type="file" multiple accept=".pdf,.jpg,.jpeg,.png" 
                           @change="handleFileUpload($event)"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG files only (max 2MB each)</p>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" @click="showLeaveRequestModal = false" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="isSubmitting"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <span x-show="!isSubmitting">Submit Request</span>
                    <span x-show="isSubmitting">Submitting...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Checkout Modal -->
<div x-show="showCheckoutModal" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     @click.self="showCheckoutModal = false">
    
    <div class="bg-white rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Check Out</h3>
            </div>
            <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form @submit.prevent="submitCheckout()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea x-model="checkoutForm.notes" rows="4" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Add any notes for today's work..."></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Today's Summary</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="flex justify-between">
                            <span>Check-in time:</span>
                            <span x-text="todaysCheckInTime" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Check-out time:</span>
                            <span x-text="new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})" class="font-medium text-green-600"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" @click="showCheckoutModal = false" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </button>
                <button type="submit" :disabled="isSubmittingCheckout"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 font-medium">
                    <span x-show="!isSubmittingCheckout">Check Out</span>
                    <span x-show="isSubmittingCheckout" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

