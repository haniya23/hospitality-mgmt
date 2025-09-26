<x-page-header 
    title="Bookings" 
    subtitle="Manage your reservations" 
    icon="calendar-alt" 
    :add-route="route('bookings.create')" 
    add-text="New Booking">
    
    <x-stat-cards :cards="[
        ['value' => 'bookingStats.all', 'label' => 'All'],
        ['value' => 'bookingStats.pending', 'label' => 'Pending'],
        ['value' => 'bookingStats.confirmed', 'label' => 'Confirmed'],
        ['value' => 'bookingStats.cancelled', 'label' => 'Cancelled']
    ]" />
</x-page-header>