@props([
    'show' => false,
    'maxWidth' => '2xl',
    'closable' => true,
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconBg' => 'from-blue-500 to-blue-600'
])

@php
$maxWidthClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
    '6xl' => 'sm:max-w-6xl',
    '7xl' => 'sm:max-w-7xl',
];
@endphp

<div
    x-data="{ 
        show: @entangle($show),
        init() {
            // Watch for changes to show state and control body scroll
            this.$watch('show', value => {
                if (value) {
                    // Lock body scroll when modal opens
                    document.body.style.overflow = 'hidden';
                    // Store original scroll position
                    this.originalScrollY = window.scrollY;
                } else {
                    // Restore body scroll when modal closes
                    document.body.style.overflow = '';
                    // Restore scroll position
                    if (this.originalScrollY !== undefined) {
                        window.scrollTo(0, this.originalScrollY);
                    }
                }
            });
            
            // Clean up on component destroy
            this.$el.addEventListener('alpine:destroyed', () => {
                document.body.style.overflow = '';
            });
        }
    }"
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
            class="relative w-full {{ $maxWidthClasses[$maxWidth] }} mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-h-[95vh] flex flex-col"
            @click.away="show = false"
        >
            
            @if($title || $closable)
            <!-- Header -->
            <div class="flex items-center justify-between p-6 pb-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-t-2xl border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    @if($icon)
                    <div class="w-10 h-10 bg-gradient-to-r {{ $iconBg }} rounded-xl flex items-center justify-center shadow-lg">
                        <i class="{{ $icon }} text-white text-lg"></i>
                    </div>
                    @endif
                    <div>
                        @if($title)
                        <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                        @endif
                        @if($subtitle)
                        <p class="text-sm text-gray-600 font-medium">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                
                @if($closable)
                <button 
                    @click="show = false" 
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/80 rounded-xl transition-all duration-200 shadow-sm"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>
            @endif
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 p-6 pt-4 bg-gray-50 rounded-b-2xl border-t border-gray-200">
                {{ $footer }}
            </div>
            @endif
            
        </div>
    </div>
</div>



