@extends('layouts.staff')

@section('title', 'Staff Notifications')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="staffNotifications()">
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
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Notifications</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Stay updated with your assignments and tasks</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button @click="markAllAsRead()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-check-double mr-2"></i>Mark All Read
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="modern-card rounded-2xl p-4 sm:p-6">
        @if($notifications->count() > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div class="flex items-start p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors {{ !$notification->is_read ? 'border-l-4 border-blue-500' : '' }}">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-2 h-2 rounded-full mt-2" 
                                 style="background-color: {{ 
                                     $notification->priority === 'urgent' ? '#ef4444' : 
                                     ($notification->priority === 'high' ? '#f59e0b' : 
                                     ($notification->priority === 'medium' ? '#3b82f6' : '#10b981')) 
                                 }}"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm sm:text-base font-medium text-gray-900">{{ $notification->title }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                                              style="background-color: {{ 
                                                  $notification->priority === 'urgent' ? '#fef2f2' : 
                                                  ($notification->priority === 'high' ? '#fffbeb' : 
                                                  ($notification->priority === 'medium' ? '#eff6ff' : '#f0fdf4')) 
                                              }}; color: {{ 
                                                  $notification->priority === 'urgent' ? '#dc2626' : 
                                                  ($notification->priority === 'high' ? '#d97706' : 
                                                  ($notification->priority === 'medium' ? '#2563eb' : '#059669')) 
                                              }}">
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                        @if($notification->type)
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$notification->is_read)
                                    <button @click="markAsRead({{ $notification->id }})" 
                                            class="ml-3 p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-500">You're all caught up! Check back later for new updates.</p>
            </div>
        @endif
    </div>
</div>

<script>
function staffNotifications() {
    return {
        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/staff/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    // Remove the unread styling
                    const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notification) {
                        notification.classList.remove('border-l-4', 'border-blue-500');
                    }
                    this.showNotification('Notification marked as read', 'success');
                } else {
                    this.showNotification('Failed to mark notification as read', 'error');
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                this.showNotification('An error occurred', 'error');
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/staff/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    // Remove all unread styling
                    document.querySelectorAll('.border-l-4').forEach(el => {
                        el.classList.remove('border-l-4', 'border-blue-500');
                    });
                    this.showNotification('All notifications marked as read', 'success');
                } else {
                    this.showNotification('Failed to mark all notifications as read', 'error');
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
                this.showNotification('An error occurred', 'error');
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endsection
