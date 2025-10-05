@php
    $user = auth()->user();
    $currentAccommodations = $user->properties()->withCount('propertyAccommodations')->get()->sum('property_accommodations_count');
    $maxAccommodations = $user->getMaxAccommodations();
    $remainingAccommodations = max(0, $maxAccommodations - $currentAccommodations);
    $canCreate = $user->canCreateAccommodation();
@endphp

<div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <!-- Left Section -->
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
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
            
            <!-- Accommodation Limits Info -->
            <div class="flex flex-wrap items-center gap-4 mt-4">
                <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span class="text-sm font-medium text-blue-700">
                        {{ $currentAccommodations }}/{{ $maxAccommodations }} Accommodations Used
                    </span>
                </div>
                
                @if($remainingAccommodations > 0)
                    <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-green-700">
                            {{ $remainingAccommodations }} Remaining
                        </span>
                    </div>
                @else
                    <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-red-50 to-rose-50 rounded-lg border border-red-200">
                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        <span class="text-sm font-medium text-red-700">
                            Limit Reached
                        </span>
                    </div>
                @endif
                
                <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg border border-purple-200">
                    <i class="fas fa-crown text-purple-500 text-xs"></i>
                    <span class="text-sm font-medium text-purple-700 capitalize">
                        {{ $user->subscription_status ?? 'starter' }} Plan
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex flex-col sm:flex-row gap-3">
            @if(!$canCreate)
                <a href="{{ route('subscription.plans') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                    <i class="fas fa-arrow-up mr-2"></i>
                    Upgrade Plan
                </a>
            @else
                <a href="{{ route('accommodations.create') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                    <i class="fas fa-plus mr-2"></i>
                    Add Accommodation
                </a>
            @endif
        </div>
    </div>

    @if(!$canCreate)
        <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-lg mt-0.5"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-800 mb-1">Accommodation Limit Reached</h3>
                    <p class="text-amber-700 text-sm">
                        You've reached your accommodation limit ({{ $maxAccommodations }}). 
                        <a href="{{ route('subscription.plans') }}" class="font-semibold underline hover:no-underline">
                            Upgrade your plan
                        </a> to create more accommodations.
                    </p>
                </div>
            </div>
        </div>
    @endif
    
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
