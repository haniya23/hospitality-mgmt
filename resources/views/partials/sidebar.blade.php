<div>
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:bg-white lg:shadow-md lg:shadow-green-200/50 lg:flex lg:flex-col lg:overflow-y-auto transition-all duration-300" 
         :class="sidebarCollapsed ? 'lg:w-16 lg:p-2' : 'lg:w-72 lg:p-5'">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3" :class="{ 'justify-center': sidebarCollapsed }">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                    <span class="text-white text-lg font-bold">S</span>
                </div>
                <span class="text-xl font-bold text-gray-900 transition-opacity duration-300" :class="{ 'opacity-0 w-0 overflow-hidden': sidebarCollapsed }">Stay loops</span>
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="fas text-gray-500" :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
            </button>
        </div>

        @php
            $user = auth()->user();
            $canAccessAdvanced = ($user->subscription_status === 'trial' && $user->trial_plan === 'professional') || in_array($user->subscription_status, ['starter', 'professional']);
        @endphp

        <div class="flex-1 overflow-y-auto">
            <!-- Dashboard Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4 transition-opacity duration-300" :class="{ 'opacity-0': sidebarCollapsed }">Main</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('dashboard') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all" :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <i class="fas fa-home w-5"></i>
                        <span class="transition-opacity duration-300" :class="{ 'opacity-0 w-0 overflow-hidden': sidebarCollapsed }">Dashboard</span>
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

            <!-- Analytics Section -->
            @if($canAccessAdvanced)
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-4">Analytics</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('reports.analytics') }}" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                        <i class="fas fa-chart-bar w-5"></i>Reports
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
        
        
        <!-- Plan Details -->
        <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Current Plan</span>
                @if(auth()->user()->isOnTrial())
                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Trial</span>
                @else
                    <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Active</span>
                @endif
            </div>
            <div class="text-lg font-bold text-gray-900 mb-1">{{ auth()->user()->getPlanName() }}</div>
            <div class="text-sm text-gray-600 mb-3">
                @if(auth()->user()->isOnTrial())
                    {{ auth()->user()->remaining_trial_days }} days left
                @else
                    {{ auth()->user()->properties_limit }} {{ Str::plural('property', auth()->user()->properties_limit) }}
                @endif
            </div>
            @if(auth()->user()->isOnTrial() || auth()->user()->subscription_status === 'starter')
                <a href="{{ route('subscription.plans') }}" class="block w-full text-center bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-medium py-2 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all">
                    Upgrade Now
                </a>
            @endif
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

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-72 bg-white p-5 shadow-xl lg:hidden" x-data="{ 
            openSections: {
                overview: false,
                properties: false,
                bookings: false,
                customers: false,
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

            <!-- Analytics Section -->
            @if($canAccessAdvanced)
            <div class="space-y-1">
                <button @click="openSections.analytics = !openSections.analytics" class="w-full flex items-center justify-between p-3 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-chart-bar w-5 text-gray-500"></i>
                        <span>Analytics</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 text-gray-400 transition-transform" :class="openSections.analytics ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openSections.analytics" x-transition class="ml-8 space-y-1">
                    <a href="{{ route('reports.analytics') }}" @click="sidebarOpen = false" class="block p-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded transition-colors {{ request()->routeIs('reports.*') ? 'text-green-600 bg-green-50' : '' }}">
                        Reports & Analytics
                    </a>
                </div>
            </div>
            @endif

            <!-- Subscription -->
            <a href="{{ route('subscription.plans') }}" @click="sidebarOpen = false" class="flex gap-4 p-3 font-semibold rounded-lg hover:bg-green-100 {{ request()->routeIs('subscription.*') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-crown w-5"></i>Subscription
            </a>
        </div>
        
        
        <!-- Plan Details -->
        <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Current Plan</span>
                @if(auth()->user()->isOnTrial())
                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Trial</span>
                @else
                    <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Active</span>
                @endif
            </div>
            <div class="text-lg font-bold text-gray-900 mb-1">{{ auth()->user()->getPlanName() }}</div>
            <div class="text-sm text-gray-600 mb-3">
                @if(auth()->user()->isOnTrial())
                    {{ auth()->user()->remaining_trial_days }} days left
                @else
                    {{ auth()->user()->properties_limit }} {{ Str::plural('property', auth()->user()->properties_limit) }}
                @endif
            </div>
            @if(auth()->user()->isOnTrial() || auth()->user()->subscription_status === 'starter')
                <a href="{{ route('subscription.plans') }}" @click="sidebarOpen = false" class="block w-full text-center bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-medium py-2 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all">
                    Upgrade Now
                </a>
            @endif
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
