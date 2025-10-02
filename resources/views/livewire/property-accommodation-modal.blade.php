<div x-data="{ show: @entangle('showModal') }" x-show="show" x-on:keydown.escape.window="show = false" style="display: none; z-index: 99999 !important;" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40">
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative w-full max-w-md sm:max-w-lg mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $accommodationId ? 'Edit' : 'Add' }} Accommodation</h3>
                        <p class="text-sm text-emerald-600 font-medium">{{ $property->name ?? '' }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-xl transition-all duration-200 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    
                    <div>
                        <label for="predefinedTypeId" class="block text-sm font-semibold text-gray-700 mb-2">Accommodation Type</label>
                        <select id="predefinedTypeId" wire:model="predefinedTypeId" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select Accommodation Type</option>
                            @foreach($predefinedTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('predefinedTypeId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="customName" class="block text-sm font-semibold text-gray-700 mb-2">Custom Name (Optional)</label>
                        <input type="text" id="customName" wire:model.lazy="customName" placeholder="Override default name..." class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                        @error('customName') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="maxOccupancy" class="block text-sm font-semibold text-gray-700 mb-2">Max Occupancy</label>
                            <input type="number" id="maxOccupancy" wire:model.lazy="maxOccupancy" min="1" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                            @error('maxOccupancy') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="basePrice" class="block text-sm font-semibold text-gray-700 mb-2">Base Price (₹/night)</label>
                            <input type="number" id="basePrice" wire:model.lazy="basePrice" step="0.01" min="0" class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                            @error('basePrice') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea id="description" wire:model.lazy="description" rows="3" placeholder="Describe this accommodation..." class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="isActive" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </form>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-6 py-4 rounded-b-2xl">
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" wire:click="closeModal" class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm">
                        Cancel
                    </button>
                    <button type="submit" wire:click="save" class="flex-1 sm:flex-none sm:px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <span class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ $accommodationId ? 'Update' : 'Add' }} Accommodation</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

