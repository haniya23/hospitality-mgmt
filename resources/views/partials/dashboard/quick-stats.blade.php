<div class="grid grid-cols-2 gap-4">
    <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-purple-200" 
         @click="navigateToGuests()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center mb-3 shadow-sm">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="stats.totalGuests"></div>
        <div class="text-sm text-gray-600">Total Guests</div>
    </div>

    <div class="bg-gradient-to-br from-cyan-100 to-blue-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-cyan-200" 
         @click="navigateToReviews()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center mb-3 shadow-sm">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="stats.avgRating"></div>
        <div class="text-sm text-gray-600">Avg Rating</div>
    </div>
</div>