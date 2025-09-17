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
        border-radius: 20px;
        background: linear-gradient(40deg, #FF0080, #FF8C00 70%);
        box-shadow: 0 8px 32px rgba(0,0,0,0.3), inset 0 2px 0 rgba(255,255,255,0.2);
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
    }
    .glass-3d-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 20px;
    }
    .glass-3d-content {
        position: relative;
        z-index: 2;
        padding: 12px;
    }
    .glass-3d-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.4), inset 0 2px 0 rgba(255,255,255,0.3);
    }
    .active-filter-card {
        background: linear-gradient(40deg, #8983F7, #A3DAFB 70%) !important;
        box-shadow: 0 12px 40px rgba(137, 131, 247, 0.4), inset 0 2px 0 rgba(255,255,255,0.3) !important;
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
            <button @click="openBookingModal()" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                <i class="fas fa-plus text-pink-500 mr-2"></i>
                <span class="font-medium text-slate-800">New</span>
            </button>
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