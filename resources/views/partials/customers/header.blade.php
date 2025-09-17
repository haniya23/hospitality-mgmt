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
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Customer Management</h1>
                    <p class="text-sm opacity-90">Manage your customers</p>
                </div>
            </div>
            <button @click="showAddModal = true" class="glassmorphism rounded-xl px-4 py-2">
                <i class="fas fa-plus mr-2"></i>
                <span class="font-medium">Add</span>
            </button>
        </div>

        <div class="glassmorphism rounded-2xl p-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold" x-text="filteredCustomers.filter(c => c.status === 'active').length"></div>
                    <div class="text-xs opacity-75">Active</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" x-text="filteredCustomers.reduce((sum, c) => sum + c.totalBookings, 0)"></div>
                    <div class="text-xs opacity-75">Total Bookings</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" x-text="filteredCustomers.length"></div>
                    <div class="text-xs opacity-75">Total Customers</div>
                </div>
            </div>
        </div>
    </div>
</header>