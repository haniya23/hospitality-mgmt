<x-page-header 
    title="Cancelled Bookings" 
    subtitle="Manage and view all cancelled bookings" 
    icon="times-circle">
    
    <x-slot name="actions">
        <a href="{{ route('bookings.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Bookings
        </a>
    </x-slot>
    
    <x-stat-cards :cards="[
        [
            'value' => 'getStatValue(\'total\')', 
            'label' => 'Total Cancelled',
            'icon' => 'fas fa-times-circle',
            'bgGradient' => 'from-red-50 to-pink-50',
            'accentColor' => 'bg-red-500',
            'clickable' => true,
            'action' => 'filterByPeriod(\'total\')'
        ],
        [
            'value' => 'getStatValue(\'thisMonth\')', 
            'label' => 'This Month',
            'icon' => 'fas fa-calendar',
            'bgGradient' => 'from-orange-50 to-amber-50',
            'accentColor' => 'bg-orange-500',
            'clickable' => true,
            'action' => 'filterByPeriod(\'thisMonth\')'
        ],
        [
            'value' => 'getStatValue(\'thisWeek\')', 
            'label' => 'This Week',
            'icon' => 'fas fa-calendar-week',
            'bgGradient' => 'from-yellow-50 to-amber-50',
            'accentColor' => 'bg-yellow-500',
            'clickable' => true,
            'action' => 'filterByPeriod(\'thisWeek\')'
        ],
        [
            'value' => 'getStatValue(\'today\')', 
            'label' => 'Today',
            'icon' => 'fas fa-calendar-day',
            'bgGradient' => 'from-purple-50 to-indigo-50',
            'accentColor' => 'bg-purple-500',
            'clickable' => true,
            'action' => 'filterByPeriod(\'today\')'
        ]
    ]" />
</x-page-header>
