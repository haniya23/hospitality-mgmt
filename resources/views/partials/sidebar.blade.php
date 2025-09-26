<div>
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:w-72 lg:bg-white lg:p-5 lg:shadow-md lg:shadow-purple-200/50 lg:flex lg:flex-col">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <span class="text-white text-lg font-bold">H</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Hospitality</span>
            </div>
        </div>

        @php
            $user = auth()->user();
            $canAccessAdvanced = ($user->subscription_status === 'trial' && $user->trial_plan === 'professional') || in_array($user->subscription_status, ['starter', 'professional']);
        @endphp

        <ul class="w-full flex flex-col gap-2 flex-1">
            <li><a href="{{ route('dashboard') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-home w-6"></i>Dashboard
            </a></li>
            <li><a href="{{ route('properties.index') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('properties.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-building w-6"></i>Properties
            </a></li>
            <li><a href="{{ route('bookings.index') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('bookings.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-calendar w-6"></i>Bookings
            </a></li>
            <li><a href="{{ route('customers.index') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-users w-6"></i>Customers
            </a></li>
            @if($canAccessAdvanced)
            <li><a href="{{ route('b2b.index') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('b2b.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-handshake w-6"></i>B2B Partners
            </a></li>
            @endif

            <li><a href="{{ route('pricing.index') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('pricing.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-dollar-sign w-6"></i>Pricing
            </a></li>
            @if($canAccessAdvanced)
            <li><a href="{{ route('reports.analytics') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-chart-bar w-6"></i>Reports
            </a></li>
            @endif
            <li><a href="{{ route('subscription.plans') }}" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('subscription.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-crown w-6"></i>Subscription
            </a></li>
        </ul>
        
        <!-- Plan Details -->
        <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
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
                <a href="{{ route('subscription.plans') }}" class="block w-full text-center bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-medium py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all">
                    Upgrade Now
                </a>
            @endif
        </div>
        
        <ul class="w-full flex flex-col gap-2 mt-4">
            <li class="mt-auto">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 text-gray-700 transition-all w-full text-left">
                        <i class="fas fa-sign-out-alt w-6"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-72 bg-white p-5 shadow-xl lg:hidden">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                    <span class="text-white text-lg font-bold">H</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Hospitality</span>
            </div>
            <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times w-5 h-5 text-gray-500"></i>
            </button>
        </div>

        <ul class="w-full flex flex-col gap-2">
            <li><a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-home w-6"></i>Dashboard
            </a></li>
            <li><a href="{{ route('properties.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('properties.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-building w-6"></i>Properties
            </a></li>
            <li><a href="{{ route('bookings.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('bookings.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-calendar w-6"></i>Bookings
            </a></li>
            <li><a href="{{ route('customers.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-users w-6"></i>Customers
            </a></li>
            <li><a href="{{ route('b2b.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('b2b.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-handshake w-6"></i>B2B Partners
            </a></li>

            <li><a href="{{ route('pricing.index') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('pricing.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-dollar-sign w-6"></i>Pricing
            </a></li>
            <li><a href="{{ route('reports.analytics') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-chart-bar w-6"></i>Reports
            </a></li>
            <li><a href="{{ route('subscription.plans') }}" @click="sidebarOpen = false" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 {{ request()->routeIs('subscription.*') ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white' : 'text-gray-700' }} transition-all">
                <i class="fas fa-crown w-6"></i>Subscription
            </a></li>
        </ul>
        
        <!-- Plan Details -->
        <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
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
                <a href="{{ route('subscription.plans') }}" @click="sidebarOpen = false" class="block w-full text-center bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-medium py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all">
                    Upgrade Now
                </a>
            @endif
        </div>
        
        <ul class="w-full flex flex-col gap-2 mt-4">
            <li>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex gap-4 p-4 font-semibold rounded-full hover:bg-purple-100 text-gray-700 transition-all w-full text-left">
                        <i class="fas fa-sign-out-alt w-6"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>