<x-page-header 
    title="Reports & Analytics" 
    subtitle="Business insights" 
    icon="chart-bar">
    
    <x-stat-cards :cards="[
        ['value' => '\'₹\' + (metrics.revenue/1000).toFixed(0) + \'K\'', 'label' => 'Revenue'],
        ['value' => 'metrics.occupancy + \'%\'', 'label' => 'Occupancy']
    ]" />
</x-page-header>