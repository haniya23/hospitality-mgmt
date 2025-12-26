<x-page-header 
    title="Completed Bookings" 
    subtitle="View all checked-out bookings and their details" 
    icon="check-circle">
    
    <x-slot name="actions">
        <a href="{{ route('bookings.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Bookings
        </a>
    </x-slot>
    
    <x-stat-cards :cards="[
        [
            'value' => 'total', 
            'label' => 'Total Completed',
            'icon' => 'fas fa-check-circle',
            'bgGradient' => 'from-green-50 to-emerald-50',
            'accentColor' => 'bg-green-500',
            'clickable' => false
        ]
    ]" />
</x-page-header>
