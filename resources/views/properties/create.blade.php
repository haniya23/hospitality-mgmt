@extends('layouts.mobile')

@section('title', 'Create Property - Hospitality Manager')
@section('page-title', 'Create Property')

@section('content')
    <div class="bg-gradient-to-br from-white/95 to-emerald-50/90 backdrop-blur-xl rounded-2xl shadow-2xl p-4 sm:p-6 border border-white/20">
        <!-- Header with Icon -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Create New Property</h2>
                <p class="text-sm text-emerald-600 font-medium">Add your property to start hosting guests</p>
            </div>
        </div>
            
        <form method="POST" action="{{ route('properties.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Property Name</label>
                    <input type="text" id="name" name="name" required
                           class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter your property name..."
                           value="{{ old('name') }}">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div x-data="{ selectedCategory: '{{ old('property_category_id') }}' }">
                    <label for="property_category_id" class="block text-sm font-semibold text-gray-700 mb-2">Property Type</label>
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
                                class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-left text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white hover:border-gray-300"
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
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"
                              placeholder="Describe your property in detail...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 sm:flex-none sm:px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span>Create Property</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection