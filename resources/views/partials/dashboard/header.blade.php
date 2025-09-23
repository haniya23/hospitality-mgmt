<x-trial-banner />

@if(auth()->user()->hasPendingRequest())
    @php $pendingRequest = auth()->user()->subscriptionRequests()->where('status', 'pending')->first(); @endphp
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-2xl mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <div class="font-medium">
                    Subscription Request #{{ $pendingRequest->id }} is pending admin approval.
                </div>
                <div class="text-sm mt-1">
                    Our executives will contact you soon for confirmation.
                </div>
            </div>
        </div>
    </div>
@endif

<x-page-header 
    title="Hi, {{ auth()->user()->name ?? 'Manager' }} 👋" 
    subtitle="Here's what's happening today" 
    icon="building">
    
    <div class="flex justify-end mb-4">
        <div class="relative">
            <button class="w-10 h-10 rounded-full glass-card flex items-center justify-center">
                <i class="fas fa-bell text-pink-500"></i>
            </button>
            <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center border-2 border-white">
                <span class="text-xs font-bold text-white" x-text="notifications || 0"></span>
            </div>
        </div>
    </div>

    <x-stat-cards :cards="[
        ['value' => 'todayStats?.checkIns || 0', 'label' => 'Check-ins'],
        ['value' => 'todayStats?.newBookings || 0', 'label' => 'New Bookings'],
        ['value' => 'todayStats?.checkOuts || 0', 'label' => 'Check-outs']
    ]" />
</x-page-header>