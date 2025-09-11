@extends('layouts.mobile')

@section('title', 'Edit Property - Hospitality Manager')
@section('page-title', 'Edit Property')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Property Header -->
        <div class="bg-gradient-to-br from-white/90 to-emerald-50/80 backdrop-blur-xl rounded-2xl p-4 sm:p-6 shadow-xl border border-white/20">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">{{ $property->name }}</h2>
                        <p class="text-sm text-emerald-600 font-medium">{{ $property->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 text-xs font-semibold rounded-xl shadow-sm
                    @if($property->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                    @elseif($property->status === 'active') bg-green-100 text-green-800 border border-green-200
                    @else bg-red-100 text-red-800 border border-red-200 @endif">
                    {{ ucfirst($property->status) }}
                </span>
            </div>
            
            <div class="flex justify-between items-center gap-4">
                <button 
                    x-data
                    @click="$dispatch('openPhotoModal')"
                    class="group flex items-center gap-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white px-4 py-2 rounded-xl font-medium text-sm hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Manage Photos
                </button>
                <div class="flex justify-between items-center text-xs text-gray-500 bg-white/50 rounded-lg p-2 flex-1">
                <span class="flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <span>Created {{ $property->created_at->format('M d, Y') }}</span>
                </span>
                @if($property->approved_at)
                    <span class="flex items-center space-x-1">
                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Approved {{ $property->approved_at->format('M d, Y') }}</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Property Editor Sections -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <!-- Basic Info Section -->
            <div x-data class="group bg-gradient-to-br from-white/90 to-blue-50/80 backdrop-blur-xl rounded-xl p-4 cursor-pointer hover:shadow-xl transition-all duration-300 border border-white/20 hover:border-emerald-200 hover:scale-[1.02]" 
                 @click="$dispatch('open-property-modal', { propertyId: {{ $property->id }} })">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Basic Information</h4>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ Str::limit($property->name, 20) }} • {{ $property->category->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Location Section -->
            <div x-data class="group bg-gradient-to-br from-white/90 to-purple-50/80 backdrop-blur-xl rounded-xl p-4 cursor-pointer hover:shadow-xl transition-all duration-300 border border-white/20 hover:border-emerald-200 hover:scale-[1.02]"
                 @click="$dispatch('open-property-modal', { propertyId: {{ $property->id }}, section: 'location' })">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Location & Address</h4>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ Str::limit($property->location->address ?? 'No location added', 25) }}</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Accommodation Section -->
            <div class="bg-gradient-to-br from-white/90 to-teal-50/80 backdrop-blur-xl rounded-xl p-4 shadow-xl border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v0"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Rooms & Accommodation</h4>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $property->propertyAccommodations->count() }} accommodation(s) configured</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @forelse($property->propertyAccommodations as $accommodation)
                        <div class="bg-white/60 rounded-lg p-3 border border-gray-100 hover:border-emerald-200 transition-colors cursor-pointer" 
                             x-data @click="$dispatch('open-accommodation-modal', { propertyId: {{ $property->id }}, accommodationId: {{ $accommodation->id }} })">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h5 class="font-medium text-gray-900">{{ $accommodation->display_name }}</h5>
                                    <p class="text-xs text-gray-600">Max {{ $accommodation->max_occupancy }} guests • ₹{{ number_format($accommodation->base_price) }}/night</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($accommodation->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Inactive</span>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            </svg>
                            <p class="text-gray-500 text-sm mb-3">No accommodations configured</p>
                        </div>
                    @endforelse
                    
                    <!-- Add New Accommodation Button -->
                    <button x-data @click="$dispatch('open-accommodation-modal', { propertyId: {{ $property->id }} })" 
                            class="w-full border-2 border-dashed border-emerald-200 rounded-lg p-4 text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200 group">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="font-medium">Add Accommodation</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Amenities Section -->
            <div x-data class="group bg-gradient-to-br from-white/90 to-orange-50/80 backdrop-blur-xl rounded-xl p-4 cursor-pointer hover:shadow-xl transition-all duration-300 border border-white/20 hover:border-emerald-200 hover:scale-[1.02]"
                 @click="$dispatch('open-property-modal', { propertyId: {{ $property->id }}, section: 'amenities' })">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Amenities & Facilities</h4>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">WiFi, Parking, Pool & more</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Policies Section -->
            <div x-data class="group bg-gradient-to-br from-white/90 to-indigo-50/80 backdrop-blur-xl rounded-xl p-4 cursor-pointer hover:shadow-xl transition-all duration-300 border border-white/20 hover:border-emerald-200 hover:scale-[1.02]"
                 @click="$dispatch('open-property-modal', { propertyId: {{ $property->id }}, section: 'policies' })">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">Policies & Rules</h4>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">Check-in, cancellation & house rules</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>


        </div>

        <!-- Property Details Summary -->
        <div class="bg-gradient-to-br from-white/90 to-gray-50/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/20">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-700 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Property Summary</h3>
                    <p class="text-sm text-gray-600">Complete overview of your property details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Basic Information
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">{{ $property->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ $property->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($property->status === 'active') bg-green-100 text-green-800
                                @elseif($property->status === 'draft') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Owner:</span>
                            <span class="font-medium">{{ $property->owner->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Location
                    </h4>
                    @if($property->location)
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-600">Address:</span>
                                <p class="font-medium mt-1">{{ $property->location->address }}</p>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Country:</span>
                                <span class="font-medium">{{ $property->location->country->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">State:</span>
                                <span class="font-medium">{{ $property->location->state->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">District:</span>
                                <span class="font-medium">{{ $property->location->district->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No location information added</p>
                    @endif
                </div>

                <!-- Accommodation Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Accommodation
                    </h4>
                    @if($property->propertyAccommodations->count() > 0)
                        @foreach($property->propertyAccommodations as $accommodation)
                            <div class="mb-3 last:mb-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ $accommodation->display_name }}</p>
                                        <p class="text-xs text-gray-600">Max {{ $accommodation->max_occupancy }} guests</p>
                                    </div>
                                    <span class="text-sm font-semibold text-teal-600">₹{{ number_format($accommodation->base_price) }}/night</span>
                                </div>
                                @if($accommodation->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($accommodation->description, 80) }}</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">No accommodation details added</p>
                    @endif
                </div>

                <!-- Amenities Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Amenities ({{ $property->amenities->count() }})
                    </h4>
                    @if($property->amenities->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($property->amenities as $amenity)
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">{{ $amenity->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No amenities selected</p>
                    @endif
                </div>

                <!-- Policies Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Policies
                    </h4>
                    @if($property->policy)
                        <div class="space-y-2 text-sm">
                            @if($property->policy->check_in_time || $property->policy->check_out_time)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in/out:</span>
                                    <span class="font-medium">
                                        {{ $property->policy->check_in_time ? date('g:i A', strtotime($property->policy->check_in_time)) : 'N/A' }} - 
                                        {{ $property->policy->check_out_time ? date('g:i A', strtotime($property->policy->check_out_time)) : 'N/A' }}
                                    </span>
                                </div>
                            @endif
                            @if($property->policy->cancellation_policy)
                                <div>
                                    <span class="text-gray-600">Cancellation:</span>
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($property->policy->cancellation_policy, 60) }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No policies configured</p>
                    @endif
                </div>

                <!-- Photos Information -->
                <div class="bg-white/60 rounded-xl p-4 border border-gray-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Photos ({{ $property->photos->count() }})
                    </h4>
                    @if($property->photos->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($property->photos->groupBy('caption') as $caption => $photos)
                                <span class="px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $caption)) }} ({{ $photos->count() }})
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No photos uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('property-updated', () => {
            location.reload();
        });
        
        window.addEventListener('accommodation-updated', () => {
            location.reload();
        });
    </script>

    @livewire('property-modal')
    @livewire('property-accommodation-modal')
    <!-- Photo Management Modal -->
    <livewire:property-photo-modal :property="$property" />
@endsection