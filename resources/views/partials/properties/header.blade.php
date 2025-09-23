<x-page-header 
    title="Properties" 
    subtitle="Manage your properties" 
    icon="building" 
    :add-route="route('properties.create')" 
    add-text="Add Property">
    
    <div class="flex flex-wrap gap-2 mb-4">
        <button @click="activeFilter = 'all'" 
                :class="{ 'bg-white bg-opacity-30': activeFilter === 'all' }"
                class="px-4 py-2 rounded-full text-sm font-medium text-slate-800 bg-white bg-opacity-10 hover:bg-opacity-20 transition">
            All
        </button>
        <button @click="activeFilter = 'active'" 
                :class="{ 'bg-white bg-opacity-30': activeFilter === 'active' }"
                class="px-4 py-2 rounded-full text-sm font-medium text-slate-800 bg-white bg-opacity-10 hover:bg-opacity-20 transition">
            Active
        </button>
        <button @click="activeFilter = 'pending'" 
                :class="{ 'bg-white bg-opacity-30': activeFilter === 'pending' }"
                class="px-4 py-2 rounded-full text-sm font-medium text-slate-800 bg-white bg-opacity-10 hover:bg-opacity-20 transition">
            Pending
        </button>
    </div>

    <x-stat-cards :cards="[
        ['value' => 'stats.total', 'label' => 'Total'],
        ['value' => 'stats.active', 'label' => 'Active'],
        ['value' => 'stats.rooms', 'label' => 'Total Rooms']
    ]" />
</x-page-header>