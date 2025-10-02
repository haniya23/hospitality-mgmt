@props(['cards' => []])

<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6" style="z-index: 1 !important;">
    @foreach($cards as $index => $card)
    <div class="group relative bg-white rounded-2xl overflow-hidden p-3 sm:p-4 lg:p-6 shadow-lg hover:shadow-xl transition-all duration-300 {{ $card['clickable'] ?? false ? 'cursor-pointer hover:scale-105' : '' }} border border-gray-100"
         style="z-index: 1 !important;"
         @if($card['clickable'] ?? false) @click="{{ $card['action'] }}" @endif>
        
        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br {{ $card['bgGradient'] ?? 'from-blue-50 to-purple-50' }} opacity-50 group-hover:opacity-70 transition-opacity duration-300"></div>
        
        <!-- Animated Accent -->
        <div class="absolute top-0 right-0 w-20 h-20 {{ $card['accentColor'] ?? 'bg-blue-500' }} rounded-full transform translate-x-8 -translate-y-8 group-hover:scale-150 transition-transform duration-500 opacity-10"></div>
        
        <!-- Icon (if provided) -->
        @if(isset($card['icon']))
        <div class="absolute top-3 right-3 sm:top-4 sm:right-4 text-gray-400 group-hover:text-blue-600 transition-colors duration-300 z-10">
            <i class="{{ $card['icon'] }} text-lg sm:text-xl lg:text-2xl"></i>
        </div>
        @endif
        
        <!-- Main Content -->
        <div class="relative z-10">
            <!-- Value Display -->
            <div class="mb-3">
                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-300"
                     @if(isset($card['value']) && is_numeric($card['value'])) 
                         x-data="{
                             displayValue: 0,
                             targetValue: {{ $card['value'] }},
                             animateValue() {
                                 let start = 0;
                                 let end = this.targetValue;
                                 let duration = 1500;
                                 let startTime = performance.now();
                                 
                                 const animate = (currentTime) => {
                                     let elapsed = currentTime - startTime;
                                     let progress = Math.min(elapsed / duration, 1);
                                     let easeProgress = 1 - Math.pow(1 - progress, 3);
                                     
                                     this.displayValue = Math.floor(start + (end - start) * easeProgress);
                                     
                                     if (progress < 1) {
                                         requestAnimationFrame(animate);
                                     }
                                 };
                                 requestAnimationFrame(animate);
                             }
                         }"
                         x-init="setTimeout(() => animateValue(), {{ $index * 200 + 300 }})"
                         x-text="displayValue.toLocaleString()"
                     @elseif(isset($card['value']))
                         x-text="{{ $card['value'] }}"
                     @else
                         x-data="{ value: '{{ $card['staticValue'] ?? '0' }}' }"
                         x-text="value"
                     @endif>
                    @if(!isset($card['value']))
                        {{ $card['staticValue'] ?? '0' }}
                    @endif
                </div>
                
                <!-- Value Unit/Suffix -->
                @if(isset($card['suffix']) || isset($card['prefix']))
                <div class="text-sm text-gray-500 group-hover:text-blue-500 transition-colors duration-300">
                    {{ $card['prefix'] ?? '' }}{{ $card['suffix'] ?? '' }}
                </div>
                @endif
            </div>
            
            <!-- Label -->
            <h3 class="text-xs sm:text-sm lg:text-base font-semibold text-gray-700 group-hover:text-gray-900 transition-colors duration-300 mb-1">
                {{ $card['label'] }}
            </h3>
            
            <!-- Subtitle -->
            @if(isset($card['subtitle']))
            <p class="text-xs sm:text-sm text-gray-500 group-hover:text-gray-600 transition-colors duration-300">
                {{ $card['subtitle'] }}
            </p>
            @endif
        </div>
        
        <!-- Bottom Section -->
        <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 flex justify-between items-end">
            <!-- Action Button -->
            @if($card['clickable'] ?? false)
            <button class="text-xs sm:text-sm text-blue-600 group-hover:text-blue-700 transition-colors duration-300 flex items-center gap-1 font-medium">
                @if(isset($card['buttonText']))
                <span>{{ $card['buttonText'] }}</span>
                <i class="fas fa-arrow-right text-xs"></i>
                @endif
            </button>
            @endif
            
            <!-- Trend Indicator -->
            @if(isset($card['trend']))
            <div class="flex items-center gap-1">
                @if($card['trend'] === 'up')
                    <i class="fas fa-arrow-up text-green-500 text-xs sm:text-sm"></i>
                @elseif($card['trend'] === 'down')
                    <i class="fas fa-arrow-down text-red-500 text-xs sm:text-sm"></i>
                @else
                    <i class="fas fa-minus text-yellow-500 text-xs sm:text-sm"></i>
                @endif
                
                @if(isset($card['trendValue']))
                <span class="text-xs text-gray-600">
                    {{ $card['trendValue'] }}
                </span>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Progress Bar (if provided) -->
        @if(isset($card['progress']))
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-100">
            <div class="h-full {{ $card['progressColor'] ?? 'bg-blue-500' }} transition-all duration-700 transform origin-left"
                 style="width: {{ $card['progress'] }}%"></div>
        </div>
        @endif
    </div>
    @endforeach
</div>