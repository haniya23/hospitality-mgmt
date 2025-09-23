<x-page-header 
    title="B2B Partners" 
    subtitle="Manage your business partners" 
    icon="handshake">
    
    <div class="mb-4">
        <button @click="openCreateModal()" class="glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
            <i class="fas fa-plus text-pink-500 mr-2"></i>
            <span class="font-medium text-slate-800 hidden sm:inline">New B2B Partner</span>
            <span class="font-medium text-slate-800 sm:hidden">New Partner</span>
        </button>
    </div>

    <x-stat-cards :cards="[
        ['value' => 'filteredPartners.filter(p => p.status === \'active\').length', 'label' => 'Active Partners'],
        ['value' => 'filteredPartners.reduce((sum, p) => sum + p.reservations_count, 0)', 'label' => 'Total Bookings'],
        ['value' => 'filteredPartners.length', 'label' => 'Total Partners']
    ]" />
</x-page-header>