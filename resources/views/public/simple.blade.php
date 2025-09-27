<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospitality Manager - Simple Landing</title>
    <link rel="stylesheet" href="{{ asset('css/simple-landing.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Modern Counter Styles -->
    <style>
        .hero-booking-counters {
            display: flex;
            gap: 3rem;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(55, 48, 163, 0.1) 100%);
            border-radius: 2rem;
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(30, 64, 175, 0.2);
        }
        
        .counter-item {
            text-align: center;
            padding: 2rem 1.5rem;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(30, 64, 175, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .counter-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
            border-radius: 2px;
        }
        
        .counter-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.25);
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
        }
        
        .counter-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 700;
        }
        
        .counter-number {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #1e40af 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .counter-subtitle {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
        }
        
        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: #1e40af;
            text-shadow: 0 1px 2px rgba(30, 64, 175, 0.1);
        }
        
        .stat-label {
            display: block;
            font-size: 0.875rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        /* Travel Partners Section */
        .partners-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .section-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 1.125rem;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .partners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .partner-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .partner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
        }
        
        .partner-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .partner-rank {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, #1e40af 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rank-number {
            color: white;
            font-weight: 800;
            font-size: 1.125rem;
        }
        
        .partner-info {
            margin-right: 3rem;
        }
        
        .partner-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .partner-type {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .partner-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .booking-count, .commission-rate {
            background: #f1f5f9;
            color: #475569;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .commission-rate {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .partner-contact {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .partner-contact svg {
            width: 1rem;
            height: 1rem;
        }
        
        .partner-status {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
        }
        
        .no-partners {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        
        .no-partners-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .no-partners h3 {
            font-size: 1.5rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .hero-booking-counters {
                flex-direction: column;
                gap: 1.5rem;
                margin: 1.5rem 0;
                padding: 1rem;
            }
            
            .counter-item {
                width: 100%;
                max-width: 300px;
                padding: 1.5rem 1rem;
            }
            
            .counter-number {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 1rem;
                margin: 1.5rem 0;
                padding: 1rem;
            }
            
            .stat-item {
                min-width: auto;
                width: 100%;
            }
            
            .stat-number {
                font-size: 1.75rem;
            }
            
            .partners-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .partner-card {
                padding: 1rem;
            }
            
            .partner-info {
                margin-right: 2.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero-booking-counters {
                gap: 1rem;
                padding: 0.75rem;
            }
            
            .counter-label {
                font-size: 0.75rem;
            }
            
            .counter-number {
                font-size: 2rem;
            }
            
            .hero-stats {
                padding: 0.75rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .partner-card {
                padding: 0.75rem;
            }
            
            .partner-name {
                font-size: 1.125rem;
            }
        }
        
        /* Mobile Navigation Improvements */
        .nav-container {
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e40af;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .logo:hover {
            color: #3730a3;
        }
        
        .nav-buttons-desktop {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .nav-button-group {
            display: flex;
            gap: 1rem;
        }
        
        .nav-button {
            padding: 0.5rem 1rem;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-button:hover {
            color: #1e40af;
            background: rgba(30, 64, 175, 0.1);
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #475569;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            color: #1e40af;
            background: rgba(30, 64, 175, 0.1);
        }
        
        .mobile-menu-toggle svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .nav-content {
                padding: 1rem;
            }
            
            .nav-buttons-desktop {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .logo {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" :class="{ 'overflow-hidden': sidebarOpen }">
    
    <!-- Top Navigation -->
    <nav class="nav-container">
        <div class="nav-content">
            <a href="#home" class="logo">🏨 Hospitality Manager</a>
            
            <!-- Desktop Navigation Buttons -->
            <div class="nav-buttons-desktop">
                <div class="nav-button-group">
                    <a href="#properties" class="nav-button">Properties</a>
                    <a href="#partners" class="nav-button">Partners</a>
                    <a href="#analytics" class="nav-button">Analytics</a>
                </div>
                
                <div class="auth-dropdown" onclick="toggleDropdown(this)">
                    <button class="dropdown-toggle">
                        Account
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('login') }}" class="dropdown-item">Sign In</a>
                        <a href="{{ route('register') }}" class="dropdown-item">Register</a>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" @click="sidebarOpen = true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </nav>

    <!-- Sidebar (Mobile Menu) -->
    <div class="sidebar" :class="{ 'open': sidebarOpen }">
        <div class="sidebar-header">
            <div class="logo">Menu</div>
            <button class="sidebar-close-btn" @click="sidebarOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <ul class="sidebar-nav">
            <li><a href="#properties" @click="sidebarOpen = false">Properties</a></li>
            <li><a href="#partners" @click="sidebarOpen = false">Partners</a></li>
            <li><a href="#analytics" @click="sidebarOpen = false">Analytics</a></li>
            <hr class="my-4">
            <li><a href="{{ route('login') }}" class="font-bold">Sign In</a></li>
            <li><a href="{{ route('register') }}" class="font-bold">Register</a></li>
        </ul>
    </div>
    <!-- Overlay for when sidebar is open -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/30 z-50"></div>


    <main>
        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="container">
                <h1>Find Your Perfect Stay</h1>
                <p>Discover amazing properties and book your next adventure with ease.</p>
                
                <!-- Booking Counters in Hero -->
                <div class="hero-booking-counters">
                    <div class="counter-item">
                        <div class="counter-label">Total Bookings</div>
                        <span class="counter-number">{{ number_format($totalBookings) }}</span>
                        <div class="counter-subtitle">All Time</div>
                    </div>
                    <div class="counter-item">
                        <div class="counter-label">Today's Bookings</div>
                        <span class="counter-number">{{ number_format($todayBookings) }}</span>
                        <div class="counter-subtitle">Today</div>
                    </div>
                </div>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $totalProperties }}</span>
                        <span class="stat-label">Properties</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $activeUsers }}</span>
                        <span class="stat-label">Active Users</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ count($topTravelPartners) }}</span>
                        <span class="stat-label">Travel Partners</span>
                    </div>
                </div>
                
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search destinations...">
                    <button class="search-button">Search</button>
                </div>
            </div>
        </section>

        <!-- Properties Section -->
        <section class="section" id="properties">
            <div class="container">
                <h2 class="section-title">Featured Properties</h2>
                <div class="properties-grid">
                    @forelse($properties ?? [] as $property)
                        <div class="property-card">
                            <div class="property-image">
                                @if(isset($property->photos) && $property->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . $property->photos->first()->file_path) }}" alt="{{ $property->name }}">
                                @else
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">🏨</div>
                                @endif
                                <div class="property-badge">{{ $property->category->name ?? 'Hotel' }}</div>
                            </div>
                            <div class="property-content">
                                <h3 class="property-title">{{ $property->name }}</h3>
                                <div class="property-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.1.4-.27.61-.47.21-.2.4-.4.56-.6.17-.2.32-.4.47-.65A10.43 10.43 0 0014 12c0-2.21-1.79-4-4-4s-4 1.79-4 4c0 .889.285 1.709.762 2.392.16.221.32.422.48.621.15.18.32.341.5.49.21.17.43.33.66.46.15.08.32.15.5.21l.005.002zM10 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                    <span>
                                        @if(isset($property->location) && $property->location->city)
                                            {{ $property->location->city->name }}, {{ $property->location->city->district->state->name }}
                                        @else
                                            Location not specified
                                        @endif
                                    </span>
                                </div>
                                <div class="property-amenities">
                                    @forelse($property->amenities->take(3) as $amenity)
                                        <span class="amenity-tag">{{ $amenity->name }}</span>
                                    @empty
                                        <span class="amenity-tag">WiFi</span>
                                        <span class="amenity-tag">Pool</span>
                                    @endforelse
                                </div>
                                <div class="property-footer">
                                    <div>
                                        <div class="property-price">
                                            @if(isset($property->propertyAccommodations) && $property->propertyAccommodations->isNotEmpty())
                                                @php
                                                    $minPrice = $property->propertyAccommodations->min('base_price');
                                                @endphp
                                                ₹{{ number_format($minPrice) }}
                                            @else
                                                ₹2,500
                                            @endif
                                        </div>
                                        <div class="price-period">/ night</div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" style="color: #fbbf24;" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span style="color: #6b7280; font-size: 0.875rem;">4.8</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        @for($i = 1; $i <= 6; $i++)
                            <!-- Placeholder Card -->
                        @endfor
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Travel Partners Section -->
        <section class="section partners-section" id="partners">
            <div class="container">
                <h2 class="section-title">Top Travel Partners</h2>
                <p class="section-subtitle">Our most trusted partners with the highest booking volumes</p>
                <div class="partners-grid">
                    @forelse($topTravelPartners as $index => $partner)
                        <div class="partner-card">
                            <div class="partner-rank">
                                <span class="rank-number">{{ $index + 1 }}</span>
                            </div>
                            <div class="partner-info">
                                <h3 class="partner-name">{{ $partner->partner_name }}</h3>
                                <div class="partner-type">{{ $partner->partner_type }}</div>
                                <div class="partner-stats">
                                    <span class="booking-count">{{ $partner->booking_count ?? 0 }} bookings</span>
                                    @if($partner->commission_rate > 0)
                                        <span class="commission-rate">{{ $partner->commission_rate }}% commission</span>
                                    @endif
                                </div>
                                @if($partner->email)
                                    <div class="partner-contact">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                        </svg>
                                        {{ $partner->email }}
                                    </div>
                                @endif
                            </div>
                            <div class="partner-status status-active">
                                {{ ucfirst($partner->status) }}
                            </div>
                        </div>
                    @empty
                        <div class="no-partners">
                            <div class="no-partners-icon">🤝</div>
                            <h3>No Travel Partners Yet</h3>
                            <p>We're working on building partnerships with top travel agencies.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Analytics Section -->
        <section class="section analytics-section" id="analytics">
            <div class="container">
                <h2 class="section-title">Platform Analytics</h2>
                <div class="analytics-grid">
                    <div class="analytics-card">
                        <div class="card-title">
                            <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path></svg></div>
                            <div class="title-text">Total Bookings</div>
                            <div class="percent">+{{ round(($todayBookings / max($totalBookings, 1)) * 100) }}%</div>
                        </div>
                        <div class="card-data"><p>{{ number_format($totalBookings) }}</p><div class="range"><div class="fill" style="width: {{ min(($totalBookings / max($totalBookings, 1)) * 100, 100) }}%"></div></div></div>
                    </div>
                    <div class="analytics-card">
                        <div class="card-title">
                            <div class="card-icon" style="background-color: #3b82f6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path></svg></div>
                            <div class="title-text">Total Revenue</div>
                            <div class="percent" style="color: #1d4ed8; background-color: #dbeafe;">+{{ $totalBookings > 0 ? round(($todayBookings / $totalBookings) * 100) : 0 }}%</div>
                        </div>
                        <div class="card-data"><p>₹{{ number_format($totalRevenue) }}</p><div class="range"><div class="fill" style="width: {{ min(($totalRevenue / max($totalRevenue, 1)) * 100, 100) }}%; background-color: #3b82f6;"></div></div></div>
                    </div>
                    <div class="analytics-card">
                        <div class="card-title">
                            <div class="card-icon" style="background-color: #8b5cf6;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg></div>
                            <div class="title-text">Active Users</div>
                            <div class="percent" style="color: #6d28d9; background-color: #ede9fe;">+{{ $totalBookings > 0 ? round(($activeUsers / max($totalBookings, 1)) * 100) : 0 }}%</div>
                        </div>
                        <div class="card-data"><p>{{ number_format($activeUsers) }}</p><div class="range"><div class="fill" style="width: {{ min(($activeUsers / max($activeUsers, 1)) * 100, 100) }}%; background-color: #8b5cf6;"></div></div></div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <div class="bottom-nav">
        <div class="bottom-nav-content">
            <a href="#home" class="bottom-nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                <span>Home</span>
            </a>
            <a href="#properties" class="bottom-nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a1 1 0 000 2c5.523 0 10 4.477 10 10a1 1 0 102 0C17 8.373 11.627 3 5 3z"></path><path d="M4 9a1 1 0 011-1 7 7 0 017 7 1 1 0 11-2 0 5 5 0 00-5-5 1 1 0 01-1-1zM3 15a2 2 0 114 0 2 2 0 01-4 0z"></path></svg>
                <span>Properties</span>
            </a>
            <a href="#partners" class="bottom-nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                <span>Partners</span>
            </a>
        </div>
    </div>

    <script>
        // Dropdown toggle function
        function toggleDropdown(element) {
            const dropdown = element.querySelector('.dropdown-menu');
            const isOpen = dropdown.classList.contains('show');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdown.classList.add('show');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.auth-dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add counter animation on scroll
        function animateCounters() {
            const counters = document.querySelectorAll('.counter-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/,/g, ''));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 16);
            });
        }

        // Initialize animations when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Animate counters when they come into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            const countersSection = document.querySelector('.hero-booking-counters');
            if (countersSection) {
                observer.observe(countersSection);
            }
        });
    </script>
</body>
</html>