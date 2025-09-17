@push('styles')
<style>
    .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .glassmorphism { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18); }
</style>
@endpush

<header class="gradient-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-10"></div>
    <div class="relative px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition-all lg:hidden">
                    <i class="fas fa-bars text-white"></i>
                </button>
                <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Bookings</h1>
                    <p class="text-sm opacity-90">Manage your reservations</p>
                </div>
            </div>
            <button @click="openBookingModal()" class="glassmorphism rounded-xl px-4 py-2 hover:bg-opacity-30 transition-all">
                <i class="fas fa-plus mr-2"></i>
                <span class="font-medium">New</span>
            </button>
        </div>

        <!-- Flash Messages -->
        <div x-show="message" x-transition class="mb-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
            <span x-text="message"></span>
        </div>

        <div class="flex space-x-2 mb-4">
            <button @click="statusFilter = ''" :class="statusFilter === '' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'" class="px-4 py-2 rounded-full text-sm font-medium transition">All</button>
            <button @click="statusFilter = 'confirmed'" :class="statusFilter === 'confirmed' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'" class="px-4 py-2 rounded-full text-sm font-medium transition">Confirmed</button>
            <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-white bg-opacity-30' : 'bg-white bg-opacity-10'" class="px-4 py-2 rounded-full text-sm font-medium transition">Pending</button>
        </div>

        <div class="glassmorphism rounded-2xl p-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold" x-text="filteredBookings.filter(b => b.status === 'pending').length"></div>
                    <div class="text-xs opacity-75">Pending</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" x-text="filteredBookings.filter(b => b.status === 'confirmed').length"></div>
                    <div class="text-xs opacity-75">Confirmed</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" x-text="filteredBookings.filter(b => b.status === 'cancelled').length"></div>
                    <div class="text-xs opacity-75">Cancelled</div>
                </div>
            </div>
        </div>
    </div>
</header>