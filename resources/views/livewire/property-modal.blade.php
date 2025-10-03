<div
    x-data="{ show: @entangle('showModal'), ...simpleModalScrollLock() }"
    x-init="setupScrollLock('show')"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    style="display: none; z-index: 99999 !important;"
    class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Mobile & Desktop Overlay -->
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        
        <!-- Modal Container -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md sm:max-w-lg lg:max-w-xl xl:max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col"
        >
            
            <!-- Header with Gradient Background -->
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        @if($section === 'basic')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        @elseif($section === 'location')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif($section === 'accommodation')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"/>
                            </svg>
                        @elseif($section === 'amenities')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        @elseif($section === 'policies')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($section === 'photos')
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight" id="modal-title">
                            @if($section === 'basic') Edit Basic Information
                            @elseif($section === 'location') Edit Location & Address
                            @elseif($section === 'accommodation') Edit Rooms & Accommodation
                            @elseif($section === 'amenities') Manage Amenities
                            @elseif($section === 'policies') Edit Policies & Rules
                            @elseif($section === 'photos') Manage Photos
                            @else Edit Property @endif
                        </h3>
                        <p class="text-sm text-emerald-600 font-medium mt-1">
                            @if($section === 'basic') Update your property's basic details
                            @elseif($section === 'location') Set location and address information
                            @elseif($section === 'accommodation') Configure room types and pricing
                            @elseif($section === 'amenities') Choose available amenities
                            @elseif($section === 'policies') Set check-in rules and policies
                            @elseif($section === 'photos') Upload and manage property photos
                            @else Manage your property settings @endif
                        </p>
                    </div>
                </div>
                <button 
                    wire:click="closeModal" 
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-xl transition-all duration-200 shadow-sm"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    
                    @if($section === 'basic')
                        <div class="space-y-6">
                            <div>
                                <label for="owner" class="block text-sm font-semibold text-gray-700 mb-2">Property Owner</label>
                                <input 
                                    type="text" 
                                    id="owner" 
                                    value="{{ $owner_name }}" 
                                    readonly 
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 bg-gray-50 text-gray-500 cursor-not-allowed"
                                >
                            </div>
                            
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Property Name</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    wire:model.lazy="name" 
                                    placeholder="Enter your property name..."
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                >
                                @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Property Type</label>
                                <x-custom-dropdown 
                                    :options="$categories->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])->toArray()"
                                    wireModel="property_category_id"
                                    placeholder="Select Property Type"
                                    searchable="true"
                                />
                                @error('property_category_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            

                            
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                <textarea 
                                    id="description" 
                                    wire:model.lazy="description" 
                                    rows="4" 
                                    placeholder="Describe your property in detail..."
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                                ></textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                    @elseif($section === 'location')
                        <div class="space-y-6">
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Full Address</label>
                                <textarea 
                                    id="address" 
                                    wire:model.lazy="address" 
                                    rows="3" 
                                    placeholder="Enter complete address..."
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                                ></textarea>
                                @error('address') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div wire:key="country-dropdown">
                                    <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                                    <x-custom-dropdown 
                                        :options="$countries->map(fn($country) => ['id' => $country->id, 'name' => $country->name])->toArray()"
                                        wireModel="country_id"
                                        placeholder="Select Country"
                                        searchable="true"
                                    />
                                    @error('country_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                                
                                <div wire:key="state-dropdown-{{ $country_id }}">
                                    <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                                    <x-custom-dropdown 
                                        :options="$states->map(fn($state) => ['id' => $state->id, 'name' => $state->name])->toArray()"
                                        wireModel="state_id"
                                        placeholder="Select State"
                                        searchable="true"
                                    />
                                    @error('state_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div wire:key="district-dropdown-{{ $state_id }}">
                                    <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">District</label>
                                    <x-custom-dropdown 
                                        :options="$districts->map(fn($district) => ['id' => $district->id, 'name' => $district->name])->toArray()"
                                        wireModel="district_id"
                                        placeholder="Select District"
                                        searchable="true"
                                    />
                                    @error('district_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                                
                                <div wire:key="city-dropdown-{{ $district_id }}">
                                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                    <x-custom-dropdown 
                                        :options="$cities->map(fn($city) => ['id' => $city->id, 'name' => $city->name])->toArray()"
                                        wireModel="city_id"
                                        placeholder="Select City"
                                        searchable="true"
                                    />
                                    @error('city_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div wire:key="pincode-dropdown-{{ $city_id }}">
                                <label for="pincode" class="block text-sm font-semibold text-gray-700 mb-2">Pincode</label>
                                <x-custom-dropdown 
                                    :options="$pincodes->map(fn($pincode) => ['id' => $pincode->id, 'name' => $pincode->code])->toArray()"
                                    wireModel="pincode_id"
                                    placeholder="Select Pincode"
                                    searchable="true"
                                />
                                @error('pincode_id') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        

                        
                    @elseif($section === 'amenities')
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Select Available Amenities</h4>
                                <p class="text-xs text-gray-500">Choose all amenities available at your property</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto">
                                @foreach($amenities as $amenity)
                                    <label class="group flex items-center space-x-3 p-4 border border-gray-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50/50 cursor-pointer transition-all duration-200">
                                        <input 
                                            type="checkbox" 
                                            wire:model="selectedAmenities" 
                                            value="{{ $amenity->id }}" 
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2"
                                        >
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-emerald-700">{{ $amenity->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                    @elseif($section === 'policies')
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="check_in_time" class="block text-sm font-semibold text-gray-700 mb-2">Check-in Time</label>
                                    <input 
                                        type="time" 
                                        id="check_in_time" 
                                        wire:model.lazy="check_in_time" 
                                        class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                    >
                                    @error('check_in_time') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="check_out_time" class="block text-sm font-semibold text-gray-700 mb-2">Check-out Time</label>
                                    <input 
                                        type="time" 
                                        id="check_out_time" 
                                        wire:model.lazy="check_out_time" 
                                        class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                    >
                                    @error('check_out_time') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="cancellation_policy" class="block text-sm font-semibold text-gray-700 mb-2">Cancellation Policy</label>
                                <textarea 
                                    id="cancellation_policy" 
                                    wire:model.lazy="cancellation_policy" 
                                    rows="4" 
                                    placeholder="Describe your cancellation policy in detail..."
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                                ></textarea>
                                @error('cancellation_policy') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="house_rules" class="block text-sm font-semibold text-gray-700 mb-2">House Rules</label>
                                <textarea 
                                    id="house_rules" 
                                    wire:model.lazy="house_rules" 
                                    rows="4" 
                                    placeholder="List your house rules and guidelines for guests..."
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                                ></textarea>
                                @error('house_rules') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        

                        
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Feature Under Development</h3>
                            <p class="text-gray-500">This section is being enhanced with new functionality.</p>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Enhanced Footer with Better Actions -->
            <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-6 py-4 rounded-b-2xl">
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button 
                        type="button" 
                        wire:click="closeModal" 
                        class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    >
                        Cancel
                    </button>
                    
                    @if(in_array($section, ['basic', 'location', 'amenities', 'policies']))
                        <button 
                            type="submit" 
                            wire:click="save"
                            class="flex-1 sm:flex-none sm:px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            <span class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save Changes</span>
                            </span>
                        </button>
                    @else
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="flex-1 sm:flex-none sm:px-6 py-3 bg-gray-400 text-white font-medium rounded-xl hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200"
                        >
                            Close
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>