<div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <!-- Left Section -->
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bed text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        Accommodations
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base">Manage your rooms and accommodations</p>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('accommodations.create') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                <i class="fas fa-plus mr-2"></i>
                Add Accommodation
            </a>
        </div>
    </div>
    
    <div x-data="accommodationStats()" x-init="init()" class="mt-6">
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
</div>

@push('scripts')
<script>
function accommodationStats() {
    return {
        accommodations: @json($accommodations->items() ?? []),
        
        get stats() {
            return {
                total: this.accommodations.length,
                active: this.accommodations.filter(a => a.property.status === 'active').length,
                totalPrice: Math.round(this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0) * 100) / 100,
                avgPrice: this.accommodations.length > 0 ? Math.round((this.accommodations.reduce((sum, a) => sum + parseFloat(a.base_price), 0) / this.accommodations.length) * 100) / 100 : 0
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
