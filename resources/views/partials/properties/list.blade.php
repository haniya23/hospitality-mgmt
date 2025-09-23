@push('styles')
<style>
    .property-card { 
        background: white; 
        transition: all 0.3s ease;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .property-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 40px rgba(0,0,0,0.15); 
    }
    .status-active { 
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669; 
    }
    .status-pending { 
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706; 
    }
    .status-inactive { 
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626; 
    }
    .property-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    .action-btn:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

<div class="space-y-4">
    <template x-for="property in filteredProperties" :key="property.id">
        <div class="property-card p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="flex items-center space-x-3 mb-3 sm:mb-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl property-icon flex items-center justify-center text-white font-bold">
                        <i class="fas fa-building text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800 text-lg" x-text="property.name"></h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-xs px-3 py-1 rounded-full font-medium"
                                  :class="'status-' + property.status"
                                  x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                            <span class="text-xs text-gray-500" x-text="property.category"></span>
                        </div>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xl font-bold text-gray-800" x-text="property.accommodations_count + ' rooms'"></div>
                    <div class="text-sm text-gray-500">85% occupied</div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-4">
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-map-marker-alt text-gray-500"></i>
                    <span class="text-sm text-gray-600 font-medium" x-text="property.location?.city + ', ' + property.location?.state"></span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800" x-text="property.accommodations_count"></div>
                        <div class="text-xs text-gray-500">Rooms</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800">4.5</div>
                        <div class="text-xs text-gray-500">Rating</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800">12</div>
                        <div class="text-xs text-gray-500">Bookings</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <a :href="'/properties/' + property.id + '/edit'" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl font-medium text-sm hover:from-blue-600 hover:to-blue-700 transition text-center action-btn">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Property
                </a>
                <div class="flex gap-2">
                    <button class="bg-gradient-to-r from-green-100 to-green-200 text-green-700 py-3 px-4 rounded-xl font-medium text-sm hover:from-green-200 hover:to-green-300 transition action-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="bg-gradient-to-r from-red-100 to-red-200 text-red-700 py-3 px-4 rounded-xl font-medium text-sm hover:from-red-200 hover:to-red-300 transition action-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-if="filteredProperties.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-building"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Properties Found</h3>
            <p class="text-gray-500 text-sm mb-4">Start by adding your first property.</p>
            <a href="{{ route('properties.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add Property
            </a>
        </div>
    </template>
</div>