<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ briefingTab: 'arrivals' }">
    <!-- Left Column: Operations Briefing (2/3 width on large screens) -->
    <div class="lg:col-span-2 bg-gradient-to-br from-white/95 to-purple-50/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-clipboard-list text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Daily Operations</h3>
                    <p class="text-xs text-purple-600 font-medium">Real-time briefing for today</p>
                </div>
            </div>

            <!-- Operational Tabs -->
            <div class="flex bg-gray-100 p-1 rounded-xl self-start sm:self-auto">
                <button @click="briefingTab = 'arrivals'"
                    :class="briefingTab === 'arrivals' ? 'bg-white text-purple-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                    Arrivals
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-purple-100 text-purple-800" x-text="arrivalsToday.length"></span>
                </button>
                <button @click="briefingTab = 'departures'"
                    :class="briefingTab === 'departures' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                    Departures
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-indigo-100 text-indigo-800" x-text="departuresToday.length"></span>
                </button>
                <button @click="briefingTab = 'occupied'"
                    :class="briefingTab === 'occupied' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                    In-House
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-emerald-100 text-emerald-800" x-text="occupiedRooms.length"></span>
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div>
            <!-- Tab: Arrivals -->
            <div x-show="briefingTab === 'arrivals'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
                <template x-if="arrivalsToday.length === 0">
                    <div class="p-8 text-center bg-white/40 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-plane-arrival text-gray-300 text-4xl mb-3"></i>
                        <h4 class="text-sm font-bold text-gray-800">No arrivals scheduled for today</h4>
                        <p class="text-xs text-gray-500 mt-1">All bookings for today have checked in or none exist.</p>
                    </div>
                </template>
                
                <template x-if="arrivalsToday.length > 0">
                    <div class="divide-y divide-gray-100">
                        <template x-for="booking in arrivalsToday" :key="booking.id">
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-purple-50/20 px-2 rounded-xl transition-colors duration-150">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900" x-text="booking.guest?.name"></span>
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800" x-text="booking.accommodation?.room_type || 'Room'"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-door-open text-purple-500 mr-1"></i>Room: <span class="font-semibold text-gray-800" x-text="booking.accommodation?.display_name"></span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <i class="fas fa-users text-purple-500 mr-1"></i>Guests: <span x-text="`${booking.adults} Ads, ${booking.children} Chd`"></span>
                                    </p>
                                    <div class="flex gap-4 mt-2 text-[11px] text-gray-500 font-medium">
                                        <span>Total: ₹<span class="text-gray-800" x-text="formatNumber(booking.total_amount)"></span></span>
                                        <span>Paid: ₹<span class="text-emerald-600" x-text="formatNumber(booking.advance_paid)"></span></span>
                                        <span x-show="booking.balance_pending > 0" class="text-red-500">Bal: ₹<span x-text="formatNumber(booking.balance_pending)"></span></span>
                                    </div>
                                </div>
                                <div class="self-stretch sm:self-auto flex items-center justify-end">
                                    <a :href="`/checkin/${booking.uuid}`" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl hover:shadow-lg transition-all duration-200 text-xs font-bold gap-1.5 transform hover:scale-105">
                                        <i class="fas fa-sign-in-alt text-xs"></i>
                                        Check In
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Tab: Departures -->
            <div x-show="briefingTab === 'departures'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
                <template x-if="departuresToday.length === 0">
                    <div class="p-8 text-center bg-white/40 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-plane-departure text-gray-300 text-4xl mb-3"></i>
                        <h4 class="text-sm font-bold text-gray-800">No departures scheduled for today</h4>
                        <p class="text-xs text-gray-500 mt-1">No active guests are checked out today.</p>
                    </div>
                </template>
                
                <template x-if="departuresToday.length > 0">
                    <div class="divide-y divide-gray-100">
                        <template x-for="booking in departuresToday" :key="booking.id">
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-indigo-50/20 px-2 rounded-xl transition-colors duration-150">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900" x-text="booking.guest?.name"></span>
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-indigo-100 text-indigo-800" x-text="booking.accommodation?.room_type || 'Room'"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-door-open text-indigo-500 mr-1"></i>Room: <span class="font-semibold text-gray-800" x-text="booking.accommodation?.display_name"></span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Departure: <span class="font-semibold text-gray-800" x-text="formatDate(booking.check_out_date)"></span>
                                    </p>
                                    <div class="flex gap-4 mt-2 text-[11px] text-gray-500 font-medium">
                                        <span>Total: ₹<span class="text-gray-800" x-text="formatNumber(booking.total_amount)"></span></span>
                                        <span>Paid: ₹<span class="text-emerald-600" x-text="formatNumber(booking.advance_paid)"></span></span>
                                        <span x-show="booking.balance_pending > 0" class="text-red-500 font-bold">Outstanding: ₹<span x-text="formatNumber(booking.balance_pending)"></span></span>
                                    </div>
                                </div>
                                <div class="self-stretch sm:self-auto flex items-center justify-end">
                                    <a :href="`/checkout/${booking.uuid}`" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all duration-200 text-xs font-bold gap-1.5 transform hover:scale-105">
                                        <i class="fas fa-sign-out-alt text-xs"></i>
                                        Check Out
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Tab: In-House -->
            <div x-show="briefingTab === 'occupied'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
                <template x-if="occupiedRooms.length === 0">
                    <div class="p-8 text-center bg-white/40 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-hotel text-gray-300 text-4xl mb-3"></i>
                        <h4 class="text-sm font-bold text-gray-800">No occupied rooms</h4>
                        <p class="text-xs text-gray-500 mt-1">There are currently no guests checked in.</p>
                    </div>
                </template>
                
                <template x-if="occupiedRooms.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="booking in occupiedRooms" :key="booking.id">
                            <div class="p-4 bg-white/80 rounded-xl border border-gray-100 hover:shadow-md transition-shadow flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-door-closed text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-gray-900 text-sm" x-text="booking.accommodation?.display_name"></span>
                                        <span class="px-2 py-0.5 text-[9px] uppercase font-bold tracking-wider rounded bg-green-100 text-green-800">In House</span>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-700 mt-1" x-text="booking.guest?.name"></p>
                                    <div class="mt-2 flex items-center justify-between text-[10px] text-gray-500">
                                        <span>Out: <span class="font-semibold text-gray-700" x-text="formatDate(booking.check_out_date)"></span></span>
                                        <span class="text-red-500 font-bold" x-show="booking.balance_pending > 0">Bal: ₹<span x-text="formatNumber(booking.balance_pending)"></span></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Right Column: Daily Cash Drawer Widget (1/3 width) -->
    <div class="bg-gradient-to-br from-white/95 to-emerald-50/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 bg-gradient-to-tr from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-cash-register text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Cash Drawer</h3>
                    <p class="text-xs text-emerald-600 font-medium font-bold">Today's Collections</p>
                </div>
            </div>

            <!-- Big Tally -->
            <div class="bg-white/60 rounded-2xl p-6 border border-emerald-100 text-center mb-6 shadow-sm">
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Total Collected Today</p>
                <h4 class="text-4xl font-extrabold text-emerald-700">
                    ₹<span x-text="formatNumber(dailyIncomeTotal)"></span>
                </h4>
            </div>

            <!-- Transactions list -->
            <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Today's Collections Log</h5>
            
            <div class="space-y-3 overflow-y-auto max-h-48 pr-1">
                <template x-if="dailyIncomeRecords.length === 0">
                    <div class="text-center py-6 text-gray-400 text-xs">
                        <i class="fas fa-receipt mb-2 text-lg"></i>
                        <p>No payments recorded today</p>
                    </div>
                </template>
                
                <template x-if="dailyIncomeRecords.length > 0">
                    <template x-for="record in dailyIncomeRecords" :key="record.id">
                        <div class="p-3 bg-white/80 rounded-xl border border-gray-100 hover:border-emerald-200 transition-colors flex items-center justify-between gap-3 text-xs">
                            <div class="flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-gray-800" x-text="record.accommodation?.display_name || 'Booking'"></span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                        :class="record.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                        x-text="record.payment_status === 'paid' ? 'Paid' : 'Partial'"></span>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-0.5" x-text="record.reference_number || 'N/A'"></p>
                            </div>
                            <span class="font-extrabold text-emerald-700" x-text="`₹${formatNumber(record.paid_amount)}`"></span>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <div class="pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('owner.income.index') }}" class="w-full inline-flex items-center justify-center p-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-xs gap-1.5 transform hover:scale-105 shadow-md hover:shadow-lg">
                <i class="fas fa-file-invoice-dollar"></i>
                Go to Accounting Ledger
            </a>
        </div>
    </div>
</div>
