<x-page-header 
    title="Bookings" 
    subtitle="Manage your reservations" 
    icon="calendar-alt" 
    :add-route="route('bookings.create')" 
    add-text="New Booking">
    
    <x-stat-cards :cards="[
        ['staticValue' => '0', 'label' => 'All'],
        ['staticValue' => '0', 'label' => 'Pending'],
        ['staticValue' => '0', 'label' => 'Confirmed'],
        ['staticValue' => '0', 'label' => 'Cancelled']
    ]" />
</x-page-header>