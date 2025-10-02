<!-- Notification Center Component -->
<div x-data="{ 
    open: false, 
    notifications: [
        {
            id: 1,
            type: 'info',
            title: 'System Update',
            message: 'New features have been added to your dashboard.',
            time: '2 minutes ago',
            read: false
        },
        {
            id: 2,
            type: 'success',
            title: 'Booking Confirmed',
            message: 'Your recent booking has been successfully confirmed.',
            time: '1 hour ago',
            read: false
        },
        {
            id: 3,
            type: 'warning',
            title: 'Payment Due',
            message: 'Your subscription payment is due in 3 days.',
            time: '2 hours ago',
            read: true
        }
    ],
    get unreadCount() {
        return this.notifications.filter(n => !n.read).length;
    },
    markAsRead(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.read = true;
        }
    },
    markAllAsRead() {
        this.notifications.forEach(n => n.read = true);
    }
}" class="relative">
    <!-- Notification Bell -->
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
        <i class="fas fa-bell text-lg"></i>
        <!-- Notification badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 bg-red-500 text-white text-xs font-semibold rounded-full flex items-center justify-center px-1">
        </span>
    </button>
    
    <!-- Notification Dropdown -->
    <div x-show="open" 
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-60 max-h-96 overflow-hidden">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                Mark all as read
            </button>
        </div>
        
        <!-- Notifications List -->
        <div class="max-h-64 overflow-y-auto">
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id)" 
                     class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                     :class="{ 'bg-blue-50': !notification.read }">
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 rounded-full"
                                 :class="{
                                     'bg-blue-500': notification.type === 'info',
                                     'bg-green-500': notification.type === 'success',
                                     'bg-yellow-500': notification.type === 'warning',
                                     'bg-red-500': notification.type === 'error'
                                 }">
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                <span x-show="!notification.read" class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="notification.time"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Empty State -->
        <div x-show="notifications.length === 0" class="px-4 py-8 text-center">
            <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2"></i>
            <p class="text-sm text-gray-500">No notifications</p>
        </div>
        
        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="#" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>
