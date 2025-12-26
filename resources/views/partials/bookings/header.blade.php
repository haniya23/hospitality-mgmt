<x-page-header 
    title="Bookings" 
    subtitle="Manage your reservations" 
    icon="calendar-alt" 
    :add-route="route('bookings.create')" 
    add-text="New Booking">
    
    
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
            'value' => 'bookingStats.completed', 
            'label' => 'Completed',
            'icon' => 'fas fa-check-double',
            'bgGradient' => 'from-teal-50 to-cyan-50',
            'accentColor' => 'bg-teal-500',
            'clickable' => true,
            'action' => 'navigateToCompletedBookings()'
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