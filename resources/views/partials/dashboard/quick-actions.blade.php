<div class="grid grid-cols-2 gap-3 mb-6">
    <a href="{{ route('bookings.index') }}" class="glassmorphism rounded-2xl p-4 text-left block hover:bg-opacity-30 transition-all">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                <i class="fas fa-calendar-plus text-white"></i>
            </div>
            <div>
                <div class="font-semibold">Bookings</div>
                <div class="text-sm opacity-75">Manage reservations</div>
            </div>
        </div>
    </a>
    <a href="{{ route('properties.index') }}" class="glassmorphism rounded-2xl p-4 text-left block hover:bg-opacity-30 transition-all">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                <i class="fas fa-home text-white"></i>
            </div>
            <div>
                <div class="font-semibold">Properties</div>
                <div class="text-sm opacity-75">View & manage</div>
            </div>
        </div>
    </a>
</div>