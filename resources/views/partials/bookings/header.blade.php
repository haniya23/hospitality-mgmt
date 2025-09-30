<x-page-header 
    title="Bookings" 
    subtitle="Manage your reservations" 
    icon="calendar-alt" 
    :add-route="route('bookings.create')" 
    add-text="New Booking">
    
    <template #actions>
        <button @click="openBulkDownloadModal()" 
                class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium text-sm hover:bg-purple-700 transition flex items-center space-x-2">
            <i class="fas fa-download"></i>
            <span>Bulk Download</span>
        </button>
    </template>
    
    <x-stat-cards :cards="[
        [
            'value' => 'bookingStats.all', 
            'label' => 'All',
            'icon' => 'fas fa-calendar-check',
            'bgGradient' => 'from-blue-50 to-indigo-50',
            'accentColor' => 'bg-blue-500',
            'clickable' => true,
            'action' => 'navigateToAllBookings()'
        ],
        [
            'value' => 'bookingStats.pending', 
            'label' => 'Pending',
            'icon' => 'fas fa-clock',
            'bgGradient' => 'from-yellow-50 to-amber-50',
            'accentColor' => 'bg-yellow-500',
            'clickable' => true,
            'action' => 'navigateToPendingBookings()'
        ],
        [
            'value' => 'bookingStats.confirmed', 
            'label' => 'Confirmed',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500',
            'clickable' => true,
            'action' => 'navigateToConfirmedBookings()'
        ],
        [
            'value' => 'bookingStats.cancelled', 
            'label' => 'Cancelled',
            'icon' => 'fas fa-times-circle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500',
            'clickable' => true,
            'action' => 'navigateToCancelledBookings()'
        ]
    ]" />
</x-page-header>