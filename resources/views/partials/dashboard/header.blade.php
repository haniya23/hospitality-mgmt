<x-trial-banner />

<x-page-header 
    title="Hi, {{ auth()->user()->name ?? 'Manager' }} 👋" 
    subtitle="Here's what's happening today" 
    icon="building">
    
    <div class="flex justify-end mb-2">
        <div class="relative">
            <button class="w-10 h-10 rounded-full glass-card flex items-center justify-center">
                <i class="fas fa-bell text-green-500"></i>
            </button>
            <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center border-2 border-white">
                <span class="text-xs font-bold text-white">0</span>
            </div>
        </div>
    </div>

</x-page-header>