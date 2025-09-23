@props(['title', 'subtitle', 'icon', 'addRoute' => null, 'addText' => 'Add'])

@push('styles')
<style>
    .page-header-gradient {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 16px;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    .stat-content {
        position: relative;
        z-index: 2;
        padding: 1rem;
    }
</style>
@endpush

<header class="page-header-gradient text-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-white bg-opacity-10"></div>
    <div class="relative px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-full glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
                    <i class="fas fa-bars text-pink-500"></i>
                </button>
                <div class="w-10 h-10 rounded-full glass-card flex items-center justify-center">
                    <i class="fas fa-{{ $icon }} text-teal-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">{{ $title }}</h1>
                    <p class="text-sm text-slate-700">{{ $subtitle }}</p>
                </div>
            </div>
            @if($addRoute)
            <a href="{{ $addRoute }}" class="glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                <i class="fas fa-plus text-pink-500 mr-2"></i>
                <span class="font-medium text-slate-800 hidden sm:inline">{{ $addText }}</span>
                <span class="font-medium text-slate-800 sm:hidden">Add</span>
            </a>
            @endif
        </div>

        <div x-show="message" x-transition class="mb-4 p-4 rounded-xl" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            <span x-text="message"></span>
        </div>

        {{ $slot }}
    </div>
</header>