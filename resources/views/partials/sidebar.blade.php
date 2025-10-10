<div>
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 lg:hidden" style="z-index: 99998 !important;" @click="sidebarOpen = false"></div>

    <!-- Desktop Sidebar -->
    <div class="sidebar-desktop hidden lg:fixed lg:top-16 lg:bottom-0 lg:left-0 lg:z-40 lg:bg-white lg:shadow-md lg:shadow-green-200/50 lg:flex lg:flex-col lg:overflow-y-auto transition-all duration-300" 
         :class="sidebarCollapsed ? 'sidebar-collapsed' : 'sidebar-expanded'"
         x-data="{ collapsed: false }"
         x-init="$watch('sidebarCollapsed', value => collapsed = value)">
        
        <!-- When collapsed, show only the expand button and plan indicator -->
        <template x-if="sidebarCollapsed">
            <div class="flex flex-col items-center justify-center h-full py-4 space-y-4">
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors" title="Expand sidebar">
                    <i class="fas fa-chevron-right text-gray-500"></i>
                </button>
                
                <!-- Circular Plan Indicator -->
                @php
                    $user = auth()->user();
                    $remainingDays = 0;
                    $planName = '';
                    $strokeColor = '#10b981'; // green-500
                    $bgColor = '#10b981'; // green-500
                    
                    if ($user->subscription_status === 'trial') {
                        $totalDays = 15; // Trial period
                        $remainingDays = $user->remaining_trial_days;
                        $planName = 'Trial';
                        $strokeColor = '#3b82f6'; // blue-500
                        $bgColor = '#3b82f6'; // blue-500
                    } elseif (in_array($user->subscription_status, ['starter', 'professional'])) {
                        $remainingDays = $user->remaining_subscription_days;
                        $planName = ucfirst($user->subscription_status);
                        // Dynamic billing cycle: 30 days for monthly, 365 days for yearly
                        $totalDays = ($user->billing_cycle === 'yearly') ? 365 : 30;
                        if ($user->subscription_status === 'professional') {
                            $strokeColor = '#f59e0b'; // amber-500
                            $bgColor = '#f59e0b'; // amber-500
                        }
                    } else {
                        $totalDays = 30; // Default fallback
                    }
                    
                    // Battery shows remaining percentage based on actual billing cycle
                    $percentage = $totalDays > 0 ? min(100, max(0, ($remainingDays / $totalDays) * 100)) : 0;
                @endphp
                
                <div class="relative w-12 h-12" 
                     x-data="{ 
                         percentage: {{ $percentage }}, 
                         animatedPercentage: 0,
                         init() {
                             setTimeout(() => {
                                 this.animatedPercentage = this.percentage;
                             }, 100);
                         }
                     }" 
                     title="{{ $planName }} - {{ $remainingDays }} days left">
                    <!-- Background Circle -->
                    <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                        <path stroke="#e5e7eb" stroke-width="3" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                        <!-- Progress Circle -->
                        <path stroke="{{ $strokeColor }}" 
                              stroke-width="3" 
                              fill="transparent" 
                              stroke-linecap="round"
                              class="transition-all duration-2000 ease-out"
                              :stroke-dasharray="animatedPercentage + ' 100'"
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                        </path>
                    </svg>
                    
                    <!-- Center Content -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-xs font-bold text-gray-700">{{ $remainingDays }}</div>
                            <div class="text-xs text-gray-500 leading-none">days</div>
                        </div>
                    </div>
                    
                    <!-- Plan Badge -->
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center" style="background-color: {{ $bgColor }}">
                        @if($user->subscription_status === 'trial')
                            <i class="fas fa-gift text-white text-xs"></i>
                        @elseif($user->subscription_status === 'professional')
                            <i class="fas fa-crown text-white text-xs"></i>
                        @else
                            <i class="fas fa-star text-white text-xs"></i>
                        @endif
                    </div>
                </div>
            </div>
        </template>

        <!-- When expanded, show full sidebar content -->
        <template x-if="!sidebarCollapsed">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-lg font-bold">S</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Stay loops</span>
                    </div>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors" title="Collapse sidebar">
                        <i class="fas fa-chevron-left text-gray-500"></i>
                    </button>
                </div>

        @php
            $user = auth()->user();
            $canAccessAdvanced = ($user->subscription_status === 'trial' && $user->trial_plan === 'professional') || in_array($user->subscription_status, ['starter', 'professional']);
        @endphp

        <div class="flex-1 overflow-y-auto">
                <!-- Dashboard Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Main</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('dashboard') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a></li>
                    </ul>
                </div>

                <!-- Bookings Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Bookings</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('booking.dashboard') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('booking.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-calendar-plus w-5"></i>New Booking
                        </a></li>
                        <li><a href="{{ route('bookings.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('bookings.index') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-calendar w-5"></i>All Bookings
                        </a></li>
                        <li><a href="{{ route('bookings.calendar') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('bookings.calendar') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-calendar-alt w-5"></i>Calendar View
                        </a></li>
                        <li><a href="{{ route('bookings.cancelled') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('bookings.cancelled') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-times-circle w-5"></i>Cancelled
                        </a></li>
                    </ul>
                </div>

                <!-- Check-in/Check-out Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Guest Services</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('checkin.confirmed-bookings') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('checkin.confirmed-bookings') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-calendar-check w-5"></i>Ready for Check-in
                        </a></li>
                        <li><a href="{{ route('checkin.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('checkin.index') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-sign-in-alt w-5"></i>Check-in Records
                        </a></li>
                        <li><a href="{{ route('checkout.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('checkout.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-sign-out-alt w-5"></i>Check-out Records
                        </a></li>
                    </ul>
                </div>

                <!-- Properties Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Properties</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('properties.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('properties.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-building w-5"></i>Properties
                        </a></li>
                        <li><a href="{{ route('accommodations.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('accommodations.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-bed w-5"></i>Accommodations
                        </a></li>
                    </ul>
                </div>

                <!-- Business Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Business</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('customers.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-users w-5"></i>Customers
                        </a></li>
                        <li><a href="{{ route('pricing.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('pricing.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-dollar-sign w-5"></i>Pricing
                        </a></li>
                        @if($canAccessAdvanced)
                        <li><a href="{{ route('b2b.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('b2b.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-handshake w-5"></i>B2B Partners
                        </a></li>
                        @endif
                    </ul>
                </div>

                <!-- Staff Management Section -->
                @if(auth()->user()->isOwner())
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Staff Management</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('owner.staff.index') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('owner.staff.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-users-cog w-5"></i>Manage Staff
                        </a></li>
                        <li><a href="{{ route('owner.staff.create') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('owner.staff.create') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-user-plus w-5"></i>Add Staff
                        </a></li>
                        <li><a href="{{ route('manager.analytics') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('manager.analytics') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-chart-line w-5"></i>Staff Analytics
                        </a></li>
                    </ul>
                </div>
                @endif

                <!-- Account Section -->
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Account</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('subscription.plans') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('subscription.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                            <i class="fas fa-crown w-5"></i>Subscription
                        </a></li>
                    </ul>
                </div>
                </div>
                
                <!-- Plan Details with Battery Indicator -->
                @php
                    $user = auth()->user();
                    $remainingDays = 0;
                    $planName = '';
                    $strokeColor = '#10b981'; // green-500
                    $bgColor = '#10b981'; // green-500
                    $bgGradient = 'from-green-50 to-emerald-50';
                    $borderColor = 'border-green-200';
                    $badgeColor = 'bg-green-100 text-green-600';
                    
                    if ($user->subscription_status === 'trial') {
                        $totalDays = 15; // Trial period
                        $remainingDays = $user->remaining_trial_days;
                        $planName = 'Trial - Professional';
                        $strokeColor = '#3b82f6'; // blue-500
                        $bgColor = '#3b82f6'; // blue-500
                        $bgGradient = 'from-blue-50 to-indigo-50';
                        $borderColor = 'border-blue-200';
                        $badgeColor = 'bg-blue-100 text-blue-600';
                    } elseif (in_array($user->subscription_status, ['starter', 'professional'])) {
                        $remainingDays = $user->remaining_subscription_days;
                        $billingCycle = $user->billing_cycle ?? 'monthly';
                        $planName = ucfirst($user->subscription_status) . ' Plan (' . ucfirst($billingCycle) . ')';
                        // Dynamic billing cycle: 30 days for monthly, 365 days for yearly
                        $totalDays = ($billingCycle === 'yearly') ? 365 : 30;
                        if ($user->subscription_status === 'professional') {
                            $strokeColor = '#f59e0b'; // amber-500
                            $bgColor = '#f59e0b'; // amber-500
                            $bgGradient = 'from-amber-50 to-yellow-50';
                            $borderColor = 'border-amber-200';
                            $badgeColor = 'bg-amber-100 text-amber-600';
                        }
                    } else {
                        $totalDays = 30; // Default fallback
                    }
                    
                    // Battery shows remaining percentage based on actual billing cycle
                    $percentage = $totalDays > 0 ? min(100, max(0, ($remainingDays / $totalDays) * 100)) : 0;
                @endphp
                
                <div class="mt-4 p-4 bg-gradient-to-r {{ $bgGradient }} rounded-xl border {{ $borderColor }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-600">Current Plan</span>
                            <!-- Battery Indicator -->
                            <div class="relative w-8 h-8" 
                                 x-data="{ 
                                     percentage: {{ $percentage }}, 
                                     animatedPercentage: 0,
                                     init() {
                                         setTimeout(() => {
                                             this.animatedPercentage = this.percentage;
                                         }, 300);
                                     }
                                 }" 
                                 title="{{ $remainingDays }} days remaining">
                                <!-- Background Circle -->
                                <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
                                    <path stroke="#e5e7eb" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <!-- Progress Circle -->
                                    <path stroke="{{ $strokeColor }}" 
                                          stroke-width="4" 
                                          fill="transparent" 
                                          stroke-linecap="round"
                                          class="transition-all duration-2000 ease-out"
                                          :stroke-dasharray="animatedPercentage + ' 100'"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                    </path>
                                </svg>
                                
                                <!-- Center Percentage -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs font-bold text-gray-700" x-text="Math.round(animatedPercentage) + '%'"></span>
                                </div>
                            </div>
                        </div>
                        
                        @if(auth()->user()->subscription_status === 'trial')
                            <span class="text-xs {{ $badgeColor }} px-2 py-1 rounded-full">Trial</span>
                        @else
                            <span class="text-xs {{ $badgeColor }} px-2 py-1 rounded-full">Active</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-lg font-bold text-gray-900">
                            {{ $planName }}
                        </div>
                        
                        <!-- Plan Icon -->
                        <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background-color: {{ $bgColor }}">
                            @if($user->subscription_status === 'trial')
                                <i class="fas fa-gift text-white text-xs"></i>
                            @elseif($user->subscription_status === 'professional')
                                <i class="fas fa-crown text-white text-xs"></i>
                            @else
                                <i class="fas fa-star text-white text-xs"></i>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div>
                            @if(auth()->user()->subscription_status === 'trial')
                                {{ $remainingDays }} days left
                            @elseif(auth()->user()->subscription_ends_at)
                                {{ $remainingDays }} days remaining
                            @else
                                {{ auth()->user()->properties_limit }} {{ Str::plural('property', auth()->user()->properties_limit) }}
                            @endif
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-2000 ease-out rounded-full" 
                                     style="background-color: {{ $strokeColor }}; width: {{ $percentage }}%"
                                     x-data="{ width: 0 }"
                                     x-init="setTimeout(() => { width = {{ $percentage }} }, 500)"
                                     :style="'width: ' + width + '%'">
                                </div>
                            </div>
                            <span class="text-xs font-medium text-gray-500">{{ round($percentage) }}%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <div class="mt-auto pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-red-50 text-red-600 transition-all w-full text-left">
                            <i class="fas fa-sign-out-alt w-5"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-72 bg-white p-5 shadow-xl lg:hidden" style="z-index: 99998 !important;"         x-data="{ 
            openSections: {
                overview: false,
                properties: false,
                bookings: false,
                guestServices: false,
                customers: false,
                staff: false,
                business: false,
                analytics: false
            }
        }">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <span class="text-white text-lg font-bold">S</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Stay loops</span>
            </div>
            <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times w-5 h-5 text-gray-500"></i>
            </button>
        </div>

        <div class="space-y-2 overflow-y-auto flex-1">
            <!-- Overview Section -->
            <div class="space-y-1">
                <button @click="openSections.overview = !openSections.overview" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-home w-5 text-gray-500"></i>
                        <span>Overview</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.overview ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.overview" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('dashboard') ? 'text-green-600 bg-green-50' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('booking.dashboard') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('booking.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Booking Dashboard
                    </a>
                </div>
            </div>

            <!-- Properties Section -->
            <div class="space-y-1">
                <button @click="openSections.properties = !openSections.properties" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-building w-5 text-gray-500"></i>
                        <span>Properties</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.properties ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.properties" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('properties.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('properties.*') ? 'text-green-600 bg-green-50' : '' }}">
                        All Properties
                    </a>
                    <a href="{{ route('accommodations.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('accommodations.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Accommodations
                    </a>
                </div>
            </div>

            <!-- Bookings Section -->
            <div class="space-y-1">
                <button @click="openSections.bookings = !openSections.bookings" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-calendar w-5 text-gray-500"></i>
                        <span>Bookings</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.bookings ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.bookings" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('bookings.index') ? 'text-green-600 bg-green-50' : '' }}">
                        All Bookings
                    </a>
                    <a href="{{ route('bookings.cancelled') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('bookings.cancelled') ? 'text-green-600 bg-green-50' : '' }}">
                        Cancelled Bookings
                    </a>
                </div>
            </div>

            <!-- Guest Services Section -->
            <div class="space-y-1">
                <button @click="openSections.guestServices = !openSections.guestServices" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-concierge-bell w-5 text-gray-500"></i>
                        <span>Guest Services</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.guestServices ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.guestServices" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('checkin.confirmed-bookings') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('checkin.confirmed-bookings') ? 'text-green-600 bg-green-50' : '' }}">
                        Ready for Check-in
                    </a>
                    <a href="{{ route('checkin.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('checkin.index') ? 'text-green-600 bg-green-50' : '' }}">
                        Check-in Records
                    </a>
                    <a href="{{ route('checkout.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('checkout.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Check-out Records
                    </a>
                </div>
            </div>

            <!-- Customers Section -->
            <div class="space-y-1">
                <button @click="openSections.customers = !openSections.customers" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users w-5 text-gray-500"></i>
                        <span>Customers</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.customers ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.customers" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('customers.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('customers.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Customer Management
                    </a>
                </div>
            </div>

            <!-- Staff Management Section -->
            @if(auth()->user()->isOwner())
            <div class="space-y-1">
                <button @click="openSections.staff = !openSections.staff" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users-cog w-5 text-gray-500"></i>
                        <span>Staff Management</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.staff ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.staff" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('owner.staff.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('owner.staff.index') ? 'text-green-600 bg-green-50' : '' }}">
                        Manage Staff
                    </a>
                    <a href="{{ route('owner.staff.create') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('owner.staff.create') ? 'text-green-600 bg-green-50' : '' }}">
                        Add Staff
                    </a>
                    <a href="{{ route('manager.analytics') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('manager.analytics') ? 'text-green-600 bg-green-50' : '' }}">
                        Staff Analytics
                    </a>
                </div>
            </div>
            @endif

            <!-- Business Section -->
            @if($canAccessAdvanced)
            <div class="space-y-1">
                <button @click="openSections.business = !openSections.business" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-handshake w-5 text-gray-500"></i>
                        <span>Business</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.business ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.business" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('b2b.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('b2b.*') ? 'text-green-600 bg-green-50' : '' }}">
                        B2B Partners
                    </a>
                    <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('pricing.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Pricing Management
                    </a>
                </div>
            </div>
            @else
            <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('pricing.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-dollar-sign w-5"></i>Pricing
            </a>
            @endif


            <!-- Subscription -->
            <a href="{{ route('subscription.plans') }}" @click="sidebarOpen = false" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('subscription.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-crown w-5"></i>Subscription
            </a>
        </div>
        
        
        <!-- Plan Details with Battery Indicator (Mobile) -->
        @php
            $user = auth()->user();
            $remainingDays = 0;
            $planName = '';
            $strokeColor = '#10b981'; // green-500
            $bgColor = '#10b981'; // green-500
            $bgGradient = 'from-green-50 to-emerald-50';
            $borderColor = 'border-green-200';
            $badgeColor = 'bg-green-100 text-green-600';
            
            if ($user->subscription_status === 'trial') {
                $totalDays = 15; // Trial period
                $remainingDays = $user->remaining_trial_days;
                $planName = 'Trial - Professional';
                $strokeColor = '#3b82f6'; // blue-500
                $bgColor = '#3b82f6'; // blue-500
                $bgGradient = 'from-blue-50 to-indigo-50';
                $borderColor = 'border-blue-200';
                $badgeColor = 'bg-blue-100 text-blue-600';
            } elseif (in_array($user->subscription_status, ['starter', 'professional'])) {
                $remainingDays = $user->remaining_subscription_days;
                $billingCycle = $user->billing_cycle ?? 'monthly';
                $planName = ucfirst($user->subscription_status) . ' Plan (' . ucfirst($billingCycle) . ')';
                // Dynamic billing cycle: 30 days for monthly, 365 days for yearly
                $totalDays = ($billingCycle === 'yearly') ? 365 : 30;
                if ($user->subscription_status === 'professional') {
                    $strokeColor = '#f59e0b'; // amber-500
                    $bgColor = '#f59e0b'; // amber-500
                    $bgGradient = 'from-amber-50 to-yellow-50';
                    $borderColor = 'border-amber-200';
                    $badgeColor = 'bg-amber-100 text-amber-600';
                }
            } else {
                $totalDays = 30; // Default fallback
            }
            
            // Battery shows remaining percentage based on actual billing cycle
            $percentage = $totalDays > 0 ? min(100, max(0, ($remainingDays / $totalDays) * 100)) : 0;
        @endphp
        
        <div class="mt-4 p-4 bg-gradient-to-r {{ $bgGradient }} rounded-xl border {{ $borderColor }}">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-600">Current Plan</span>
                    <!-- Battery Indicator -->
                    <div class="relative w-8 h-8" 
                         x-data="{ 
                             percentage: {{ $percentage }}, 
                             animatedPercentage: 0,
                             init() {
                                 setTimeout(() => {
                                     this.animatedPercentage = this.percentage;
                                 }, 400);
                             }
                         }" 
                         title="{{ $remainingDays }} days remaining">
                        <!-- Background Circle -->
                        <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
                            <path stroke="#e5e7eb" stroke-width="4" fill="transparent" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                            <!-- Progress Circle -->
                            <path stroke="{{ $strokeColor }}" 
                                  stroke-width="4" 
                                  fill="transparent" 
                                  stroke-linecap="round"
                                  class="transition-all duration-2000 ease-out"
                                  :stroke-dasharray="animatedPercentage + ' 100'"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                            </path>
                        </svg>
                        
                        <!-- Center Percentage -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-bold text-gray-700" x-text="Math.round(animatedPercentage) + '%'"></span>
                        </div>
                    </div>
                </div>
                
                @if(auth()->user()->subscription_status === 'trial')
                    <span class="text-xs {{ $badgeColor }} px-2 py-1 rounded-full">Trial</span>
                @else
                    <span class="text-xs {{ $badgeColor }} px-2 py-1 rounded-full">Active</span>
                @endif
            </div>
            
            <div class="flex items-center justify-between mb-2">
                <div class="text-lg font-bold text-gray-900">
                    {{ $planName }}
                </div>
                
                <!-- Plan Icon -->
                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background-color: {{ $bgColor }}">
                    @if($user->subscription_status === 'trial')
                        <i class="fas fa-gift text-white text-xs"></i>
                    @elseif($user->subscription_status === 'professional')
                        <i class="fas fa-crown text-white text-xs"></i>
                    @else
                        <i class="fas fa-star text-white text-xs"></i>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    @if(auth()->user()->subscription_status === 'trial')
                        {{ $remainingDays }} days left
                    @elseif(auth()->user()->subscription_ends_at)
                        {{ $remainingDays }} days remaining
                    @else
                        {{ auth()->user()->properties_limit }} {{ Str::plural('property', auth()->user()->properties_limit) }}
                    @endif
                </div>
                
                <!-- Progress Bar -->
                <div class="flex items-center gap-2">
                    <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-2000 ease-out rounded-full" 
                             style="background-color: {{ $strokeColor }}; width: {{ $percentage }}%"
                             x-data="{ width: 0 }"
                             x-init="setTimeout(() => { width = {{ $percentage }} }, 600)"
                             :style="'width: ' + width + '%'">
                        </div>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ round($percentage) }}%</span>
                </div>
            </div>
        </div>
        
        <ul class="w-full flex flex-col gap-2 mt-4">
            <li>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-green-100 text-gray-700 transition-all w-full text-left">
                        <i class="fas fa-sign-out-alt w-6"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
