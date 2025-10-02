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
                    <div class="top-bar__user-plan {{ $subscriptionInfo['color'] }}">
                        {{ $subscriptionInfo['label'] }}
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
                            <div class="top-bar__menu-plan {{ $subscriptionInfo['color'] }}">
                                {{ $subscriptionInfo['label'] }} • {{ $subscriptionInfo['detail'] }}
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
                {{-- Notifications --}}
                <x-notification-center />
                
                {{-- User Section --}}
                <div class="top-bar__user-section">
                    <div class="top-bar__user-details">
                        <div class="top-bar__user-name">{{ $userName }}</div>
                        <div class="top-bar__user-status {{ $subscriptionInfo['color'] }}">
                            {{ $subscriptionInfo['label'] }}
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
