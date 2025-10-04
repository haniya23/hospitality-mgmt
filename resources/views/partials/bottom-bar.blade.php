<!-- Bottom Navigation Bar -->
@php
    $user = auth()->user();
    $canAccessAdvanced = ($user->subscription_status === 'trial' && $user->trial_plan === 'professional') || in_array($user->subscription_status, ['starter', 'professional']);
@endphp

<div class="fixed bottom-0 left-0 right-0 z-20 lg:hidden" x-data="{ showMoreMenu: false }">
    <!-- Floating Navigation Container -->
    <div class="mx-4 mb-4 lg:mx-8 lg:mb-8">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-gray-200/50 px-2 py-3 lg:px-4 lg:py-4">
            <div class="flex items-center justify-around lg:justify-center lg:gap-6">
                <!-- Dashboard -->
                <div class="group relative">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                        <i class="fas fa-home text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs lg:text-sm font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Properties -->
                <div class="group relative">
                    <a href="{{ route('properties.index') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('properties.*') ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                        <i class="fas fa-building text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs lg:text-sm font-medium">Properties</span>
                    </a>
                </div>

                <!-- More Menu (Centered - 3rd position) -->
                <div class="group relative">
                    <button @click="showMoreMenu = !showMoreMenu" class="flex flex-col items-center justify-center p-2 lg:p-3 rounded-2xl transition-all duration-200 text-gray-600 hover:text-green-600 hover:bg-green-50">
                        <i class="fas fa-bars text-lg lg:text-xl mb-1"></i>
                        <span class="text-xs font-medium">More</span>
                    </button>

                    <!-- Drop-up Menu -->
                    <div x-show="showMoreMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform translate-y-4"
                         @click.away="showMoreMenu = false"
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 sm:w-64 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 py-2 z-50 max-h-80 overflow-y-auto">
                        
                        <!-- Properties Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Properties</h4>
                            <div class="space-y-1">
                                <a href="{{ route('accommodations.index') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('accommodations.*') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-bed w-4 mr-3"></i>
                                    <span class="text-sm">Accommodations</span>
                                </a>
                            </div>
                        </div>

                        <!-- Bookings Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bookings</h4>
                            <div class="space-y-1">
                                <a href="{{ route('bookings.calendar') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('bookings.calendar') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-calendar-alt w-4 mr-3"></i>
                                    <span class="text-sm">Calendar</span>
                                </a>
                                <a href="{{ route('bookings.index') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('bookings.index') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-list w-4 mr-3"></i>
                                    <span class="text-sm">All Bookings</span>
                                </a>
                                <a href="{{ route('bookings.cancelled') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('bookings.cancelled') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-times-circle w-4 mr-3"></i>
                                    <span class="text-sm">Cancelled Bookings</span>
                                </a>
                            </div>
                        </div>

                        <!-- Business Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Business</h4>
                            <div class="space-y-1">
                                <a href="{{ route('customers.index') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('customers.*') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-users w-4 mr-3"></i>
                                    <span class="text-sm">Customers</span>
                                </a>
                                <a href="{{ route('pricing.index') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('pricing.*') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-dollar-sign w-4 mr-3"></i>
                                    <span class="text-sm">Pricing</span>
                                </a>
                            </div>
                        </div>


                        <!-- Account Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Account</h4>
                            <div class="space-y-1">
                                <a href="{{ route('subscription.plans') }}" @click="showMoreMenu = false" class="flex items-center px-3 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-lg {{ request()->routeIs('subscription.*') ? 'bg-green-50 text-green-600' : '' }}">
                                    <i class="fas fa-crown w-4 mr-3"></i>
                                    <span class="text-sm">Subscription</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Booking -->
                <div class="group relative">
                    <a href="{{ route('bookings.create') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('bookings.create') ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                        <i class="fas fa-calendar-plus text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs lg:text-sm font-medium">New Booking</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>