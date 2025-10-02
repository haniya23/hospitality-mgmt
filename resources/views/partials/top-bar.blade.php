@auth
@if(auth()->user()->subscription_status === 'trial' && auth()->user()->is_trial_active)
<div class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 lg:hidden">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
            <i class="fas fa-bars text-gray-600"></i>
        </button>
        
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                <span class="text-white text-sm font-bold">S</span>
            </div>
            <span class="text-lg font-bold text-gray-900">Stay loops</span>
        </div>
        
        <!-- User Info and Sign Out -->
        <div class="flex items-center space-x-3">
            <!-- User Name -->
            <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Manager' }}</div>
                <div class="text-xs text-gray-500">
                    Trial - {{ auth()->user()->remaining_trial_days }} days left
                </div>
            </div>
            
            <!-- User Avatar & Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-colors">
                    {{ substr(auth()->user()->name ?? 'M', 0, 1) }}
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <!-- User Info (Mobile) -->
                    <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Manager' }}</div>
                        <div class="text-xs text-gray-500">
                            Trial - {{ auth()->user()->remaining_trial_days }} days left
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <a href="{{ route('subscription.plans') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-crown w-4 mr-2 text-gray-400"></i>
                        Subscription
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-4 mr-2 text-red-400"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trial Banner for Mobile -->
@if(auth()->user()->subscription_status === 'trial' && auth()->user()->is_trial_active)
<div class="fixed top-16 left-0 right-0 z-20 lg:hidden">
    <div class="mx-4 mt-2">
        <a href="{{ route('subscription.plans') }}" class="block w-full rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3 shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="bg-white/20 rounded-full p-1.5 flex-shrink-0">
                        <i class="fas fa-gift text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-semibold leading-tight block">
                            {{ auth()->user()->remaining_trial_days }} days left
                        </span>
                        <span class="text-xs text-blue-100 font-medium block">
                            Professional trial • Upgrade now
                        </span>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-white/70 text-sm"></i>
            </div>
        </a>
    </div>
</div>
@endif

<!-- Subscription Active Banner for Mobile -->
@if(in_array(auth()->user()->subscription_status, ['starter', 'professional']))
<div class="fixed top-16 left-0 right-0 z-20 lg:hidden">
    <div class="mx-4 mt-2">
        <div class="block w-full rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-3 shadow-lg">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="bg-white/20 rounded-full p-1.5 flex-shrink-0">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-semibold leading-tight block">
                            {{ ucfirst(auth()->user()->subscription_status) }} Plan
                        </span>
                        <span class="text-xs text-green-100 font-medium block">
                            @if(auth()->user()->subscription_ends_at)
                                {{ auth()->user()->subscription_ends_at->diffInDays() }} days remaining
                            @else
                                Active subscription
                            @endif
                        </span>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="text-white text-xs font-medium underline hover:no-underline transition-all">
                    Manage
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endauth
