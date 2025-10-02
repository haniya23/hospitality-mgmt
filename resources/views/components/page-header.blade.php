@props(['title', 'subtitle', 'icon', 'addRoute' => null, 'addText' => 'Add'])

<header class="relative overflow-hidden mb-6" style="z-index: 1 !important;">
    <!-- Modern Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-white via-green-50/30 to-emerald-50/40 rounded-2xl"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 via-emerald-500/5 to-green-500/5 rounded-2xl"></div>
    
    <!-- Glass overlay -->
    <div class="absolute inset-0 bg-white/60 backdrop-blur-sm rounded-2xl border border-white/20"></div>
    
    <!-- Content -->
    <div class="relative px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Header Row -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Icon -->
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-{{ $icon }} text-white text-lg sm:text-xl"></i>
                </div>
                
                <!-- Title & Subtitle -->
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $title }}</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $subtitle }}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                <!-- Custom Actions Slot -->
                @if(isset($actions))
                    {{ $actions }}
                @endif
                
                <!-- Add Button -->
                @if($addRoute)
                <a href="{{ $addRoute }}" class="group bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus text-sm"></i>
                    <span class="hidden sm:inline">{{ $addText }}</span>
                    <span class="sm:hidden">Add</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Slot Content -->
        <div class="space-y-4">
            {{ $slot }}
        </div>
    </div>
</header>