<div x-data="{ show: @entangle('isOpen') }" x-show="show" x-on:keydown.escape.window="show = false" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40">
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative w-full max-w-md sm:max-w-lg lg:max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col">
            
            <!-- Header with Gradient Background -->
            <div class="flex items-center justify-between p-4 sm:p-6 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl border-b border-emerald-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">Manage Photos</h3>
                        <p class="text-sm text-emerald-600 font-medium mt-1">Upload property images (max 512KB each)</p>
                    </div>
                </div>
                <button wire:click="close" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-xl transition-all duration-200 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 space-y-6">
                    
                    <!-- Main Photo Section -->
                    <div class="bg-gradient-to-br from-white/90 to-blue-50/80 backdrop-blur-xl rounded-xl p-4 shadow-xl border border-white/20">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Main Property Photo</h4>
                                <p class="text-xs text-gray-600">Primary image for your property</p>
                            </div>
                        </div>
                        
                        @if($existingMainPhoto)
                            <div class="relative group">
                                <img src="{{ Storage::url($existingMainPhoto->file_path) }}" 
                                     alt="Main Photo" 
                                     class="w-full h-48 sm:h-64 object-cover rounded-xl shadow-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                                    <button wire:click="removePhoto({{ $existingMainPhoto->id }})"
                                            class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 font-medium text-sm">
                                        Remove Photo
                                    </button>
                                </div>
                                <span class="absolute top-3 left-3 bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">MAIN</span>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-emerald-200 rounded-xl p-6 sm:p-8 text-center hover:border-emerald-300 hover:bg-emerald-50/50 transition-all cursor-pointer">
                                <label class="cursor-pointer block">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm sm:text-base font-medium text-gray-700 mb-2">Drop main photo here</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mb-4">or tap to browse</p>
                                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-4 sm:px-6 py-2 rounded-lg inline-block hover:from-emerald-700 hover:to-teal-700 transition-all text-sm font-medium">
                                        Choose Main Photo
                                    </div>
                                    <input type="file" wire:model="mainPhoto" class="hidden" accept="image/*">
                                </label>
                            </div>
                            @error('mainPhoto') <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <!-- Additional Photos Section -->
                    <div class="bg-gradient-to-br from-white/90 to-teal-50/80 backdrop-blur-xl rounded-xl p-4 shadow-xl border border-white/20">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Additional Photos</h4>
                                    <p class="text-xs text-gray-600">{{ $existingAdditionalPhotos->count() }}/3 photos uploaded</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            @if($existingAdditionalPhotos->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($existingAdditionalPhotos as $photo)
                                        <div class="relative group bg-white/60 rounded-lg border border-gray-100 overflow-hidden">
                                            <img src="{{ Storage::url($photo->file_path) }}" 
                                                 alt="Additional Photo" 
                                                 class="w-full h-24 sm:h-32 object-cover">
                                            <button wire:click="removePhoto({{ $photo->id }})"
                                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($existingAdditionalPhotos->count() < 3)
                                <div class="border-2 border-dashed border-emerald-200 rounded-lg p-4 sm:p-6 text-center hover:border-emerald-300 hover:bg-emerald-50/50 transition-all cursor-pointer">
                                    <label class="cursor-pointer block">
                                        <svg class="w-8 h-8 text-emerald-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-700 mb-1">Add more photos</p>
                                        <p class="text-xs text-gray-500">Multiple selection allowed</p>
                                        <input type="file" wire:model="additionalPhotos" class="hidden" accept="image/*" multiple>
                                    </label>
                                </div>
                                @error('additionalPhotos.*') <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer with Better Actions -->
            <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-4 sm:px-6 py-4 rounded-b-2xl">
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button wire:click="close" class="flex-1 sm:flex-none sm:px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 shadow-sm">
                        Close
                    </button>
                    
                    @if($existingMainPhoto || $existingAdditionalPhotos->count() > 0)
                        <button wire:click="removeAllPhotos" wire:confirm="Are you sure you want to remove all photos?" 
                                class="flex-1 sm:flex-none sm:px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Remove All</span>
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>