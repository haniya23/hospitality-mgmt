<x-page-header 
    title="B2B Partners" 
    subtitle="Manage your business partners" 
    icon="handshake">
    
    <div class="mb-4">
        <a href="{{ route('b2b.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            <span class="hidden sm:inline">New B2B Partner</span>
            <span class="sm:hidden">New Partner</span>
        </a>
    </div>

    <x-stat-cards :cards="[
        ['value' => 'activePartners', 'label' => 'Active Partners'],
        ['value' => 'totalBookings', 'label' => 'Total Bookings'],
        ['value' => 'totalPartners', 'label' => 'Total Partners']
    ]" />
</x-page-header>