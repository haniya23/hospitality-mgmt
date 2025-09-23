{{-- Add these styles to your stylesheet or inside the <head> tag --}}
@push('styles')
<style>
    /* * A soft, airy gradient for the header that perfectly complements 
     * the revenue cards below it. It blends the colors from your examples.
     */
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    /* * A lighter glassmorphism effect for buttons and internal cards.
     * This style matches the 'bg-white bg-opacity-30' from your card example.
     */
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .glass-3d-card {
        --white: #ffe7ff;
        --bg: #080808;
        --radius: 20px;
        outline: none;
        cursor: pointer;
        border: 0;
        position: relative;
        border-radius: var(--radius);
        background-color: var(--bg);
        transition: all 0.2s ease;
        box-shadow:
            inset 0 0.3rem 0.9rem rgba(255, 255, 255, 0.3),
            inset 0 -0.1rem 0.3rem rgba(0, 0, 0, 0.7),
            inset 0 -0.4rem 0.9rem rgba(255, 255, 255, 0.5),
            0 3rem 3rem rgba(0, 0, 0, 0.3),
            0 1rem 1rem -0.6rem rgba(0, 0, 0, 0.8);
    }
    .glass-3d-content {
        color: rgba(255, 255, 255, 0.7);
        padding: 16px;
        border-radius: inherit;
        position: relative;
        overflow: hidden;
    }
    .glass-3d-content::before,
    .glass-3d-content::after {
        content: "";
        position: absolute;
        transition: all 0.3s ease;
    }
    .glass-3d-content::before {
        left: -15%;
        right: -15%;
        bottom: 25%;
        top: -100%;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.12);
    }
    .glass-3d-content::after {
        left: 6%;
        right: 6%;
        top: 12%;
        bottom: 40%;
        border-radius: 22px 22px 0 0;
        box-shadow: inset 0 10px 8px -10px rgba(255, 255, 255, 0.8);
        background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0.3) 0%,
            rgba(0, 0, 0, 0) 50%,
            rgba(0, 0, 0, 0) 100%
        );
    }
    .glass-3d-card:hover {
        box-shadow:
            inset 0 0.3rem 0.5rem rgba(255, 255, 255, 0.4),
            inset 0 -0.1rem 0.3rem rgba(0, 0, 0, 0.7),
            inset 0 -0.4rem 0.9rem rgba(255, 255, 255, 0.7),
            0 3rem 3rem rgba(0, 0, 0, 0.3),
            0 1rem 1rem -0.6rem rgba(0, 0, 0, 0.8);
    }
    .glass-3d-card:hover .glass-3d-content::before {
        transform: translateY(-5%);
    }
    .glass-3d-card:hover .glass-3d-content::after {
        opacity: 0.4;
        transform: translateY(5%);
    }
    .glass-3d-card:active {
        transform: translateY(4px);
        box-shadow:
            inset 0 0.3rem 0.5rem rgba(255, 255, 255, 0.5),
            inset 0 -0.1rem 0.3rem rgba(0, 0, 0, 0.8),
            inset 0 -0.4rem 0.9rem rgba(255, 255, 255, 0.4),
            0 3rem 3rem rgba(0, 0, 0, 0.3),
            0 1rem 1rem -0.6rem rgba(0, 0, 0, 0.8);
    }
</style>
@endpush

{{-- The New Header --}}
<header class="soft-header-gradient text-slate-800 relative overflow-hidden pb-6">
    {{-- This optional overlay adds a little bit of depth. You can adjust or remove it. --}}
    <div class="absolute inset-0 bg-white bg-opacity-10"></div>

    <div class="relative px-4 py-6">
        {{-- Top Section: Welcome message and notifications --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                {{-- Mobile sidebar toggle button --}}
                <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
                    <i class="fas fa-bars text-pink-500"></i>
                </button>
                {{-- Main icon --}}
                <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                    <i class="fas fa-building text-teal-600"></i>
                </div>
                {{-- Welcome Text --}}
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Hi, {{ auth()->user()->name ?? 'Manager' }} 👋</h1>
                    <p class="text-sm text-slate-700">Here's what's happening today.</p>
                </div>
            </div>
            {{-- Notification Button --}}
            <div class="relative">
                <button class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                    <i class="fas fa-bell text-pink-500"></i>
                </button>
                <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-xs font-bold text-white" x-text="notifications || 0"></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-3d-card">
                <div class="glass-3d-content flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-orange-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white opacity-80">Check-ins</p>
                        <p class="text-xl font-bold text-white" x-text="todayStats?.checkIns || 0"></p>
                    </div>
                </div>
            </div>
            <div class="glass-3d-card">
                <div class="glass-3d-content flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white opacity-80">New Bookings</p>
                        <p class="text-xl font-bold text-white" x-text="todayStats?.newBookings || 0"></p>
                    </div>
                </div>
            </div>
            <div class="glass-3d-card">
                <div class="glass-3d-content flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-white opacity-80">Check-outs</p>
                        <p class="text-xl font-bold text-white" x-text="todayStats?.checkOuts || 0"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>