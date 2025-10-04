@auth
{{-- 
    Professional Top Bar Component
    - Future-proof responsive design
    - Optimized for performance and accessibility
    - Scroll-friendly implementation
    - Mobile-first approach
--}}

@php
    $user = auth()->user();
    $userName = $user->name ?? 'Manager';
    $userInitial = substr($userName, 0, 1);
    
    // User role logic
    $roleInfo = match($user->user_type) {
        'admin' => [
            'label' => 'Administrator',
            'icon' => 'fas fa-shield-alt',
            'color' => 'text-red-600',
            'bgColor' => 'bg-red-50'
        ],
        'owner' => [
            'label' => 'Property Owner',
            'icon' => 'fas fa-crown',
            'color' => 'text-purple-600',
            'bgColor' => 'bg-purple-50'
        ],
        'staff' => [
            'label' => 'Staff Member',
            'icon' => 'fas fa-user-tie',
            'color' => 'text-blue-600',
            'bgColor' => 'bg-blue-50'
        ],
        default => [
            'label' => 'User',
            'icon' => 'fas fa-user',
            'color' => 'text-gray-600',
            'bgColor' => 'bg-gray-50'
        ]
    };
    
    // Subscription status logic (cached for performance)
    $subscriptionInfo = match($user->subscription_status) {
        'trial' => [
            'label' => 'Trial',
            'detail' => $user->remaining_trial_days . ' days left',
            'color' => 'text-blue-600'
        ],
        'starter', 'professional' => [
            'label' => ucfirst($user->subscription_status) . ' Plan',
            'detail' => 'Active',
            'color' => 'text-green-600'
        ],
        default => [
            'label' => 'Free Plan',
            'detail' => 'Limited access',
            'color' => 'text-gray-600'
        ]
    };
@endphp

{{-- Top Bar Container - Professional Structure --}}
<header class="top-bar" role="banner">
    {{-- Mobile Navigation Bar --}}
    <nav class="top-bar__mobile" aria-label="Mobile navigation">
        <div class="top-bar__mobile-content">
            {{-- Mobile Menu Toggle --}}
            <button 
                @click="sidebarOpen = !sidebarOpen"
                class="top-bar__menu-btn"
                aria-label="Toggle navigation menu"
                aria-expanded="false"
                :aria-expanded="sidebarOpen.toString()"
            >
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            
            {{-- Brand Logo --}}
            <div class="top-bar__brand">
                <div class="top-bar__logo">
                    <span class="sr-only">Stay loops</span>
                    <span aria-hidden="true">S</span>
                </div>
                <div class="top-bar__brand-text">
                    <h1 class="top-bar__title">Stay loops</h1>
                    <p class="top-bar__greeting">Hi, {{ $userName }} 👋</p>
                </div>
            </div>
            
            {{-- User Actions --}}
            <div class="top-bar__actions">
                {{-- User Info (Hidden on small screens) --}}
                <div class="top-bar__user-info">
                    <div class="top-bar__user-name">{{ $userName }}</div>
                    <div class="flex items-center gap-2">
                        <div class="top-bar__user-plan {{ $subscriptionInfo['color'] }}">
                            {{ $subscriptionInfo['label'] }}
                        </div>
                        <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $roleInfo['bgColor'] }}">
                            <i class="{{ $roleInfo['icon'] }} {{ $roleInfo['color'] }} text-xs"></i>
                            <span class="text-xs font-medium {{ $roleInfo['color'] }}">{{ $roleInfo['label'] }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- User Menu Dropdown --}}
                <div class="top-bar__dropdown" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="top-bar__avatar"
                        aria-label="User menu"
                        aria-expanded="false"
                        :aria-expanded="open.toString()"
                    >
                        {{ $userInitial }}
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="top-bar__menu"
                        role="menu"
                        aria-orientation="vertical"
                    >
                        {{-- Mobile User Info --}}
                        <div class="top-bar__menu-header">
                            <div class="top-bar__menu-user">{{ $userName }}</div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="top-bar__menu-plan {{ $subscriptionInfo['color'] }}">
                                    {{ $subscriptionInfo['label'] }} • {{ $subscriptionInfo['detail'] }}
                                </div>
                            </div>
                            <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $roleInfo['bgColor'] }} w-fit">
                                <i class="{{ $roleInfo['icon'] }} {{ $roleInfo['color'] }} text-xs"></i>
                                <span class="text-xs font-medium {{ $roleInfo['color'] }}">{{ $roleInfo['label'] }}</span>
                            </div>
                        </div>
                        
                        {{-- Menu Items --}}
                        <a href="{{ route('subscription.plans') }}" class="top-bar__menu-item" role="menuitem">
                            <i class="fas fa-crown" aria-hidden="true"></i>
                            <span>Subscription</span>
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="top-bar__menu-form">
                            @csrf
                            <button type="submit" class="top-bar__menu-item top-bar__menu-item--danger" role="menuitem">
                                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    {{-- Desktop Navigation Bar --}}
    <nav class="top-bar__desktop" aria-label="Desktop navigation">
        <div class="top-bar__desktop-content">
            {{-- Page Title Section --}}
            <div class="top-bar__page-info">
                <h1 class="top-bar__page-title">
                    @yield('page-title', 'Dashboard')
                </h1>
                @hasSection('breadcrumbs')
                    <nav class="top-bar__breadcrumbs" aria-label="Breadcrumb">
                        @yield('breadcrumbs')
                    </nav>
                @endif
            </div>
            
            {{-- Desktop Actions --}}
            <div class="top-bar__desktop-actions">
                {{-- Quick Navigation Buttons --}}
                @if($user->user_type === 'owner')
                <div class="flex items-center space-x-2 mr-4">
                    <a href="{{ route('owner.staff.index') }}" 
                       class="top-bar__nav-btn {{ request()->routeIs('owner.staff*') ? 'top-bar__nav-btn--active' : '' }}"
                       title="Staff Management">
                        <i class="fas fa-users"></i>
                        <span class="hidden lg:inline">Staff</span>
                    </a>
                    <a href="{{ route('owner.attendance.index') }}" 
                       class="top-bar__nav-btn {{ request()->routeIs('owner.attendance*') ? 'top-bar__nav-btn--active' : '' }}"
                       title="Attendance">
                        <i class="fas fa-calendar-check"></i>
                        <span class="hidden lg:inline">Attendance</span>
                    </a>
                    <a href="{{ route('owner.leave-requests.index') }}" 
                       class="top-bar__nav-btn {{ request()->routeIs('owner.leave-requests*') ? 'top-bar__nav-btn--active' : '' }}"
                       title="Leave Requests">
                        <i class="fas fa-calendar-times"></i>
                        <span class="hidden lg:inline">Leave</span>
                    </a>
                </div>
                @endif
                
                {{-- Notifications --}}
                <x-notification-center />
                
                {{-- User Section --}}
                <div class="top-bar__user-section">
                    <div class="top-bar__user-details">
                        <div class="top-bar__user-name">{{ $userName }}</div>
                        <div class="flex items-center gap-2">
                            <div class="top-bar__user-status {{ $subscriptionInfo['color'] }}">
                                {{ $subscriptionInfo['label'] }}
                            </div>
                            <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $roleInfo['bgColor'] }}">
                                <i class="{{ $roleInfo['icon'] }} {{ $roleInfo['color'] }} text-xs"></i>
                                <span class="text-xs font-medium {{ $roleInfo['color'] }}">{{ $roleInfo['label'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- User Dropdown --}}
                    <div class="top-bar__user-dropdown" x-data="{ open: false }">
                        <button 
                            @click="open = !open"
                            class="top-bar__user-avatar"
                            aria-label="User menu"
                            aria-expanded="false"
                            :aria-expanded="open.toString()"
                        >
                            {{ $userInitial }}
                        </button>
                        
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="top-bar__user-menu"
                            role="menu"
                        >
                            <a href="{{ route('subscription.plans') }}" class="top-bar__user-menu-item" role="menuitem">
                                <i class="fas fa-crown" aria-hidden="true"></i>
                                <span>Subscription</span>
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="top-bar__user-menu-item top-bar__user-menu-item--danger" role="menuitem">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

@endauth
