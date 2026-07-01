<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stay loops - Manage Your Property Free, From Anywhere</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Vite Assets (compiled Tailwind CSS v4) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1VLPS4F73T"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-1VLPS4F73T');
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .font-display {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .hero-bg {
            background-image: 
                radial-gradient(circle at 100% 0%, rgba(16, 185, 129, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(59, 130, 246, 0.06) 0%, transparent 40%);
        }
        .glow-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glow-hover:hover {
            box-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.15);
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-emerald-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Header / Navigation -->
    <nav class="bg-white/70 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-200/60 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="#home" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                            <span class="text-white text-xl font-bold font-display">S</span>
                        </div>
                        <span class="text-xl font-bold font-display tracking-tight text-slate-900">Stay loops</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Features</a>
                    <a href="#properties" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Properties</a>
                    <a href="#partners" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Partners</a>
                    <a href="#stats" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Analytics</a>
                    <a href="#pricing" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Pricing</a>
                    <a href="#faq" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">FAQs</a>
                </div>

                <!-- CTA Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-emerald-600 transition-colors px-4 py-2">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="bg-slate-900 hover:bg-emerald-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-200 transform hover:-translate-y-0.5">
                        Get Started Free
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-slate-900 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Toggle menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden border-t border-slate-200/50 bg-white" x-cloak>
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#features" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Features</a>
                <a href="#properties" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Properties</a>
                <a href="#partners" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Partners</a>
                <a href="#stats" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Analytics</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">Pricing</a>
                <a href="#faq" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">FAQs</a>
                <div class="border-t border-slate-100 pt-4 mt-2 flex flex-col space-y-3 px-4">
                    <a href="{{ route('login') }}" class="text-center font-semibold text-slate-700 hover:text-emerald-600 py-2.5 rounded-xl border border-slate-200">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="text-center font-semibold text-white bg-slate-900 hover:bg-emerald-600 py-3 rounded-xl transition-all">
                        Get Started Free
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative overflow-hidden pt-8 pb-20 md:pb-28 hero-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="lg:col-span-6 text-center lg:text-left space-y-6">
                    <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-emerald-200/50 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Manage your property free, from anywhere</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-display text-slate-900 tracking-tight leading-[1.1] sm:leading-[1.05]">
                        Manage Your Property <br class="hidden sm:inline" />
                        <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Free, From Anywhere</span>
                    </h1>

                    <p class="text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        Say loops is a professional, all-in-one hospitality management solution designed to organize rooms, schedule staff, process bookings, and generate detailed financial reports—entirely for free.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-tr from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-semibold px-8 py-4 rounded-2xl shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/35 transition-all text-center">
                            Create Free Account
                        </a>
                        <a href="#features" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-800 font-semibold px-8 py-4 rounded-2xl border border-slate-200 shadow-sm transition-all text-center">
                            Explore Features
                        </a>
                    </div>

                    <div class="flex items-center justify-center lg:justify-start space-x-6 pt-4 text-xs font-bold text-slate-500">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            <span>No Credit Card Required</span>
                        </span>
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            <span>Free Forever Plan</span>
                        </span>
                    </div>
                </div>

                <!-- App Mockup Visual (Glassmorphism CSS representation) -->
                <div class="lg:col-span-6 relative">
                    <div class="relative mx-auto max-w-[540px] lg:max-w-none">
                        <!-- Background Glow Elements -->
                        <div class="absolute -inset-4 bg-gradient-to-tr from-emerald-500 to-cyan-400 rounded-3xl opacity-20 blur-2xl -z-10"></div>
                        
                        <!-- Main Panel Mockup -->
                        <div class="bg-slate-900 text-slate-100 rounded-3xl shadow-2xl border border-slate-800 overflow-hidden font-mono text-[11px] leading-relaxed relative z-10">
                            <!-- Top Bar -->
                            <div class="flex items-center justify-between px-4 py-3 bg-slate-950/80 border-b border-slate-800">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                                </div>
                                <div class="text-[10px] text-slate-500 tracking-wider">APP.STAYLOOPS.COM/DASHBOARD</div>
                                <div class="w-4"></div>
                            </div>
                            
                            <div class="grid grid-cols-12 min-h-[350px]">
                                <!-- Left Sidebar -->
                                <div class="col-span-3 bg-slate-950/40 p-4 border-r border-slate-800 space-y-4 hidden sm:block">
                                    <div class="h-4 bg-emerald-500/20 rounded-md w-3/4 border border-emerald-500/20"></div>
                                    <div class="space-y-2.5">
                                        <div class="h-3 bg-slate-800 rounded-md w-full"></div>
                                        <div class="h-3 bg-slate-800 rounded-md w-5/6"></div>
                                        <div class="h-3 bg-slate-800 rounded-md w-4/5"></div>
                                        <div class="h-3 bg-slate-800 rounded-md w-11/12"></div>
                                        <div class="h-3 bg-slate-800 rounded-md w-2/3"></div>
                                    </div>
                                    <div class="pt-6">
                                        <div class="h-3 bg-slate-800 rounded-md w-full mb-2"></div>
                                        <div class="h-8 bg-slate-900 rounded-lg border border-slate-800 p-1 flex items-center justify-between">
                                            <div class="w-4 h-4 rounded bg-emerald-500"></div>
                                            <div class="w-8 h-2 bg-slate-700 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Center Panel -->
                                <div class="col-span-12 sm:col-span-9 p-5 space-y-5">
                                    <!-- Mini Header -->
                                    <div class="flex justify-between items-center">
                                        <div class="space-y-1">
                                            <div class="text-xs font-bold text-slate-300 font-display">Overview Dashboard</div>
                                            <div class="text-[9px] text-emerald-400 flex items-center space-x-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-ping"></span>
                                                <span>Live Sync Enabled</span>
                                            </div>
                                        </div>
                                        <div class="px-2.5 py-1 bg-slate-800 rounded-md border border-slate-700 text-[9px] text-slate-300 flex items-center space-x-1.5">
                                            <span>Select Property: Grand Palace</span>
                                        </div>
                                    </div>

                                    <!-- Counter Cards -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
                                            <span class="text-[9px] text-slate-500 tracking-wider">REVENUE</span>
                                            <span class="text-xs font-bold block text-slate-200">₹45,280</span>
                                            <span class="text-[8px] text-emerald-400 font-bold font-sans">+12% vs last wk</span>
                                        </div>
                                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
                                            <span class="text-[9px] text-slate-500 tracking-wider">OCCUPANCY</span>
                                            <span class="text-xs font-bold block text-slate-200">88.5%</span>
                                            <span class="text-[8px] text-emerald-400 font-bold font-sans">+4.1%</span>
                                        </div>
                                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/80 space-y-1">
                                            <span class="text-[9px] text-slate-500 tracking-wider">CLEANING</span>
                                            <span class="text-xs font-bold block text-slate-200">4/5 Ready</span>
                                            <span class="text-[8px] text-yellow-400 font-bold font-sans">1 In Progress</span>
                                        </div>
                                    </div>

                                    <!-- Table / Timeline Mock -->
                                    <div class="bg-slate-950/60 rounded-xl border border-slate-800/80 overflow-hidden">
                                        <div class="px-3.5 py-2 bg-slate-950/80 border-b border-slate-800 text-[9px] text-slate-400 font-bold">Upcoming Arrivals</div>
                                        <div class="divide-y divide-slate-900 px-3.5 py-1">
                                            <div class="py-2 flex items-center justify-between text-[9px]">
                                                <span class="text-slate-300 font-bold">Room 104 • Deluxe Suite</span>
                                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[8px] font-sans font-bold">Checked In</span>
                                            </div>
                                            <div class="py-2 flex items-center justify-between text-[9px]">
                                                <span class="text-slate-300 font-bold">Room 201 • Executive Twin</span>
                                                <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30 text-[8px] font-sans font-bold">Confirmed</span>
                                            </div>
                                            <div class="py-2 flex items-center justify-between text-[9px]">
                                                <span class="text-slate-300 font-bold">Room 102 • Royal Penthouse</span>
                                                <span class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 text-[8px] font-sans font-bold">Arriving Today</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Micro Badges -->
                        <div class="absolute -right-6 bottom-12 bg-white text-slate-900 rounded-2xl shadow-xl border border-slate-100 p-3.5 flex items-center space-x-3 z-20">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="space-y-0.5">
                                <span class="block text-[10px] text-slate-500 font-bold tracking-wide">ZERO FEES</span>
                                <span class="block text-xs font-extrabold text-slate-900">100% Free Plan</span>
                            </div>
                        </div>

                        <div class="absolute -left-6 top-16 bg-white text-slate-900 rounded-2xl shadow-xl border border-slate-100 p-3.5 flex items-center space-x-3 z-20">
                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V14a2 2 0 012-2h.055M11 20.055V18a2 2 0 00-2-2h-.055" /></svg>
                            </div>
                            <div class="space-y-0.5">
                                <span class="block text-[10px] text-slate-500 font-bold tracking-wide">LOCATION</span>
                                <span class="block text-xs font-extrabold text-slate-900">Manage Anywhere</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Platforms Logo Cloud -->
    <section class="border-y border-slate-200/60 bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Integrated with Hospitality Ecosystems</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 items-center justify-items-center opacity-40">
                <span class="text-lg font-extrabold tracking-tight text-slate-800 font-display">Booking.com</span>
                <span class="text-lg font-extrabold tracking-tight text-slate-800 font-display">Airbnb</span>
                <span class="text-lg font-extrabold tracking-tight text-slate-800 font-display">Expedia</span>
                <span class="text-lg font-extrabold tracking-tight text-slate-800 font-display">Agoda</span>
                <span class="text-lg font-extrabold tracking-tight text-slate-800 font-display">TripAdvisor</span>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Powerful Capabilities</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Everything You Need to Run Your Property</p>
                <p class="text-slate-500 text-lg leading-relaxed">Stay loops equips you with standard administrative, staffing, and financial tracking toolkits to streamline operations.</p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/50 glow-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-5 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Property Management</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Easily maintain room inventories, track availability, customize pricing calendars, and handle guest registrations across multiple layouts.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/50 glow-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-5 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Staff & Cleaning Tasks</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Assign housekeepers, coordinate room checks, track tasks in real-time, and manage role-based staff credentials.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/50 glow-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-5 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Financial Reporting</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Log incomes and operational costs. Generate weekly/monthly reports, monitor revenue sheets, and export details for tax returns.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/50 glow-hover">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-5 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">B2B Travel Partners</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Track OTA booking counts, monitor commissions, handle partner transactions, and optimize booking pipelines.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Walkthrough / How it Works -->
    <section class="py-20 bg-white border-y border-slate-200/50" x-data="{ activeStep: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <!-- Info Column -->
                <div class="lg:col-span-5 space-y-6">
                    <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">SaaS Walkthrough</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Streamlined Hospitality in 4 Simple Steps</p>
                    <p class="text-slate-500">Discover how Stay loops automates booking processing, resource delegation, and revenue reporting.</p>

                    <!-- Interactive Steps Buttons -->
                    <div class="space-y-3 pt-4">
                        <button @click="activeStep = 1" 
                                :class="activeStep === 1 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                                class="flex items-center space-x-4 w-full p-4 rounded-xl border text-left font-semibold transition-all">
                            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">1</span>
                            <span>List & Setup Accommodations</span>
                        </button>
                        <button @click="activeStep = 2" 
                                :class="activeStep === 2 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                                class="flex items-center space-x-4 w-full p-4 rounded-xl border text-left font-semibold transition-all">
                            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">2</span>
                            <span>Accept & Organise Reservations</span>
                        </button>
                        <button @click="activeStep = 3" 
                                :class="activeStep === 3 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                                class="flex items-center space-x-4 w-full p-4 rounded-xl border text-left font-semibold transition-all">
                            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">3</span>
                            <span>Delegate Cleaning & Tasks</span>
                        </button>
                        <button @click="activeStep = 4" 
                                :class="activeStep === 4 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:border-slate-300 text-slate-700'"
                                class="flex items-center space-x-4 w-full p-4 rounded-xl border text-left font-semibold transition-all">
                            <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">4</span>
                            <span>Monitor Profitability Analytics</span>
                        </button>
                    </div>
                </div>

                <!-- Showcase Illustration Column -->
                <div class="lg:col-span-7 bg-slate-50 p-6 sm:p-10 rounded-3xl border border-slate-200/60 min-h-[360px] flex items-center justify-center">
                    <!-- Step 1 Details -->
                    <div x-show="activeStep === 1" x-transition.opacity class="space-y-4 text-center">
                        <div class="text-5xl mb-4">🏠</div>
                        <h4 class="text-xl font-bold text-slate-900 font-display">1. Setup Your Property Catalog</h4>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">Add photos, define custom room features, configure base price metrics, and list check-in protocols. Stay loops creates local inventories within minutes.</p>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm max-w-sm mx-auto text-left space-y-2">
                            <div class="h-3 bg-emerald-100 rounded w-1/3"></div>
                            <div class="h-2.5 bg-slate-200 rounded w-full"></div>
                            <div class="h-2.5 bg-slate-200 rounded w-5/6"></div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-xs font-bold text-emerald-600">₹2,800 / night</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold text-[9px] border border-emerald-100">Deluxe Double</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 Details -->
                    <div x-show="activeStep === 2" x-transition.opacity class="space-y-4 text-center" x-cloak>
                        <div class="text-5xl mb-4">📅</div>
                        <h4 class="text-xl font-bold text-slate-900 font-display">2. Streamlined Booking Calendars</h4>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">View all reservations chronologically. Handle guest arrivals, track check-outs, process invoice states, and verify payment settlements in real time.</p>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm max-w-sm mx-auto text-left space-y-2">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <span class="text-xs font-bold text-slate-800">Booking #4029</span>
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 font-bold text-[9px] border border-blue-100">Confirmed</span>
                            </div>
                            <div class="text-[11px] text-slate-500 space-y-1">
                                <div>Check-in: <strong>12 Oct 2026</strong></div>
                                <div>Nights: <strong>3 Nights</strong></div>
                                <div>Guest: <strong>A. Sharma (2 Guests)</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Details -->
                    <div x-show="activeStep === 3" x-transition.opacity class="space-y-4 text-center" x-cloak>
                        <div class="text-5xl mb-4">🧹</div>
                        <h4 class="text-xl font-bold text-slate-900 font-display">3. Smart Housekeeping & Task Delegator</h4>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">Create housekeeper profiles, auto-assign rooms upon check-out, track cleanliness statuses, and coordinate maintenance tasks from the mobile view.</p>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm max-w-sm mx-auto text-left space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800">Room 203 Cleaning</span>
                                <span class="px-2 py-0.5 rounded bg-yellow-50 text-yellow-600 font-bold text-[9px] border border-yellow-100">In Progress</span>
                            </div>
                            <div class="text-[11px] text-slate-500">Assigned To: <strong>Rohan Singh (Housekeeping)</strong></div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                                <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 Details -->
                    <div x-show="activeStep === 4" x-transition.opacity class="space-y-4 text-center" x-cloak>
                        <div class="text-5xl mb-4">📈</div>
                        <h4 class="text-xl font-bold text-slate-900 font-display">4. Complete Finance Analytics</h4>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">Log operations expenses and OTA commissions. Generate instant weekly or monthly income sheets, and export tax summaries as CSV.</p>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm max-w-sm mx-auto text-left space-y-3">
                            <div class="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">Monthly Net Income</div>
                            <div class="flex items-end justify-between h-16 px-4">
                                <div class="w-6 bg-slate-200 rounded-t h-8"></div>
                                <div class="w-6 bg-slate-200 rounded-t h-12"></div>
                                <div class="w-6 bg-emerald-500 rounded-t h-16"></div>
                                <div class="w-6 bg-emerald-600 rounded-t h-14"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-700">
                                <span>Profit Margin: 72%</span>
                                <span class="text-emerald-600">₹1,12,400 Profit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Stats / Dynamic Analytics Section -->
    <section id="stats" class="py-20 bg-slate-900 text-white relative">
        <!-- Floating shapes -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(16,185,129,0.06),transparent_50%)]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Platform Analytics</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display tracking-tight text-white">Live Platform Activity</p>
                <p class="text-slate-400 text-base">Real-time statistics aggregating current properties, active users, and guest reservations running on Stay loops.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stat Card 1 -->
                <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 text-center space-y-2 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                    <span class="block text-slate-500 text-xs font-bold tracking-widest uppercase">Total Bookings</span>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white font-display tracking-tight">
                        {{ number_format($totalBookings) }}
                    </span>
                    <span class="block text-[10px] text-emerald-400 font-bold font-mono">ALL TIME RESERVATIONS</span>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 text-center space-y-2 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-blue-500 to-teal-400"></div>
                    <span class="block text-slate-500 text-xs font-bold tracking-widest uppercase">Today's Bookings</span>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white font-display tracking-tight">
                        {{ number_format($todayBookings) }}
                    </span>
                    <span class="block text-[10px] text-blue-400 font-bold font-mono">NEW BOOKINGS TODAY</span>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 text-center space-y-2 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-pink-500"></div>
                    <span class="block text-slate-500 text-xs font-bold tracking-widest uppercase">Active Properties</span>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white font-display tracking-tight">
                        {{ number_format($totalProperties) }}
                    </span>
                    <span class="block text-[10px] text-purple-400 font-bold font-mono">REGISTERED HOTELS / HOMESTAYS</span>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-slate-950/60 p-6 rounded-2xl border border-slate-800 text-center space-y-2 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-yellow-500 to-orange-500"></div>
                    <span class="block text-slate-500 text-xs font-bold tracking-widest uppercase">Active Users</span>
                    <span class="block text-4xl sm:text-5xl font-extrabold text-white font-display tracking-tight">
                        {{ number_format($activeUsers) }}
                    </span>
                    <span class="block text-[10px] text-yellow-400 font-bold font-mono">VERIFIED PROPERTY MANAGERS</span>
                </div>
            </div>

            <!-- Revenue Callout Card -->
            <div class="mt-12 bg-gradient-to-r from-emerald-950/80 to-slate-950/80 rounded-3xl p-6 sm:p-8 border border-emerald-800/40 max-w-3xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-1 text-center sm:text-left">
                    <h4 class="text-lg font-bold text-slate-200 font-display">Aggregate Financial Processing</h4>
                    <p class="text-sm text-slate-400 max-w-md">Our dashboard has aggregated and tracked transactional check-in receipts for booking settlements.</p>
                </div>
                <div class="bg-slate-900/90 py-3 px-6 rounded-2xl border border-slate-800/80 text-center min-w-[200px]">
                    <span class="text-[10px] text-slate-400 block font-bold tracking-wider">TOTAL VOLUME PROCESSED</span>
                    <span class="text-2xl font-black text-emerald-400 block tracking-tight font-display">₹{{ number_format($totalRevenue) }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Properties Grid -->
    <section id="properties" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Property Directory</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Featured Accommodations</p>
                <p class="text-slate-500 text-base">Explore properties currently managed through the Stay loops system.</p>
            </div>

            <!-- Properties Cards Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($properties ?? [] as $property)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col h-full group">
                        <!-- Card Cover Image -->
                        <div class="relative h-56 bg-slate-200 overflow-hidden">
                            @if(isset($property->photos) && $property->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $property->photos->first()->file_path) }}" 
                                     alt="{{ $property->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-slate-800 to-slate-900 flex items-center justify-center text-white text-3xl">
                                    🏨
                                </div>
                            @endif
                            <!-- Category Badge -->
                            <span class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-lg shadow-sm">
                                {{ $property->category->name ?? 'Hotel' }}
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 flex flex-col flex-grow space-y-4">
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900 font-display line-clamp-1">{{ $property->name }}</h3>
                                <div class="flex items-center space-x-1.5 text-xs text-slate-500">
                                    <svg class="w-4.5 h-4.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-1">
                                        @if(isset($property->location) && $property->location->city)
                                            {{ $property->location->city->name }}, {{ $property->location->city->district->state->name }}
                                        @else
                                            Location not specified
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Amenity Badges -->
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($property->amenities->take(3) as $amenity)
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded">
                                        {{ $amenity->name }}
                                    </span>
                                @empty
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded">WiFi</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded">AC Rooms</span>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2 py-0.5 rounded">Housekeeping</span>
                                @endforelse
                            </div>

                            <!-- Price block footer -->
                            <div class="pt-4 border-t border-slate-100 flex justify-between items-center mt-auto">
                                <div>
                                    <span class="text-xs text-slate-400 block">Starting From</span>
                                    <span class="text-lg font-extrabold text-emerald-600 font-display">
                                        @if(isset($property->propertyAccommodations) && $property->propertyAccommodations->isNotEmpty())
                                            ₹{{ number_format($property->propertyAccommodations->min('base_price')) }}
                                        @else
                                            ₹2,500
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-slate-500">/ night</span>
                                </div>

                                <div class="flex items-center space-x-1 bg-yellow-50 text-yellow-700 px-2 py-1 rounded-lg border border-yellow-200/50">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span class="text-xs font-bold font-sans">4.8</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback Placeholders -->
                    @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden flex flex-col h-full opacity-60">
                            <div class="h-56 bg-slate-200 flex items-center justify-center text-4xl">🏨</div>
                            <div class="p-6 space-y-4">
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                                    <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                                </div>
                                <div class="h-2 bg-slate-200 rounded w-3/4 pt-4"></div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Top Travel Partners Section -->
    <section id="partners" class="py-20 bg-white border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Synergies & Channels</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Top Travel Agency Partners</p>
                <p class="text-slate-500 text-base">Agencies and travel partners with the highest reservation volumes linked to our properties.</p>
            </div>

            <!-- Partners Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($topTravelPartners as $index => $partner)
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60 relative overflow-hidden flex flex-col justify-between group">
                        <!-- Top line accent -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                        
                        <!-- Ranking Circle Badge -->
                        <div class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shadow-sm
                            {{ $index === 0 ? 'bg-gradient-to-tr from-yellow-400 to-amber-300 text-amber-950 border border-yellow-500/20' : '' }}
                            {{ $index === 1 ? 'bg-gradient-to-tr from-slate-300 to-slate-200 text-slate-900 border border-slate-400/20' : '' }}
                            {{ $index === 2 ? 'bg-gradient-to-tr from-amber-600 to-amber-500 text-amber-50 border border-amber-600/20' : '' }}
                            {{ $index > 2 ? 'bg-slate-200 text-slate-600' : '' }}">
                            <span>#{{ $index + 1 }}</span>
                        </div>

                        <!-- Card Body -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-display pr-10 line-clamp-1">{{ $partner->partner_name }}</h3>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mt-0.5">
                                    {{ $partner->partner_type }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-3 text-xs">
                                <span class="bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/50 px-2.5 py-1 rounded-lg">
                                    {{ $partner->booking_count ?? 0 }} bookings
                                </span>
                                @if($partner->commission_rate > 0)
                                    <span class="bg-blue-50 text-blue-700 font-bold border border-blue-200/50 px-2.5 py-1 rounded-lg">
                                        {{ $partner->commission_rate }}% commission
                                    </span>
                                @endif
                            </div>

                            @if($partner->email)
                                <div class="flex items-center space-x-1.5 text-xs text-slate-500 pt-1">
                                    <svg class="w-4.5 h-4.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate">{{ $partner->email }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200/50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Integration Status</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase border bg-emerald-50 text-emerald-700 border-emerald-200/40">
                                {{ $partner->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <!-- Empty State Fallback -->
                    <div class="col-span-3 text-center py-12 bg-slate-50 rounded-3xl border border-slate-200/50">
                        <span class="text-4xl">🤝</span>
                        <h3 class="text-lg font-bold text-slate-900 mt-3 font-display">No Travel Partners Registered</h3>
                        <p class="text-slate-400 text-sm max-w-sm mx-auto mt-1">Properties are currently connected via standard organic bookings.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Pricing Section (Emphasizing the 'Free' tier) -->
    <section id="pricing" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Plans & Pricing</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Flexible Plans For Every Operator</p>
                <p class="text-slate-500 text-base">Manage your property completely free with our standard tier. Switch to advanced channels as your rooms scale.</p>
            </div>

            <!-- Pricing Grid -->
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Plan 1: Free Forever -->
                <div class="bg-white rounded-3xl p-8 border-2 border-emerald-500 shadow-lg relative overflow-hidden flex flex-col justify-between">
                    <!-- Ribbon -->
                    <span class="absolute top-4 right-4 bg-emerald-500 text-white text-[10px] font-black tracking-widest uppercase px-3 py-1 rounded-full font-display">
                        FREE FOREVER
                    </span>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-slate-900 font-display">Starter Free Plan</h3>
                            <p class="text-slate-400 text-xs font-normal">Perfect for homestays, bed-and-breakfasts, and boutique hotels.</p>
                        </div>
                        
                        <div class="flex items-baseline">
                            <span class="text-5xl font-black text-slate-900 font-display">₹0</span>
                            <span class="text-slate-400 text-sm font-semibold ml-2">/ month</span>
                        </div>
                        
                        <!-- Divider -->
                        <div class="h-[1px] bg-slate-100"></div>

                        <!-- Feature list -->
                        <ul class="space-y-3.5 text-slate-600 text-xs font-medium">
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>Manage up to 5 Active Properties</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>Unlimited Reservations & Check-ins</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>Housekeeper & Staff Task Delegation</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>Weekly Net Income Reports</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span>Basic Travel Partner tracking</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-8">
                        <a href="{{ route('register') }}" class="block w-full text-center bg-gradient-to-tr from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold py-4 rounded-2xl shadow-md transition-all">
                            Get Started For Free
                        </a>
                    </div>
                </div>

                <!-- Plan 2: Growth Pro -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between opacity-85">
                    <span class="absolute top-4 right-4 bg-slate-200 text-slate-700 text-[9px] font-black tracking-widest uppercase px-3 py-1 rounded-full font-display">
                        COMING SOON
                    </span>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-slate-900 font-display">Professional Scale</h3>
                            <p class="text-slate-400 text-xs font-normal">For hotel chains, resorts, and vacation rental managers.</p>
                        </div>
                        
                        <div class="flex items-baseline">
                            <span class="text-5xl font-black text-slate-900 font-display">₹1,999</span>
                            <span class="text-slate-400 text-sm font-semibold ml-2">/ month</span>
                        </div>
                        
                        <!-- Divider -->
                        <div class="h-[1px] bg-slate-100"></div>

                        <!-- Feature list -->
                        <ul class="space-y-3.5 text-slate-600 text-xs font-medium">
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-slate-500">Unlimited Active Properties</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-slate-500">Custom Role-based Access Levels</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-slate-500">Automated PDF Invoice Generation</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-slate-500">Monthly Tax Audits & Exports</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-slate-500">API Sync with major channel calendars</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-8">
                        <button disabled class="w-full text-center bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl cursor-not-allowed">
                            Plan Launching Soon
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section id="faq" class="py-20 bg-white" x-data="{ activeFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <h2 class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Frequently Asked Questions</h2>
                <p class="text-3xl sm:text-4xl font-extrabold font-display text-slate-900 tracking-tight">Got Questions? We've Got Answers</p>
                <p class="text-slate-500 text-base">Have queries regarding our property limits, pricing policies, or device syncs?</p>
            </div>

            <!-- Accordions Container -->
            <div class="space-y-4 divide-y divide-slate-100">
                <!-- FAQ 1 -->
                <div class="pt-4 first:pt-0">
                    <button @click="activeFaq = activeFaq === 1 ? null : 1" 
                            class="flex justify-between items-center w-full py-4 text-left font-bold text-slate-900 hover:text-emerald-600 transition-colors focus:outline-none">
                        <span>Is Stay loops genuinely free to use?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" 
                             :class="activeFaq === 1 ? 'rotate-180 text-emerald-500' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 1" x-transition class="pb-4 text-sm text-slate-500 leading-relaxed" x-cloak>
                        Yes, our Starter Plan is 100% free forever. You can manage up to 5 properties, create bookings, record transactions, and manage cleaning teams without registering a credit card. No hidden maintenance commissions.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="pt-4">
                    <button @click="activeFaq = activeFaq === 2 ? null : 2" 
                            class="flex justify-between items-center w-full py-4 text-left font-bold text-slate-900 hover:text-emerald-600 transition-colors focus:outline-none">
                        <span>Can I access the management dashboard on my phone?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" 
                             :class="activeFaq === 2 ? 'rotate-180 text-emerald-500' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 2" x-transition class="pb-4 text-sm text-slate-500 leading-relaxed" x-cloak>
                        Absolutely! Stay loops is designed mobile-first. The responsive dashboard loads on any smartphone browser, allowing you or your housekeeping crew to manage occupancy states, assign checklist tasks, or check reservations on the go.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="pt-4">
                    <button @click="activeFaq = activeFaq === 3 ? null : 3" 
                            class="flex justify-between items-center w-full py-4 text-left font-bold text-slate-900 hover:text-emerald-600 transition-colors focus:outline-none">
                        <span>What is the property category limit?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" 
                             :class="activeFaq === 3 ? 'rotate-180 text-emerald-500' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 3" x-transition class="pb-4 text-sm text-slate-500 leading-relaxed" x-cloak>
                        Our system handles hotels, rental homestays, bed-and-breakfast listings, hostels, and guest cottages. Each type has customizable accommodation templates so you can define specific rates per night for different room models.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="pt-4">
                    <button @click="activeFaq = activeFaq === 4 ? null : 4" 
                            class="flex justify-between items-center w-full py-4 text-left font-bold text-slate-900 hover:text-emerald-600 transition-colors focus:outline-none">
                        <span>How are travel agency B2B commissions handled?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform" 
                             :class="activeFaq === 4 ? 'rotate-180 text-emerald-500' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 4" x-transition class="pb-4 text-sm text-slate-500 leading-relaxed" x-cloak>
                        Stay loops allows you to create profiles for your travel partners, link specific commission rates to them, and map booking occurrences. The B2B summary calculates aggregated commission payouts to ensure simple audits.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Banner -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-tr from-slate-900 to-slate-950 rounded-[2.5rem] p-8 sm:p-12 lg:p-16 border border-slate-800 text-center relative overflow-hidden">
                <!-- Background decorative mesh -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,rgba(16,185,129,0.1),transparent_50%)]"></div>
                
                <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold font-display text-white tracking-tight">
                        Start Managing Your Property Free Today
                    </h2>
                    <p class="text-slate-400 text-base leading-relaxed">
                        Join property managers using Stay loops to streamline check-ins, tasks, and finances from anywhere in the world.
                    </p>
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-tr from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold px-8 py-4 rounded-2xl shadow-lg transition-all">
                            Get Started For Free
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto text-slate-300 font-semibold px-8 py-4 rounded-2xl border border-slate-850 hover:bg-slate-900 hover:text-white transition-all">
                            Sign In to Account
                        </a>
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 tracking-wider">NO CONTRACTS • NO CREDIT CARD REQUIREMENT</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-850 py-12 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-base font-bold font-display">S</span>
                        </div>
                        <span class="text-base font-bold font-display text-white">Stay loops</span>
                    </div>
                    <p class="text-slate-500 text-xs">Professional hospitality and property administration software designed for mobile autonomy.</p>
                </div>

                <!-- Column 2: Product -->
                <div class="space-y-3">
                    <span class="block text-slate-200 text-xs font-bold uppercase tracking-wider">Product Features</span>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#features" class="hover:text-emerald-500 transition-colors">Property Inventory</a></li>
                        <li><a href="#features" class="hover:text-emerald-500 transition-colors">Staff Checklists</a></li>
                        <li><a href="#features" class="hover:text-emerald-500 transition-colors">Financial Ledgers</a></li>
                        <li><a href="#features" class="hover:text-emerald-500 transition-colors">Partner Directory</a></li>
                    </ul>
                </div>

                <!-- Column 3: Resources -->
                <div class="space-y-3">
                    <span class="block text-slate-200 text-xs font-bold uppercase tracking-wider">Support</span>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#faq" class="hover:text-emerald-500 transition-colors">Help Center</a></li>
                        <li><a href="#faq" class="hover:text-emerald-500 transition-colors">FAQ Sheets</a></li>
                        <li><a href="#pricing" class="hover:text-emerald-500 transition-colors">Premium Plans</a></li>
                    </ul>
                </div>

                <!-- Column 4: Legals -->
                <div class="space-y-3">
                    <span class="block text-slate-200 text-xs font-bold uppercase tracking-wider">Legal Terms</span>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-emerald-500 transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-emerald-500 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Credits -->
            <div class="border-t border-slate-850 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <span>&copy; 2026 Stay loops. All rights reserved.</span>
                <span>Powered by Advanced Laravel + Vite Ecosystems.</span>
            </div>
        </div>
    </footer>

</body>
</html>