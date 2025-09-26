<x-page-header 
    title="Properties" 
    subtitle="Manage your properties" 
    icon="building" 
    :add-route="route('properties.create')" 
    add-text="Add Property">
    
    <x-stat-cards :cards="[
        ['staticValue' => '1', 'label' => 'Total'],
        ['staticValue' => '1', 'label' => 'Active'],
        ['staticValue' => '5', 'label' => 'Total Rooms']
    ]" />
</x-page-header>