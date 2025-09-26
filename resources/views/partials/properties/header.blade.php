<x-page-header 
    title="Properties" 
    subtitle="Manage your properties" 
    icon="building" 
    :add-route="route('properties.create')" 
    add-text="Add Property">
    
    <div x-data="propertyStats()" x-init="init()">
        <x-stat-cards :cards="[
            [
                'value' => 'stats.total', 
                'label' => 'Total Properties',
                'icon' => 'fas fa-building',
                'bgGradient' => 'from-blue-50 to-indigo-50',
                'accentColor' => 'bg-blue-500'
            ],
            [
                'value' => 'stats.active', 
                'label' => 'Active Properties',
                'icon' => 'fas fa-check-circle',
                'bgGradient' => 'from-green-50 to-emerald-50',
                'accentColor' => 'bg-green-500'
            ],
            [
                'value' => 'stats.accommodations', 
                'label' => 'Total Accommodations',
                'icon' => 'fas fa-bed',
                'bgGradient' => 'from-purple-50 to-violet-50',
                'accentColor' => 'bg-purple-500'
            ],
            [
                'value' => 'stats.bookings', 
                'label' => 'Total Bookings',
                'icon' => 'fas fa-calendar-check',
                'bgGradient' => 'from-orange-50 to-amber-50',
                'accentColor' => 'bg-orange-500'
            ]
        ]" />
    </div>
</x-page-header>