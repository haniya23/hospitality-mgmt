@auth
<!-- Top Bar - Always visible for authenticated users -->
<div class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <!-- Mobile Top Bar -->
    <div class="lg:hidden">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
            <i class="fas fa-bars text-gray-600"></i>
        </button>
        
        <!-- Logo and Greeting -->
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                <span class="text-white text-sm font-bold">S</span>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold text-gray-900">Stay loops</span>
                <span class="text-xs text-gray-500 hidden sm:block">Hi, {{ auth()->user()->name ?? 'Manager' }} 👋</span>
            </div>
        </div>
        
        <!-- User Info and Sign Out -->
        <div class="flex items-center space-x-3">
            <!-- User Name -->
            <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Manager' }}</div>
                <div class="text-xs text-gray-500">
                    @if(auth()->user()->subscription_status === 'trial')
                        Trial - {{ auth()->user()->remaining_trial_days }} days left
                    @elseif(in_array(auth()->user()->subscription_status, ['starter', 'professional']))
                        {{ ucfirst(auth()->user()->subscription_status) }} Plan
                    @else
                        Free Plan
                    @endif
                </div>
            </div>
            
            <!-- User Avatar & Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-colors">
                    {{ substr(auth()->user()->name ?? 'M', 0, 1) }}
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-60">
                    <!-- User Info (Mobile) -->
                    <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Manager' }}</div>
                        <div class="text-xs text-gray-500">
                            @if(auth()->user()->subscription_status === 'trial')
                                Trial - {{ auth()->user()->remaining_trial_days }} days left
                            @elseif(in_array(auth()->user()->subscription_status, ['starter', 'professional']))
                                {{ ucfirst(auth()->user()->subscription_status) }} Plan
                            @else
                                Free Plan
                            @endif
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
    
    <!-- Desktop Top Bar -->
    <div class="hidden lg:block fixed top-0 left-72 right-0 z-50 bg-white border-b border-gray-200 shadow-sm transition-all duration-300" 
         :class="{ '!left-16': sidebarCollapsed }" 
         style="min-height: 64px;">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 h-16">
            <!-- Page Title Area -->
            <div class="flex items-center space-x-4">
                <div>
                    <div class="text-lg font-semibold text-gray-900">
                        @yield('page-title', 'Dashboard')
                    </div>
                    @hasSection('breadcrumbs')
                        <nav class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                            @yield('breadcrumbs')
                        </nav>
                    @endif
                </div>
            </div>
            
            <!-- Right Side - User Info and Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <x-notification-center />
                
                <!-- User Info -->
                <div class="flex items-center space-x-3">
                    <!-- User Name and Plan -->
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Manager' }}</div>
                        <div class="text-xs text-gray-500">
                            @if(auth()->user()->subscription_status === 'trial')
                                Trial - {{ auth()->user()->remaining_trial_days }} days left
                            @elseif(in_array(auth()->user()->subscription_status, ['starter', 'professional']))
                                {{ ucfirst(auth()->user()->subscription_status) }} Plan
                            @else
                                Free Plan
                            @endif
                        </div>
                    </div>
                    
                    <!-- User Avatar & Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center text-white font-semibold hover:from-green-600 hover:to-emerald-600 transition-colors">
                            {{ substr(auth()->user()->name ?? 'M', 0, 1) }}
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-60">
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
    </div>
</div>

@endauth
