<x-page-header 
    title="Customers" 
    subtitle="Manage your customers" 
    icon="users" 
    :add-route="route('customers.create')" 
    add-text="Add Customer">
    
    <x-stat-cards :cards="[
        ['staticValue' => '0', 'label' => 'Total'],
        ['staticValue' => '0', 'label' => 'Total Bookings'],
        ['staticValue' => '0', 'label' => 'Active']
    ]" />
</x-page-header>