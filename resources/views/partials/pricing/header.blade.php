<x-page-header 
    title="Pricing Management" 
    subtitle="Manage pricing rules" 
    icon="tags">
    
    <x-stat-cards :cards="[
        ['value' => 'rules.filter(r => r.status === \'active\').length', 'label' => 'Active Rules'],
        ['value' => 'rules.length', 'label' => 'Total Rules']
    ]" />
</x-page-header>