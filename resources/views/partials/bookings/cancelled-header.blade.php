<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Cancelled Bookings</h1>
            <p class="text-gray-600">Manage and view all cancelled bookings</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Bookings
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['value' => 'total', 'label' => 'Total Cancelled', 'icon' => 'fas fa-times-circle', 'color' => 'red'],
                ['value' => 'thisMonth', 'label' => 'This Month', 'icon' => 'fas fa-calendar', 'color' => 'orange'],
                ['value' => 'thisWeek', 'label' => 'This Week', 'icon' => 'fas fa-calendar-week', 'color' => 'yellow'],
                ['value' => 'today', 'label' => 'Today', 'icon' => 'fas fa-calendar-day', 'color' => 'purple']
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="bg-gradient-to-r from-{{ $stat['color'] }}-50 to-{{ $stat['color'] }}-100 rounded-xl p-4 hover:shadow-md transition-all cursor-pointer group" 
                 @click="filterByPeriod('{{ $stat['value'] }}')">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-gray-900 group-hover:text-{{ $stat['color'] }}-600 transition-colors duration-300" 
                             x-text="getStatValue('{{ $stat['value'] }}')"></div>
                        <div class="text-sm text-gray-600 mt-1">{{ $stat['label'] }}</div>
                    </div>
                    <div class="w-12 h-12 bg-{{ $stat['color'] }}-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                        <i class="{{ $stat['icon'] }} text-lg"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
