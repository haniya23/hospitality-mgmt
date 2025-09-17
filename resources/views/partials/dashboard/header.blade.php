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
                <button @click="$dispatch('toggle-sidebar')" class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
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
                    <span class="text-xs font-bold text-white" x-text="notifications"></span>
                </div>
            </div>
        </div>

        {{-- Quick Summary Cards (within the header) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Card 1: Sales --}}
            <div class="soft-glass-card rounded-2xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-orange-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-700">Sales</p>
                        <p class="text-xl font-bold text-slate-900">1,402</p>
                    </div>
                </div>
            </div>
            {{-- Card 2: Customers --}}
            <div class="soft-glass-card rounded-2xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-users text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-700">Customers</p>
                        <p class="text-xl font-bold text-slate-900">356</p>
                    </div>
                </div>
            </div>
            {{-- Card 3: Pending Orders --}}
            <div class="soft-glass-card rounded-2xl p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-box text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-700">Pending</p>
                        <p class="text-xl font-bold text-slate-900">23</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>