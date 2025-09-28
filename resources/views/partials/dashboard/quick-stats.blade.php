<div class="grid grid-cols-2 gap-4">
    <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-purple-200" 
         @click="nextQuote()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center mb-3 shadow-sm">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
        <div class="text-sm font-medium text-gray-800 leading-tight" x-text="currentQuote.quote"></div>
        <div class="text-xs text-gray-500 mt-1" x-text="'- ' + currentQuote.author"></div>
        <div class="text-xs text-gray-500 mt-2">Click for next quote</div>
    </div>

    <div class="bg-gradient-to-br from-cyan-100 to-blue-100 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-cyan-200" 
         @click="navigateToB2bPartners()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-50 flex items-center justify-center mb-3 shadow-sm">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <div class="text-lg font-bold text-gray-800" x-text="topB2bPartner ? topB2bPartner.partner_name : 'No partners'"></div>
        <div class="text-sm text-gray-600" x-text="topB2bPartner ? topB2bPartner.reservations_count + ' bookings' : 'Top B2B Partner'"></div>
    </div>
</div>