<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
    </div>
    
    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('bookings.create') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 transform hover:scale-105 border border-blue-200">
            <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center mb-3 shadow-lg group-hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800 text-center">New Booking</span>
        </a>

        <a href="{{ route('properties.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 transition-all duration-300 transform hover:scale-105 border border-green-200">
            <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center mb-3 shadow-lg group-hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800 text-center">Properties</span>
        </a>

        <a href="/b2b" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-violet-50 hover:from-purple-100 hover:to-violet-100 transition-all duration-300 transform hover:scale-105 border border-purple-200">
            <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center mb-3 shadow-lg group-hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800 text-center">B2B Partners</span>
        </a>

        <a href="{{ route('properties.index') }}#accommodations" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-orange-50 to-amber-50 hover:from-orange-100 hover:to-amber-100 transition-all duration-300 transform hover:scale-105 border border-orange-200">
            <div class="w-12 h-12 rounded-xl bg-orange-500 flex items-center justify-center mb-3 shadow-lg group-hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800 text-center">Accommodations</span>
        </a>
    </div>
</div>

