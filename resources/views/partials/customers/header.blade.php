<x-page-header 
    title="Customers" 
    subtitle="Manage your customers" 
    icon="users" 
    :add-route="route('customers.create')" 
    add-text="Add Customer">
    
    <x-stat-cards :cards="[
        ['value' => 'customers.length', 'label' => 'Total'],
        ['value' => 'customers.reduce((sum, c) => sum + (c.reservations_count || 0), 0)', 'label' => 'Total Bookings'],
        ['staticValue' => '0', 'label' => 'Active']
    ]" />
</x-page-header>