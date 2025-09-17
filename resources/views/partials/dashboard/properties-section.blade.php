@push('styles')
<style>
    .property-card { transition: all 0.3s ease; }
    .property-card:hover { transform: translateY(-5px); }
</style>
@endpush

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Your Properties</h3>
            <a href="{{ route('properties.index') }}" class="text-blue-600 font-medium text-sm">View all</a>
        </div>
    </div>
    
    <div class="p-4 space-y-4">
        <template x-for="property in properties.slice(0, 3)" :key="property.id">
            <div class="property-card bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                         :style="'background: linear-gradient(135deg, ' + property.color + ')'">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800" x-text="property.name"></h4>
                        <p class="text-sm text-gray-500" x-text="property.category"></p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-xs px-2 py-1 rounded-full font-medium"
                                  :class="property.status === 'active' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'"
                                  x-text="property.status.charAt(0).toUpperCase() + property.status.slice(1)"></span>
                            <span class="text-xs text-gray-500" x-text="property.rooms + ' rooms'"></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-800" x-text="property.occupancy + '%'"></div>
                        <div class="text-xs text-gray-500">Occupancy</div>
                    </div>
                </div>
            </div>
        </template>
        
        <template x-if="properties.length === 0">
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl flex items-center justify-center text-white text-2xl">
                    <i class="fas fa-plus"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Create Your First Property</h4>
                <p class="text-gray-500 text-sm mb-4">Start managing your hospitality business by adding a property.</p>
                <a href="{{ route('properties.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium inline-block">
                    <i class="fas fa-plus mr-2"></i>
                    Add Property
                </a>
            </div>
        </template>
    </div>
</div>