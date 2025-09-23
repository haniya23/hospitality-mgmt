{{-- Styles for the header --}}
@push('styles')
<style>
    /* Gradient for the header background, consistent with the soft theme */
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    /* Lighter glassmorphism effect for cards and buttons */
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
    .active-filter-card {
        background-color: #333 !important;
        box-shadow:
            inset 0 0.3rem 0.5rem rgba(255, 255, 255, 0.5),
            inset 0 -0.1rem 0.3rem rgba(0, 0, 0, 0.8),
            inset 0 -0.4rem 0.9rem rgba(255, 255, 255, 0.4),
            0 3rem 3rem rgba(0, 0, 0, 0.3),
            0 1rem 1rem -0.6rem rgba(0, 0, 0, 0.8) !important;
    }
</style>
@endpush

{{-- The Simplified Bookings Header --}}
<header class="soft-header-gradient text-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-white bg-opacity-10"></div>
    <div class="relative px-4 py-6">
        {{-- Top Section: Title and New Booking Button --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <button @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))" class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
                    <i class="fas fa-bars text-pink-500"></i>
                </button>
                <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-teal-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Bookings</h1>
                    <p class="text-sm text-slate-700">Manage your reservations</p>
                </div>
            </div>
            <a href="{{ route('bookings.create') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                <i class="fas fa-plus text-pink-500 mr-2"></i>
                <span class="font-medium text-slate-800">New</span>
            </a>
        </div>

        {{-- Flash Messages --}}
        <div x-show="message" x-transition class="mb-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            <span x-text="message"></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button @click="statusFilter = ''" :class="{ 'active-filter-card': statusFilter === '' }" class="glass-3d-card">
                <div class="glass-3d-content text-center">
                    <div class="text-2xl font-bold text-white" x-text="bookings.length"></div>
                    <div class="text-xs text-white opacity-80 uppercase tracking-wider">All</div>
                </div>
            </button>

            <button @click="statusFilter = 'pending'" :class="{ 'active-filter-card': statusFilter === 'pending' }" class="glass-3d-card">
                <div class="glass-3d-content text-center">
                    <div class="text-2xl font-bold text-white" x-text="bookings.filter(b => b.status === 'pending').length"></div>
                    <div class="text-xs text-white opacity-80 uppercase tracking-wider">Pending</div>
                </div>
            </button>

            <button @click="statusFilter = 'confirmed'" :class="{ 'active-filter-card': statusFilter === 'confirmed' }" class="glass-3d-card">
                <div class="glass-3d-content text-center">
                    <div class="text-2xl font-bold text-white" x-text="bookings.filter(b => b.status === 'confirmed').length"></div>
                    <div class="text-xs text-white opacity-80 uppercase tracking-wider">Confirmed</div>
                </div>
            </button>

            <button @click="statusFilter = 'cancelled'" :class="{ 'active-filter-card': statusFilter === 'cancelled' }" class="glass-3d-card">
                <div class="glass-3d-content text-center">
                    <div class="text-2xl font-bold text-white" x-text="bookings.filter(b => b.status === 'cancelled').length"></div>
                    <div class="text-xs text-white opacity-80 uppercase tracking-wider">Cancelled</div>
                </div>
            </button>
        </div>
    </div>
</header>