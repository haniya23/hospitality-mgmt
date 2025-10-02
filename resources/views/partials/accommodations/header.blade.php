<x-page-header 
    title="Accommodations" 
    subtitle="Manage your rooms and accommodations" 
    icon="bed" 
    :add-route="route('accommodations.create')" 
    add-text="Add Accommodation">
    
    <div x-data="accommodationStats()" x-init="init()">
        <x-stat-cards :cards="[
            [
                'value' => 'stats.total', 
                'label' => 'Total Accommodations',
                'icon' => 'fas fa-bed',
                'bgGradient' => 'from-blue-50 to-indigo-50',
                'accentColor' => 'bg-blue-500',
                'clickable' => true,
                'action' => 'navigateToAllAccommodations()'
            ],
            [
                'value' => 'stats.active', 
                'label' => 'Active Properties',
                'icon' => 'fas fa-check-circle',
                'bgGradient' => 'from-green-50 to-emerald-50',
                'accentColor' => 'bg-green-500',
                'clickable' => true,
                'action' => 'navigateToActiveAccommodations()'
            ],
            [
                'value' => 'stats.totalPrice', 
                'label' => 'Total Value',
                'icon' => 'fas fa-rupee-sign',
                'bgGradient' => 'from-purple-50 to-violet-50',
                'accentColor' => 'bg-purple-500',
                'clickable' => true,
                'action' => 'navigateToProperties()',
                'prefix' => '₹',
                'suffix' => ''
            ],
            [
                'value' => 'stats.avgPrice', 
                'label' => 'Average Price',
                'icon' => 'fas fa-chart-line',
                'bgGradient' => 'from-orange-50 to-amber-50',
                'accentColor' => 'bg-orange-500',
                'clickable' => true,
                'action' => 'navigateToBookings()',
                'prefix' => '₹',
                'suffix' => ''
            ]
        ]" />
    </div>
</x-page-header>

@push('scripts')
<script>
function accommodationStats() {
    return {
        accommodations: @json($accommodations->items() ?? []),
        
        get stats() {
            return {
                total: this.accommodations.length,
                active: this.accommodations.filter(a => a.property.status === 'active').length,
                totalPrice: this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0),
                avgPrice: this.accommodations.length > 0 ? this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0) / this.accommodations.length : 0
            };
        },

        init() {
            // Accommodation stats initialized
        },

        // Navigation functions for clickable stats
        navigateToAllAccommodations() {
            window.location.href = '/accommodations';
        },

        navigateToActiveAccommodations() {
            window.location.href = '/accommodations?status=active';
        },

        navigateToProperties() {
            window.location.href = '/properties';
        },

        navigateToBookings() {
            window.location.href = '/bookings';
        }
    }
}
</script>
@endpush
