@push('styles')
<style>
    .soft-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    .soft-glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

<header class="soft-header-gradient text-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-white bg-opacity-10"></div>
    <div class="relative px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
                    <i class="fas fa-bars text-pink-500"></i>
                </button>
                <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                    <i class="fas fa-chart-bar text-teal-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Reports & Analytics</h1>
                    <p class="text-sm text-slate-700">Business insights</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="soft-glass-card rounded-xl p-3 text-center">
                <div class="text-2xl font-bold text-slate-900">₹<span x-text="(metrics.revenue/1000).toFixed(0)"></span>K</div>
                <div class="text-xs text-slate-600 uppercase tracking-wider">Revenue</div>
            </div>
            <div class="soft-glass-card rounded-xl p-3 text-center">
                <div class="text-2xl font-bold text-slate-900" x-text="metrics.occupancy + '%'"></div>
                <div class="text-xs text-slate-600 uppercase tracking-wider">Occupancy</div>
            </div>
        </div>
    </div>
</header>