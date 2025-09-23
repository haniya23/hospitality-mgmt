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
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))" class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center hover:bg-opacity-60 transition-all lg:hidden">
                    <i class="fas fa-bars text-pink-500"></i>
                </button>
                <div class="w-10 h-10 rounded-full soft-glass-card flex items-center justify-center">
                    <i class="fas fa-edit text-teal-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Edit Booking</h1>
                    <p class="text-sm text-slate-700">Booking ID: #{{ $booking->id }}</p>
                </div>
            </div>
            <a href="{{ route('bookings.index') }}" class="soft-glass-card rounded-xl px-4 py-2 hover:bg-opacity-60 transition-all flex items-center">
                <i class="fas fa-arrow-left text-pink-500 mr-2"></i>
                <span class="font-medium text-slate-800">Back</span>
            </a>
        </div>

        @if(session('success'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 p-4 bg-red-100 text-red-800 rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</header>