@props(['cards' => []])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($cards as $index => $card)
    <div class="group relative h-32 w-full bg-white rounded-2xl overflow-hidden p-4 shadow-lg hover:shadow-xl transition-all duration-300 {{ $card['clickable'] ?? false ? 'cursor-pointer' : '' }}"
         @if($card['clickable'] ?? false) @click="{{ $card['action'] }}" @endif>
        
        <!-- Animated Background Circle -->
        <div class="absolute h-20 w-20 -top-10 -right-10 rounded-full {{ $card['bgColor'] ?? 'bg-blue-500' }} group-hover:scale-[800%] duration-500 transition-transform z-0"></div>
        
        <!-- Icon (if provided) -->
        @if(isset($card['icon']))
        <div class="absolute top-3 right-3 text-gray-400 group-hover:text-white transition-colors duration-500 z-10">
            <i class="{{ $card['icon'] }} text-xl"></i>
        </div>
        @endif
        
        <!-- Main Content -->
        <div class="relative z-10">
            <!-- Value Display -->
            <div class="mb-2">
                <div class="text-3xl font-bold text-gray-800 group-hover:text-white transition-colors duration-500"
                     @if(isset($card['value'])) 
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
                     @else
                         x-data="{ value: '{{ $card['staticValue'] ?? '0' }}' }"
                         x-text="value"
                     @endif>
                    {{ $card['staticValue'] ?? '0' }}
                </div>
                
                <!-- Value Unit/Suffix -->
                @if(isset($card['suffix']) || isset($card['prefix']))
                <div class="text-sm text-gray-500 group-hover:text-white/80 transition-colors duration-500">
                    {{ $card['prefix'] ?? '' }}{{ $card['suffix'] ?? '' }}
                </div>
                @endif
            </div>
            
            <!-- Label -->
            <h3 class="text-lg font-semibold text-gray-700 group-hover:text-white transition-colors duration-500 mb-1">
                {{ $card['label'] }}
            </h3>
            
            <!-- Subtitle -->
            @if(isset($card['subtitle']))
            <p class="text-sm text-gray-500 group-hover:text-white/80 transition-colors duration-500">
                {{ $card['subtitle'] }}
            </p>
            @endif
        </div>
        
        <!-- Bottom Section -->
        <div class="absolute bottom-3 left-4 right-4 flex justify-between items-end">
            <!-- Action Button -->
            @if($card['clickable'] ?? false)
            <button class="text-sm text-{{ $card['buttonColor'] ?? 'blue' }}-600 group-hover:text-white transition-colors duration-500 flex items-center gap-1">
                <span class="relative before:h-0.5 before:absolute before:w-full before:bg-{{ $card['buttonColor'] ?? 'blue' }}-600 group-hover:before:bg-white before:bottom-0 before:left-0 before:transition-colors before:duration-300">
                    {{ $card['buttonText'] ?? 'View Details' }}
                </span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
            @endif
            
            <!-- Trend Indicator -->
            @if(isset($card['trend']))
            <div class="flex items-center gap-1">
                @if($card['trend'] === 'up')
                    <i class="fas fa-arrow-up text-green-500 group-hover:text-white text-sm"></i>
                @elseif($card['trend'] === 'down')
                    <i class="fas fa-arrow-down text-red-500 group-hover:text-white text-sm"></i>
                @else
                    <i class="fas fa-minus text-yellow-500 group-hover:text-white text-sm"></i>
                @endif
                
                @if(isset($card['trendValue']))
                <span class="text-xs text-gray-600 group-hover:text-white/80 transition-colors duration-500">
                    {{ $card['trendValue'] }}
                </span>
                @endif
            </div>
            @endif
        </div>
        
        <!-- Progress Bar (if provided) -->
        @if(isset($card['progress']))
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-100 group-hover:bg-white/20 transition-colors duration-500">
            <div class="h-full {{ $card['progressColor'] ?? 'bg-blue-500' }} group-hover:bg-white transition-all duration-700 transform origin-left"
                 style="width: {{ $card['progress'] }}%"></div>
        </div>
        @endif
    </div>
    @endforeach
</div>