@extends('layouts.app')

@section('title', 'Create Property - Stay loops')
@section('page-title', 'Create Property')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-700 font-medium">Create Property</span>
        </nav>
    </div>

   

    <div class="bg-gradient-to-br from-white/95 to-emerald-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20">
        <!-- Enhanced Header with Icon -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">Create New Property</h2>
                <p class="text-sm text-emerald-600 font-medium mt-1">Add your property to start hosting guests</p>
                <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Takes 5 minutes
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Auto-approved
                    </div>
                </div>
            </div>
        </div>
            
        <form method="POST" action="{{ route('properties.store') }}" class="space-y-8">
            @csrf
            
            <!-- Property Name Section -->
            <div class="bg-white/50 rounded-xl p-6 border border-white/30">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Property Information</h3>
                        <p class="text-sm text-gray-600">Basic details about your property</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Property Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full border border-gray-200 rounded-xl shadow-sm py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-lg"
                               placeholder="Enter your property name..."
                               value="{{ old('name') }}">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div x-data="{ selectedCategory: '{{ old('property_category_id') }}' }">
                        <label for="property_category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Property Type <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="property_category_id" x-model="selectedCategory">
                        <div class="relative">
                            <div x-data="{
                                open: false,
                                selected: '{{ old('property_category_id') }}',
                                options: {{ $categories->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])->toJson() }},
                                getSelectedText() {
                                    const option = this.options.find(opt => opt.id == this.selected);
                                    return option ? option.name : 'Select Property Type';
                                },
                                selectOption(option) {
                                    this.selected = option.id;
                                    selectedCategory = option.id;
                                    this.open = false;
                                }
                            }">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full border border-gray-200 rounded-xl shadow-sm py-4 px-4 text-left text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white hover:border-gray-300 text-lg"
                                    :class="{ 'ring-2 ring-emerald-500 border-transparent': open }">
                                    <div class="flex items-center justify-between">
                                        <span x-text="getSelectedText()" :class="{ 'text-gray-400': !selected }"></span>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </button>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-hidden" style="display: none;">
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="option in options" :key="option.id">
                                            <button type="button" @click="selectOption(option)"
                                                class="w-full px-4 py-3 text-left text-sm hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 flex items-center justify-between"
                                                :class="{ 'bg-emerald-100 text-emerald-800 font-medium': selected == option.id }">
                                                <span x-text="option.name"></span>
                                                <svg x-show="selected == option.id" class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('property_category_id')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Description <span class="text-gray-400">(Optional)</span>
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border border-gray-200 rounded-xl shadow-sm py-4 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Describe your property in detail...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row gap-4 pt-6">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 sm:flex-none sm:px-8 py-4 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm text-center">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Cancel</span>
                    </span>
                </a>
                <button type="submit" 
                        class="flex-1 sm:flex-none sm:px-10 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Create Property</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection