<div class="glassmorphism rounded-2xl p-4">
    <h3 class="font-semibold mb-3">Today's Overview</h3>
    <div class="grid grid-cols-3 gap-4 text-center">
        <div>
            <div class="text-2xl font-bold" x-text="todayStats.checkIns"></div>
            <div class="text-xs opacity-75">Check-ins</div>
        </div>
        <div>
            <div class="text-2xl font-bold" x-text="todayStats.checkOuts"></div>
            <div class="text-xs opacity-75">Check-outs</div>
        </div>
        <div>
            <div class="text-2xl font-bold" x-text="todayStats.newBookings"></div>
            <div class="text-xs opacity-75">New bookings</div>
        </div>
    </div>
</div>