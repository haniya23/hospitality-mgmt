@push('styles')
<style>
    .accommodation-card { background: white; transition: all 0.3s ease; }
    .accommodation-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
</style>
@endpush

<div class="space-y-4">
    <template x-for="accommodation in filteredAccommodations" :key="accommodation.id">
        <div class="accommodation-card rounded-2xl p-4 shadow-sm">
            <!-- Accommodation Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900" x-text="accommodation.custom_name"></h3>
                        <p class="text-sm text-gray-500" x-text="accommodation.property.name"></p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900" x-text="'₹' + accommodation.base_price"></div>
                    <div class="text-sm text-gray-500">per night</div>
                </div>
            </div>

            <!-- Accommodation Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Type</div>
                    <div class="font-medium text-gray-900" x-text="accommodation.predefined_type?.name || 'Custom'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Max Occupancy</div>
                    <div class="font-medium text-gray-900" x-text="accommodation.max_occupancy + ' guests'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Size</div>
                    <div class="font-medium text-gray-900" x-text="accommodation.size ? accommodation.size + ' sq ft' : 'Not specified'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Amenities</div>
                    <div class="font-medium text-gray-900" x-text="accommodation.amenities?.length || 0 + ' amenities'"></div>
                </div>
            </div>

            <!-- Description -->
            <div x-show="accommodation.description" class="mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-1">Description</div>
                    <div class="text-sm text-gray-700" x-text="accommodation.description"></div>
                </div>
            </div>

            <!-- Amenities List -->
            <div x-show="accommodation.amenities && accommodation.amenities.length > 0" class="mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-500 mb-2">Amenities</div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="amenity in accommodation.amenities" :key="amenity.id">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i :class="amenity.icon" class="mr-1"></i>
                                <span x-text="amenity.name"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <span>Created: </span>
                    <span class="font-medium text-gray-900" x-text="new Date(accommodation.created_at).toLocaleDateString()"></span>
                </div>
                <div class="flex space-x-2">
                    <a :href="'/accommodations/' + accommodation.uuid" 
                       class="bg-blue-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-blue-600 transition">
                        <i class="fas fa-eye mr-1"></i>
                        View
                    </a>
                    <a :href="'/accommodations/' + accommodation.uuid + '/edit'" 
                       class="bg-green-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-green-600 transition">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <button @click="deleteAccommodation(accommodation.uuid)" 
                            class="bg-red-500 text-white py-2 px-4 rounded-xl font-medium text-sm hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <template x-if="filteredAccommodations.length === 0">
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-bed"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No accommodations found</h3>
            <p class="text-gray-500">There are no accommodations matching your criteria.</p>
        </div>
    </template>
</div>

<!-- Pagination -->
<div x-show="lastPage > 1" class="mt-8 flex items-center justify-between">
    <div class="text-sm text-gray-700">
        Showing <span x-text="from"></span> to <span x-text="to"></span> of <span x-text="total"></span> results
    </div>
    
    <div class="flex items-center space-x-2">
        <template x-for="link in paginationLinks" :key="link.page">
            <button @click="goToPage(link.page)" 
                    :disabled="link.disabled"
                    :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-text="link.label"></span>
            </button>
        </template>
    </div>
</div>
