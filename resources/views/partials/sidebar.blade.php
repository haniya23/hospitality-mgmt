<div>
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 lg:hidden"
        style="z-index: 99998 !important;" @click="sidebarOpen = false"></div>

    <!-- Desktop Sidebar -->
    <div class="sidebar-desktop hidden lg:fixed lg:top-16 lg:bottom-0 lg:left-0 lg:z-40 lg:bg-white lg:border-r lg:border-gray-100 lg:flex lg:flex-col lg:overflow-y-auto transition-all duration-300"
        :class="sidebarCollapsed ? 'sidebar-collapsed' : 'sidebar-expanded'" x-data="{ collapsed: false }"
        x-init="$watch('sidebarCollapsed', value => collapsed = value)">

        <!-- When collapsed, show only the expand button -->
        <template x-if="sidebarCollapsed">
            <div class="flex flex-col items-center h-full py-4 space-y-4">
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-50 transition-colors"
                    title="Expand sidebar">
                    <i class="fas fa-chevron-right text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
        </template>

        <!-- When expanded, show full sidebar content -->
        <template x-if="!sidebarCollapsed">
            <div class="flex flex-col h-full p-4 justify-between">
                <div>
                    <!-- Sidebar Title -->
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-tr from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-sm">
                                <span class="text-white text-sm font-bold">S</span>
                            </div>
                            <span class="text-lg font-bold text-gray-800">Stay loops</span>
                        </div>
                        <button @click="sidebarCollapsed = !sidebarCollapsed"
                            class="flex items-center justify-center w-7 h-7 rounded-lg hover:bg-gray-50 transition-colors"
                            title="Collapse sidebar">
                            <i class="fas fa-chevron-left text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>

                    @php
                        $user = auth()->user();
                        $canAccessAdvanced = true;
                        
                        // Active and Inactive classes
                        $act = 'flex items-center gap-3 pl-2 pr-4 py-2 border-l-4 border-emerald-600 bg-emerald-50/50 text-emerald-800 font-bold rounded-r-xl transition-all duration-200 text-sm';
                        $inact = 'flex items-center gap-3 pl-2 pr-4 py-2 border-l-4 border-transparent text-gray-500 hover:text-gray-900 hover:bg-gray-50/80 rounded-r-xl transition-all duration-200 text-sm';
                    @endphp

                    <div class="flex-1 overflow-y-auto space-y-6">
                        @if(auth()->user()->is_admin)
                            <!-- Admin Panel Section -->
                            <div>
                                <h4 class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-2 px-3">Admin Panel</h4>
                                <ul class="space-y-0.5">
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? $act : $inact }}">
                                            <i class="fas fa-chart-line w-5 text-center text-xs"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.user-management') }}" class="{{ request()->routeIs('admin.user-management') || request()->routeIs('admin.users.*') || request()->routeIs('admin.customer-data') ? $act : $inact }}">
                                            <i class="fas fa-users w-5 text-center text-xs"></i>
                                            <span>Users</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.location-analytics') }}" class="{{ request()->routeIs('admin.location-analytics') || request()->routeIs('admin.b2b-management') ? $act : $inact }}">
                                            <i class="fas fa-chart-pie w-5 text-center text-xs"></i>
                                            <span>Analytics</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.property-approvals') }}" class="{{ request()->routeIs('admin.property-approvals') ? $act : $inact }}">
                                            <i class="fas fa-tasks w-5 text-center text-xs"></i>
                                            <span>Actions</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <!-- Main Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Main</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $act : $inact }}">
                                        <i class="fas fa-home w-5 text-center text-xs"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Bookings Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Reservations</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('booking.dashboard') }}" class="{{ request()->routeIs('booking.*') ? $act : $inact }}">
                                        <i class="fas fa-calendar-plus w-5 text-center text-xs"></i>
                                        <span>New Booking</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.index') ? $act : $inact }}">
                                        <i class="fas fa-calendar w-5 text-center text-xs"></i>
                                        <span>All Bookings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bookings.calendar') }}" class="{{ request()->routeIs('bookings.calendar') ? $act : $inact }}">
                                        <i class="fas fa-calendar-alt w-5 text-center text-xs"></i>
                                        <span>Calendar View</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('bookings.cancelled') }}" class="{{ request()->routeIs('bookings.cancelled') ? $act : $inact }}">
                                        <i class="fas fa-times-circle w-5 text-center text-xs"></i>
                                        <span>Cancelled</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Guest Services Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Guest Services</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('checkin.confirmed-bookings') }}" class="{{ request()->routeIs('checkin.confirmed-bookings') ? $act : $inact }}">
                                        <i class="fas fa-calendar-check w-5 text-center text-xs"></i>
                                        <span>Ready for Check-in</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('checkin.index') }}" class="{{ request()->routeIs('checkin.index') ? $act : $inact }}">
                                        <i class="fas fa-sign-in-alt w-5 text-center text-xs"></i>
                                        <span>Check-in Records</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('checkout.index') }}" class="{{ request()->routeIs('checkout.*') ? $act : $inact }}">
                                        <i class="fas fa-sign-out-alt w-5 text-center text-xs"></i>
                                        <span>Check-out Records</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Properties Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Properties</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('properties.index') }}" class="{{ request()->routeIs('properties.*') ? $act : $inact }}">
                                        <i class="fas fa-building w-5 text-center text-xs"></i>
                                        <span>Properties</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? $act : $inact }}">
                                        <i class="fas fa-bed w-5 text-center text-xs"></i>
                                        <span>Accommodations</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Business Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Business</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? $act : $inact }}">
                                        <i class="fas fa-users w-5 text-center text-xs"></i>
                                        <span>Customers</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pricing.index') }}" class="{{ request()->routeIs('pricing.*') ? $act : $inact }}">
                                        <i class="fas fa-dollar-sign w-5 text-center text-xs"></i>
                                        <span>Pricing</span>
                                    </a>
                                </li>
                                @if($canAccessAdvanced)
                                    <li>
                                        <a href="{{ route('b2b.index') }}" class="{{ request()->routeIs('b2b.*') ? $act : $inact }}">
                                            <i class="fas fa-handshake w-5 text-center text-xs"></i>
                                            <span>B2B Partners</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Financial Section -->
                        @if(auth()->user()->isOwner())
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Accounting</h4>
                                <ul class="space-y-0.5">
                                    <li>
                                        <a href="{{ route('owner.financial.dashboard') }}" class="{{ request()->routeIs('owner.financial.*') ? $act : $inact }}">
                                            <i class="fas fa-chart-pie w-5 text-center text-xs"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.booking-finance.index') }}" class="{{ request()->routeIs('owner.booking-finance.*') ? $act : $inact }}">
                                            <i class="fas fa-receipt w-5 text-center text-xs"></i>
                                            <span>Booking Finances</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.income.index') }}" class="{{ request()->routeIs('owner.income.*') ? $act : $inact }}">
                                            <i class="fas fa-arrow-down w-5 text-center text-xs text-green-500"></i>
                                            <span>Income</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.expense.index') }}" class="{{ request()->routeIs('owner.expense.*') ? $act : $inact }}">
                                            <i class="fas fa-arrow-up w-5 text-center text-xs text-red-500"></i>
                                            <span>Expenses</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('owner.reports.weekly') }}" class="{{ request()->routeIs('owner.reports.*') ? $act : $inact }}">
                                            <i class="fas fa-file-invoice-dollar w-5 text-center text-xs"></i>
                                            <span>Reports</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <!-- Support Section -->
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-3">Support</h4>
                            <ul class="space-y-0.5">
                                <li>
                                    <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.index') ? $act : $inact }}">
                                        <i class="fab fa-whatsapp w-5 text-center text-xs"></i>
                                        <span>Contact Us</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Logout Button -->
                <div class="pt-4 border-t border-gray-100 mt-6">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 pl-2 pr-4 py-2 border-l-4 border-transparent text-red-500 hover:text-red-700 hover:bg-red-50 rounded-r-xl transition-all duration-200 w-full text-left text-sm font-semibold">
                            <i class="fas fa-sign-out-alt w-5 text-center text-xs"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-72 bg-white p-5 shadow-xl lg:hidden flex flex-col justify-between"
        style="z-index: 99998 !important;" x-data="{ 
            openSections: {
                overview: false,
                properties: false,
                bookings: false,
                guestServices: false,
                customers: false,
                business: false,
                analytics: false,
                financial: false
            }
        }">
        <div>
            <!-- Mobile Sidebar Header -->
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-tr from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-sm">
                        <span class="text-white text-sm font-bold">S</span>
                    </div>
                    <span class="text-lg font-bold text-gray-800">Stay loops</span>
                </div>
                <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times w-5 h-5 text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>

            <!-- Accordion Links -->
            <div class="space-y-1 overflow-y-auto flex-1 max-h-[calc(100vh-170px)] pr-1">
                @php
                    $mobileAct = 'block p-2 text-sm text-emerald-800 font-bold border-l-2 border-emerald-600 bg-emerald-50/40 rounded-r-lg transition-colors';
                    $mobileInact = 'block p-2 text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50/50 rounded-r-lg transition-colors pl-3';
                @endphp

                @if(auth()->user()->is_admin)
                    <!-- Admin Panel (Mobile) -->
                    <div class="space-y-1 pb-4 mb-4 border-b border-gray-100">
                        <div class="px-2.5 mb-2">
                            <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Admin Panel</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('admin.dashboard') ? $mobileAct : $mobileInact }}">
                            <i class="fas fa-chart-line mr-2 text-xs"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.user-management') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('admin.user-management') || request()->routeIs('admin.users.*') || request()->routeIs('admin.customer-data') ? $mobileAct : $mobileInact }}">
                            <i class="fas fa-users mr-2 text-xs"></i> Users
                        </a>
                        <a href="{{ route('admin.location-analytics') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('admin.location-analytics') || request()->routeIs('admin.b2b-management') ? $mobileAct : $mobileInact }}">
                            <i class="fas fa-chart-pie mr-2 text-xs"></i> Analytics
                        </a>
                        <a href="{{ route('admin.property-approvals') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('admin.property-approvals') ? $mobileAct : $mobileInact }}">
                            <i class="fas fa-tasks mr-2 text-xs"></i> Actions
                        </a>
                    </div>
                @endif

                <!-- Overview Section -->
                <div class="space-y-1">
                    <button @click="openSections.overview = !openSections.overview"
                        class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-home w-5 text-gray-400 text-xs"></i>
                            <span class="text-sm">Overview</span>
                        </div>
                        <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                            :class="openSections.overview ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.overview" x-transition class="ml-8 space-y-1">
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('dashboard') ? $mobileAct : $mobileInact }}">
                            Dashboard
                        </a>
                        <a href="{{ route('booking.dashboard') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('booking.*') ? $mobileAct : $mobileInact }}">
                            Booking Dashboard
                        </a>
                    </div>
                </div>

                <!-- Properties Section -->
                <div class="space-y-1">
                    <button @click="openSections.properties = !openSections.properties"
                        class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-building w-5 text-gray-400 text-xs"></i>
                            <span class="text-sm">Properties</span>
                        </div>
                        <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                            :class="openSections.properties ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.properties" x-transition class="ml-8 space-y-1">
                        <a href="{{ route('properties.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('properties.*') ? $mobileAct : $mobileInact }}">
                            All Properties
                        </a>
                        <a href="{{ route('accommodations.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('accommodations.*') ? $mobileAct : $mobileInact }}">
                            Accommodations
                        </a>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div class="space-y-1">
                    <button @click="openSections.bookings = !openSections.bookings"
                        class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-calendar w-5 text-gray-400 text-xs"></i>
                            <span class="text-sm">Bookings</span>
                        </div>
                        <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                            :class="openSections.bookings ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.bookings" x-transition class="ml-8 space-y-1">
                        <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('bookings.index') ? $mobileAct : $mobileInact }}">
                            All Bookings
                        </a>
                        <a href="{{ route('bookings.cancelled') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('bookings.cancelled') ? $mobileAct : $mobileInact }}">
                            Cancelled Bookings
                        </a>
                    </div>
                </div>

                <!-- Guest Services Section -->
                <div class="space-y-1">
                    <button @click="openSections.guestServices = !openSections.guestServices"
                        class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-concierge-bell w-5 text-gray-400 text-xs"></i>
                            <span class="text-sm">Guest Services</span>
                        </div>
                        <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                            :class="openSections.guestServices ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.guestServices" x-transition class="ml-8 space-y-1">
                        <a href="{{ route('checkin.confirmed-bookings') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('checkin.confirmed-bookings') ? $mobileAct : $mobileInact }}">
                            Ready for Check-in
                        </a>
                        <a href="{{ route('checkin.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('checkin.index') ? $mobileAct : $mobileInact }}">
                            Check-in Records
                        </a>
                        <a href="{{ route('checkout.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('checkout.*') ? $mobileAct : $mobileInact }}">
                            Check-out Records
                        </a>
                    </div>
                </div>

                <!-- Customers Section -->
                <div class="space-y-1">
                    <button @click="openSections.customers = !openSections.customers"
                        class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-users w-5 text-gray-400 text-xs"></i>
                            <span class="text-sm">Customers</span>
                        </div>
                        <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                            :class="openSections.customers ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.customers" x-transition class="ml-8 space-y-1">
                        <a href="{{ route('customers.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('customers.*') ? $mobileAct : $mobileInact }}">
                            Customer Management
                        </a>
                    </div>
                </div>

                <!-- Financial Section (Mobile) -->
                @if(auth()->user()->isOwner())
                    <div class="space-y-1">
                        <button @click="openSections.financial = !openSections.financial"
                            class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-chart-pie w-5 text-gray-400 text-xs"></i>
                                <span class="text-sm">Financial</span>
                            </div>
                            <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                                :class="openSections.financial ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="openSections.financial" x-transition class="ml-8 space-y-1">
                            <a href="{{ route('owner.financial.dashboard') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('owner.financial.*') ? $mobileAct : $mobileInact }}">
                                Dashboard
                            </a>
                            <a href="{{ route('owner.booking-finance.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('owner.booking-finance.*') ? $mobileAct : $mobileInact }}">
                                Booking Finances
                            </a>
                            <a href="{{ route('owner.income.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('owner.income.*') ? $mobileAct : $mobileInact }}">
                                Income
                            </a>
                            <a href="{{ route('owner.expense.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('owner.expense.*') ? $mobileAct : $mobileInact }}">
                                Expenses
                            </a>
                            <a href="{{ route('owner.reports.weekly') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('owner.reports.*') ? $mobileAct : $mobileInact }}">
                                Reports
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Business Section -->
                @if($canAccessAdvanced)
                    <div class="space-y-1">
                        <button @click="openSections.business = !openSections.business"
                            class="w-full flex items-center justify-between p-2.5 text-left font-semibold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-handshake w-5 text-gray-400 text-xs"></i>
                                <span class="text-sm">Business</span>
                            </div>
                            <i class="fas fa-chevron-down w-3 text-gray-400 transition-transform"
                                :class="openSections.business ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="openSections.business" x-transition class="ml-8 space-y-1">
                            <a href="{{ route('b2b.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('b2b.*') ? $mobileAct : $mobileInact }}">
                                B2B Partners
                            </a>
                            <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('pricing.*') ? $mobileAct : $mobileInact }}">
                                Pricing Management
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('pricing.index') }}" @click="sidebarOpen = false"
                        class="flex gap-4 p-2.5 font-semibold rounded-lg hover:bg-gray-50 text-gray-600 transition-all text-sm">
                        <i class="fas fa-dollar-sign w-5 text-center text-xs text-gray-400"></i>
                        <span>Pricing</span>
                    </a>
                @endif

                <!-- Contact Us -->
                <a href="{{ route('contact.index') }}" @click="sidebarOpen = false"
                    class="flex gap-4 p-2.5 font-semibold rounded-lg hover:bg-gray-50 text-gray-600 transition-all text-sm">
                    <i class="fab fa-whatsapp w-5 text-center text-xs text-gray-400"></i>
                    <span>Contact Us</span>
                </a>
            </div>
        </div>

        <!-- Mobile Sidebar Footer / Logout -->
        <div class="pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="flex gap-4 p-3 font-semibold rounded-xl hover:bg-red-50 text-red-600 transition-all w-full text-left text-sm">
                    <i class="fas fa-sign-out-alt w-5 text-center text-xs"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>