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
                    <i class="fas fa-building text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Hi, {{ auth()->user()->name ?? 'Manager' }} 👋</h1>
                    <p class="text-sm opacity-90">Welcome back to your dashboard</p>
                </div>
            </div>
            <div class="relative">
                <button class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-bell text-white"></i>
                </button>
                <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-white" x-text="notifications"></span>
                </div>
            </div>
        </div>

        @include('partials.dashboard.quick-actions')
        @include('partials.dashboard.stats-overview')
    </div>
</header>