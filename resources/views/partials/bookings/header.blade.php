<x-page-header 
    title="Bookings" 
    subtitle="Manage your reservations" 
    icon="calendar-alt" 
    :add-route="route('bookings.create')" 
    add-text="New Booking">
    
    <x-stat-cards :cards="[
        ['value' => 'bookings.length', 'label' => 'All', 'clickable' => true, 'action' => 'statusFilter = \'\''],
        ['value' => 'bookings.filter(b => b.status === \'pending\').length', 'label' => 'Pending', 'clickable' => true, 'action' => 'statusFilter = \'pending\''],
        ['value' => 'bookings.filter(b => b.status === \'confirmed\').length', 'label' => 'Confirmed', 'clickable' => true, 'action' => 'statusFilter = \'confirmed\''],
        ['value' => 'bookings.filter(b => b.status === \'cancelled\').length', 'label' => 'Cancelled', 'clickable' => true, 'action' => 'statusFilter = \'cancelled\'']
    ]" />
</x-page-header>