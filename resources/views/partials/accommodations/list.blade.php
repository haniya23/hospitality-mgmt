@push('styles')
<style>
    .accommodation-card { 
        background: white; 
        transition: all 0.3s ease; 
        border: 1px solid #e5e7eb;
    }
    .accommodation-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 20px 40px rgba(0,0,0,0.12); 
        border-color: #d1d5db;
    }
    .accommodation-card img {
        transition: transform 0.3s ease;
    }
    .accommodation-card:hover img {
        transform: scale(1.05);
    }
</style>
@endpush

<div x-data="accommodationListData()" x-init="init()" class="space-y-3 sm:space-y-4 overflow-y-auto">
    <template x-for="accommodation in filteredAccommodations" :key="accommodation.id">
        <div class="accommodation-card rounded-2xl overflow-hidden shadow-sm">
            <!-- Accommodation Image -->
            <div class="relative h-48 sm:h-56 lg:h-64 bg-gray-200">
                <template x-if="accommodation.photos && accommodation.photos.length > 0">
                    <img :src="'/storage/' + accommodation.photos[0].file_path" 
                         :alt="accommodation.custom_name + ' photo'"
                         class="w-full h-full object-cover">
                </template>
                <template x-if="!accommodation.photos || accommodation.photos.length === 0">
                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-bed text-4xl sm:text-5xl mb-2 opacity-80"></i>
                            <p class="text-sm sm:text-base font-medium opacity-90">No Image Available</p>
                        </div>
                    </div>
                </template>
                <!-- Price Badge -->
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 shadow-lg">
                    <div class="text-lg font-bold text-gray-900" x-text="'₹' + accommodation.base_price"></div>
                    <div class="text-xs text-gray-600">per night</div>
                </div>
            </div>
            
            <!-- Card Content -->
            <div class="p-3 sm:p-4 lg:p-6">
                <!-- Accommodation Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-base sm:text-lg truncate" x-text="accommodation.custom_name"></h3>
                        <p class="text-sm text-gray-500 truncate" x-text="accommodation.property.name"></p>
                    </div>
                </div>

            <!-- Accommodation Details -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4 mb-3 sm:mb-4">
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Type</div>
                    <div class="font-medium text-gray-900 text-xs sm:text-sm" x-text="accommodation.predefined_type?.name || 'Custom'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Max Occupancy</div>
                    <div class="font-medium text-gray-900 text-xs sm:text-sm" x-text="accommodation.max_occupancy + ' guests'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Size</div>
                    <div class="font-medium text-gray-900 text-xs sm:text-sm" x-text="accommodation.size ? accommodation.size + ' sq ft' : 'Not specified'"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Amenities</div>
                    <div class="font-medium text-gray-900 text-xs sm:text-sm" x-text="accommodation.amenities?.length || 0 + ' amenities'"></div>
                </div>
            </div>

            <!-- Description -->
            <div x-show="accommodation.description" class="mb-3 sm:mb-4">
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Description</div>
                    <div class="text-xs sm:text-sm text-gray-700" x-text="accommodation.description"></div>
                </div>
            </div>

            <!-- Amenities List -->
            <div x-show="accommodation.amenities && accommodation.amenities.length > 0" class="mb-3 sm:mb-4">
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-2">Amenities</div>
                    <div class="flex flex-wrap gap-1 sm:gap-2">
                        <template x-for="amenity in accommodation.amenities" :key="amenity.id">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i :class="amenity.icon" class="mr-1 text-xs"></i>
                                <span x-text="amenity.name"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Maintenance and Renovation Details -->
            <div x-show="accommodation.maintenance_status !== 'none' || accommodation.renovation_status !== 'none'" class="mb-3 sm:mb-4">
                <div class="bg-gray-50 rounded-lg p-2 sm:p-3">
                    <div class="text-xs sm:text-sm text-gray-500 mb-2">Maintenance & Renovation</div>
                    
                    <!-- Maintenance Status -->
                    <div x-show="accommodation.maintenance_status !== 'none'" class="mb-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Maintenance:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-red-100 text-red-800': accommodation.maintenance_status === 'active',
                                      'bg-yellow-100 text-yellow-800': accommodation.maintenance_status === 'scheduled',
                                      'bg-green-100 text-green-800': accommodation.maintenance_status === 'completed'
                                  }"
                                  x-text="accommodation.maintenance_status === 'scheduled' ? 'Pending' : accommodation.maintenance_status.charAt(0).toUpperCase() + accommodation.maintenance_status.slice(1)"></span>
                        </div>
                        <div x-show="accommodation.maintenance_start_date && accommodation.maintenance_end_date" class="text-xs text-gray-600 mt-1">
                            <span x-text="new Date(accommodation.maintenance_start_date).toLocaleDateString()"></span> - 
                            <span x-text="new Date(accommodation.maintenance_end_date).toLocaleDateString()"></span>
                        </div>
                        <div x-show="accommodation.maintenance_status === 'scheduled' && accommodation.maintenance_start_date" class="text-xs text-blue-600 mt-1 font-medium">
                            Available in <span x-text="getDaysUntilAvailable(accommodation.maintenance_start_date)"></span> days
                        </div>
                        <div x-show="accommodation.maintenance_status === 'active' && accommodation.maintenance_end_date" class="text-xs text-red-600 mt-1 font-medium">
                            Available in <span x-text="getDaysUntilAvailable(accommodation.maintenance_end_date)"></span> days
                        </div>
                        <div x-show="accommodation.maintenance_description" class="text-xs text-gray-600 mt-1" x-text="accommodation.maintenance_description"></div>
                        <div x-show="accommodation.maintenance_cost" class="text-xs text-gray-600 mt-1">
                            Cost: ₹<span x-text="accommodation.maintenance_cost"></span>
                        </div>
                    </div>
                    
                    <!-- Renovation Status -->
                    <div x-show="accommodation.renovation_status !== 'none'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Renovation:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-red-100 text-red-800': accommodation.renovation_status === 'active',
                                      'bg-yellow-100 text-yellow-800': accommodation.renovation_status === 'scheduled',
                                      'bg-green-100 text-green-800': accommodation.renovation_status === 'completed'
                                  }"
                                  x-text="accommodation.renovation_status === 'scheduled' ? 'Pending' : accommodation.renovation_status.charAt(0).toUpperCase() + accommodation.renovation_status.slice(1)"></span>
                        </div>
                        <div x-show="accommodation.renovation_start_date && accommodation.renovation_end_date" class="text-xs text-gray-600 mt-1">
                            <span x-text="new Date(accommodation.renovation_start_date).toLocaleDateString()"></span> - 
                            <span x-text="new Date(accommodation.renovation_end_date).toLocaleDateString()"></span>
                        </div>
                        <div x-show="accommodation.renovation_status === 'scheduled' && accommodation.renovation_start_date" class="text-xs text-blue-600 mt-1 font-medium">
                            Available in <span x-text="getDaysUntilAvailable(accommodation.renovation_start_date)"></span> days
                        </div>
                        <div x-show="accommodation.renovation_status === 'active' && accommodation.renovation_end_date" class="text-xs text-red-600 mt-1 font-medium">
                            Available in <span x-text="getDaysUntilAvailable(accommodation.renovation_end_date)"></span> days
                        </div>
                        <div x-show="accommodation.renovation_description" class="text-xs text-gray-600 mt-1" x-text="accommodation.renovation_description"></div>
                        <div x-show="accommodation.renovation_cost" class="text-xs text-gray-600 mt-1">
                            Cost: ₹<span x-text="accommodation.renovation_cost"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs sm:text-sm text-gray-500">
                    <span>Created: </span>
                    <span class="font-medium text-gray-900" x-text="new Date(accommodation.created_at).toLocaleDateString()"></span>
                </div>
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
                    <!-- First Row: Book Now & Details -->
                    <button @click="bookAccommodation(accommodation)" 
                            :disabled="accommodation.maintenance_status === 'active' || accommodation.renovation_status === 'active'"
                            :class="{
                                'bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600': !(accommodation.maintenance_status === 'active' || accommodation.renovation_status === 'active'),
                                'bg-gray-400 cursor-not-allowed': accommodation.maintenance_status === 'active' || accommodation.renovation_status === 'active'
                            }"
                            class="text-white py-2 px-3 sm:px-4 rounded-xl font-medium text-xs sm:text-sm transition flex items-center justify-center">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        <span class="hidden sm:inline" x-text="(accommodation.maintenance_status === 'active' || accommodation.renovation_status === 'active') ? 'Unavailable' : 'Book Now'"></span>
                        <span class="sm:hidden" x-text="(accommodation.maintenance_status === 'active' || accommodation.renovation_status === 'active') ? 'Unavailable' : 'Book Now'"></span>
                    </button>
                    <a :href="'/accommodations/' + accommodation.uuid" 
                       class="bg-blue-500 text-white py-2 px-3 sm:px-4 rounded-xl font-medium text-xs sm:text-sm hover:bg-blue-600 transition flex items-center justify-center">
                        <i class="fas fa-eye mr-1"></i>
                        <span class="hidden sm:inline">Details</span>
                        <span class="sm:hidden">Details</span>
                    </a>
                    <!-- Second Row: Edit & Delete -->
                    <a :href="'/accommodations/' + accommodation.uuid + '/edit'" 
                       class="bg-gray-500 text-white py-2 px-3 sm:px-4 rounded-xl font-medium text-xs sm:text-sm hover:bg-gray-600 transition flex items-center justify-center">
                        <i class="fas fa-edit mr-1"></i>
                        <span class="hidden sm:inline">Edit</span>
                        <span class="sm:hidden">Edit</span>
                    </a>
                    <a :href="'/accommodations/' + accommodation.uuid + '/maintenance'" 
                       class="bg-orange-500 text-white py-2 px-3 sm:px-4 rounded-xl font-medium text-xs sm:text-sm hover:bg-orange-600 transition flex items-center justify-center">
                        <i class="fas fa-tools mr-1"></i>
                        <span class="hidden sm:inline">Maintenance</span>
                        <span class="sm:hidden">Maint.</span>
                    </a>
                </div>
            </div>
        </div>
    </template>
    
    <template x-if="filteredAccommodations.length === 0">
        <div class="text-center py-8 sm:py-12">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 bg-gradient-to-r from-blue-400 to-purple-400 rounded-2xl flex items-center justify-center text-white text-2xl sm:text-3xl">
                <i class="fas fa-bed"></i>
            </div>
            <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No accommodations found</h3>
            <p class="text-sm sm:text-base text-gray-500">There are no accommodations matching your criteria.</p>
        </div>
    </template>
</div>

<!-- Pagination -->
<div x-show="lastPage > 1" class="mt-6 sm:mt-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
            Showing <span x-text="from"></span> to <span x-text="to"></span> of <span x-text="total"></span> results
        </div>
        
        <div class="flex items-center justify-center space-x-1 sm:space-x-2">
            <template x-for="link in paginationLinks" :key="link.page">
                <button @click="goToPage(link.page)" 
                        :disabled="link.disabled"
                        :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed min-w-[2rem] sm:min-w-[2.5rem]">
                    <span x-text="link.label"></span>
                </button>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function accommodationListData() {
    return {
        init() {
            // Initialize accommodation list data
        },

        getDaysUntilAvailable(dateString) {
            if (!dateString) return 0;
            const targetDate = new Date(dateString);
            const today = new Date();
            const diffTime = targetDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays > 0 ? diffDays : 0;
        }
    }
}
</script>
@endpush
